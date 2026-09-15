<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\Option;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $context = ActiveSemesterService::resolve($request->query('term_id'));
        $selectedTermId = $context['term_id'];
        $activeTerm = $context['active_term'];
        $isHistorical = $context['is_historical'];

        $query = Quiz::with(['subject', 'academicYear', 'term', 'questions', 'attempts'])
            ->withCount(['questions', 'attempts']);

        // Semester filter
        if ($selectedTermId && !$request->has('all_semesters')) {
            $query->where('term_id', $selectedTermId);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            });
        }

        // Subject filter
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $quizzes = $query->latest()->paginate(12)->withQueryString();
        $subjects = Subject::orderBy('code')->get();
        $terms = ActiveSemesterService::allTermsForSelect();

        return view('admin.quizzes.index', compact(
            'quizzes',
            'subjects',
            'terms',
            'selectedTermId',
            'activeTerm',
            'isHistorical'
        ));
    }

    public function create()
    {
        $subjects = Subject::orderBy('code')->get();
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $activeTerm = ActiveSemesterService::getActiveTerm();
        $activeYear = ActiveSemesterService::getActiveYear();

        return view('admin.quizzes.create', compact('subjects', 'academicYears', 'terms', 'activeTerm', 'activeYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'term_id' => 'nullable|exists:terms,id',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1|max:20',
            'questions_per_attempt' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1|max:360',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            'is_active' => 'nullable|boolean',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['show_correct_answers'] = $request->boolean('show_correct_answers', true);

        // Auto-assign active semester if not provided
        if (empty($validated['term_id'])) {
            $validated['term_id'] = ActiveSemesterService::getActiveTerm()?->id;
        }
        if (empty($validated['academic_year_id'])) {
            $validated['academic_year_id'] = ActiveSemesterService::getActiveYear()?->id;
        }

        DB::beginTransaction();
        try {
            $quiz = Quiz::create($validated);

            // Handle dynamic questions if passed directly during creation
            if ($request->has('questions_data') && is_array($request->questions_data)) {
                $this->syncDynamicQuestions($quiz, $request->questions_data);
            }

            DB::commit();

            return redirect()->route('admin.quizzes.edit', $quiz)
                ->with('success', 'Quiz created successfully. You can now configure questions and options.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create quiz: ' . $e->getMessage());
        }
    }

    public function edit(Quiz $quiz)
    {
        $subjects = Subject::orderBy('code')->get();
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $questions = $quiz->questions()->with('options')->get();

        return view('admin.quizzes.edit', compact('quiz', 'subjects', 'academicYears', 'terms', 'questions'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'term_id' => 'nullable|exists:terms,id',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1|max:20',
            'questions_per_attempt' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1|max:360',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['show_correct_answers'] = $request->boolean('show_correct_answers', true);

        DB::beginTransaction();
        try {
            $quiz->update($validated);

            if ($request->has('questions_data') && is_array($request->questions_data)) {
                $this->syncDynamicQuestions($quiz, $request->questions_data);
            }

            // Recalculate total points
            $quiz->update(['total_points' => $quiz->questions()->sum('points')]);

            DB::commit();

            return redirect()->route('admin.quizzes.edit', $quiz)
                ->with('success', 'Quiz settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update quiz: ' . $e->getMessage());
        }
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz and all associated questions & student attempts deleted.');
    }

    public function toggleStatus(Quiz $quiz)
    {
        $quiz->update(['is_active' => !$quiz->is_active]);
        $status = $quiz->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Quiz is now {$status}.");
    }

    public function duplicate(Quiz $quiz)
    {
        DB::beginTransaction();
        try {
            $cloned = $quiz->replicate();
            $cloned->title = $quiz->title . ' (Copy)';
            $cloned->is_active = false;
            $cloned->save();

            foreach ($quiz->questions as $question) {
                $clonedQuestion = $question->replicate();
                $clonedQuestion->quiz_id = $cloned->id;
                $clonedQuestion->save();

                foreach ($question->options as $option) {
                    $clonedOption = $option->replicate();
                    $clonedOption->question_id = $clonedQuestion->id;
                    $clonedOption->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.quizzes.edit', $cloned)
                ->with('success', 'Quiz successfully duplicated as a draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to duplicate quiz: ' . $e->getMessage());
        }
    }

    // Question Management
    public function addQuestion(Quiz $quiz)
    {
        return view('admin.quizzes.add-question', compact('quiz'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $type = $request->input('type', 'multiple_choice');

        if ($type === 'true_false') {
            $validated = $request->validate([
                'question_text' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'required|integer|min:1',
                'correct_tf' => 'required|in:true,false',
            ]);

            $question = $quiz->questions()->create([
                'type' => 'true_false',
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
                'order' => $quiz->questions()->count() + 1,
            ]);

            $question->options()->create([
                'option_text' => 'True',
                'is_correct' => $validated['correct_tf'] === 'true',
                'order' => 1,
            ]);
            $question->options()->create([
                'option_text' => 'False',
                'is_correct' => $validated['correct_tf'] === 'false',
                'order' => 2,
            ]);
        } elseif ($type === 'essay' || $type === 'short_answer') {
            $validated = $request->validate([
                'question_text' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'required|integer|min:1',
            ]);

            $question = $quiz->questions()->create([
                'type' => 'essay',
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
                'order' => $quiz->questions()->count() + 1,
            ]);
        } else {
            $validated = $request->validate([
                'question_text' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'required|integer|min:1',
                'options' => 'required|array|min:2|max:6',
                'options.*' => 'required|string',
                'correct_option' => 'required|integer|min:0|max:5',
            ]);

            $question = $quiz->questions()->create([
                'type' => 'multiple_choice',
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
                'order' => $quiz->questions()->count() + 1,
            ]);

            foreach ($validated['options'] as $index => $optionText) {
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => $index == $validated['correct_option'],
                    'order' => $index + 1,
                ]);
            }
        }

        $quiz->update(['total_points' => $quiz->questions()->sum('points')]);

        return redirect()->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Question added successfully.');
    }

    public function editQuestion(Quiz $quiz, Question $question)
    {
        return view('admin.quizzes.edit-question', compact('quiz', 'question'));
    }

    public function updateQuestion(Request $request, Quiz $quiz, Question $question)
    {
        $type = $request->input('type', $question->type ?? 'multiple_choice');

        if ($type === 'true_false') {
            $validated = $request->validate([
                'question_text' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'required|integer|min:1',
                'correct_tf' => 'required|in:true,false',
            ]);

            $question->update([
                'type' => 'true_false',
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
            ]);

            $question->options()->delete();

            $question->options()->create([
                'option_text' => 'True',
                'is_correct' => $validated['correct_tf'] === 'true',
                'order' => 1,
            ]);
            $question->options()->create([
                'option_text' => 'False',
                'is_correct' => $validated['correct_tf'] === 'false',
                'order' => 2,
            ]);
        } elseif ($type === 'essay' || $type === 'short_answer') {
            $validated = $request->validate([
                'question_text' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'required|integer|min:1',
            ]);

            $question->update([
                'type' => 'essay',
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
            ]);

            $question->options()->delete();
        } else {
            $validated = $request->validate([
                'question_text' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'required|integer|min:1',
                'options' => 'required|array|min:2|max:6',
                'options.*' => 'required|string',
                'correct_option' => 'required|integer|min:0|max:5',
            ]);

            $question->update([
                'type' => 'multiple_choice',
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
            ]);

            $question->options()->delete();

            foreach ($validated['options'] as $index => $optionText) {
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => $index == $validated['correct_option'],
                    'order' => $index + 1,
                ]);
            }
        }

        $quiz->update(['total_points' => $quiz->questions()->sum('points')]);

        return redirect()->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(Quiz $quiz, Question $question)
    {
        $question->delete();
        $quiz->update(['total_points' => $quiz->questions()->sum('points')]);

        return redirect()->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Question deleted successfully.');
    }

    // Attempts & Analytics
    public function attempts(Quiz $quiz)
    {
        $attempts = $quiz->attempts()
            ->with(['student.user', 'answers'])
            ->latest()
            ->paginate(20);

        $totalSubmissions = $quiz->attempts()->where('status', 'submitted')->count();
        $passedCount = $quiz->attempts()->where('status', 'submitted')->where('is_passed', true)->count();
        $passRate = $totalSubmissions > 0 ? round(($passedCount / $totalSubmissions) * 100, 1) : 0;
        $avgScore = round($quiz->attempts()->where('status', 'submitted')->avg('score') ?? 0, 1);
        $highestScore = round($quiz->attempts()->where('status', 'submitted')->max('score') ?? 0, 1);

        return view('admin.quizzes.attempts', compact(
            'quiz',
            'attempts',
            'totalSubmissions',
            'passedCount',
            'passRate',
            'avgScore',
            'highestScore'
        ));
    }

    public function showAttempt(QuizAttempt $attempt)
    {
        $attempt->load(['quiz.questions.options', 'student.user', 'answers.selectedOption']);
        $quiz = $attempt->quiz;
        $questions = $quiz->getQuestionsForAttempt($attempt);
        $answers = $attempt->answers->keyBy('question_id');

        return view('admin.quizzes.show-attempt', compact('attempt', 'quiz', 'questions', 'answers'));
    }

    public function gradeQuestion(Request $request, QuizAttempt $attempt, Question $question)
    {
        $validated = $request->validate([
            'points_earned' => 'required|integer|min:0|max:' . $question->points,
            'grader_feedback' => 'nullable|string|max:1000',
        ]);

        $answer = $attempt->answers()->where('question_id', $question->id)->first();
        if (!$answer) {
            $answer = $attempt->answers()->create([
                'question_id' => $question->id,
            ]);
        }

        $answer->update([
            'points_earned' => $validated['points_earned'],
            'is_correct' => $validated['points_earned'] > 0,
            'is_graded' => true,
            'grader_feedback' => $validated['grader_feedback'] ?? null,
        ]);

        // Recalculate attempt score & manual grading status
        $totalEarned = $attempt->answers()->sum('points_earned');
        $attemptQuestions = $attempt->quiz->getQuestionsForAttempt($attempt);
        $totalPossible = $attemptQuestions->sum('points');
        if ($totalPossible <= 0) {
            $totalPossible = $attempt->quiz->total_points > 0 ? $attempt->quiz->total_points : 1;
        }

        $score = round(($totalEarned / $totalPossible) * 100, 2);
        $isPassed = $score >= ($attempt->quiz->passing_score ?? 50);

        // Check if there are still any ungraded questions among this attempt's questions
        $hasUngraded = $attempt->answers()->where('is_graded', false)->exists();

        $attempt->update([
            'total_points_earned' => $totalEarned,
            'score' => $score,
            'is_passed' => $isPassed,
            'needs_manual_grading' => $hasUngraded,
        ]);

        return back()->with('success', "Question graded successfully. Points awarded: {$validated['points_earned']}/{$question->points}.");
    }

    public function resetAttempt(QuizAttempt $attempt)
    {
        $quiz = $attempt->quiz;
        $studentName = $attempt->student->user->name ?? 'Student';
        $attempt->delete();

        return back()->with('success', "Attempt for {$studentName} has been cleared. The student can now take the assessment again.");
    }

    public function exportAttempts(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->with(['student.user'])->get();
        $filename = 'quiz_results_' . \Str::slug($quiz->title) . '_' . date('Y-m-d') . '.csv';

        $handle = fopen('php://temp', 'w+');
        fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

        fputcsv($handle, [
            'S/N',
            'Student Name',
            'Registration Number',
            'Points Earned',
            'Total Points',
            'Score (%)',
            'Status',
            'Grade',
            'Time Taken (seconds)',
            'Submitted At'
        ]);

        $sn = 1;
        foreach ($attempts as $attempt) {
            fputcsv($handle, [
                $sn++,
                $attempt->student->user->name ?? 'N/A',
                $attempt->student->reg_number ?? 'N/A',
                $attempt->total_points_earned,
                $quiz->total_points,
                $attempt->score . '%',
                $attempt->is_passed ? 'PASSED' : 'FAILED',
                $attempt->grade_letter,
                $attempt->time_taken_seconds,
                $attempt->submitted_at?->format('Y-m-d H:i:s') ?? 'In Progress'
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    protected function syncDynamicQuestions(Quiz $quiz, array $questionsData)
    {
        // Helper to batch process dynamic questions if submitted via form builder
        foreach ($questionsData as $idx => $qData) {
            if (empty($qData['question_text'])) continue;

            $question = $quiz->questions()->create([
                'type' => $qData['type'] ?? 'multiple_choice',
                'question_text' => $qData['question_text'],
                'explanation' => $qData['explanation'] ?? null,
                'points' => (int)($qData['points'] ?? 1),
                'order' => $idx + 1,
            ]);

            if (isset($qData['options']) && is_array($qData['options'])) {
                foreach ($qData['options'] as $oIdx => $opt) {
                    $question->options()->create([
                        'option_text' => $opt['text'] ?? "Option " . ($oIdx + 1),
                        'is_correct' => !empty($opt['is_correct']),
                        'order' => $oIdx + 1,
                    ]);
                }
            }
        }
    }
}
