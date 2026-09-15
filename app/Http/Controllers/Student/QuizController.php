<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $requestedTermId = $request->query('term_id');
        $context = ActiveSemesterService::resolve($requestedTermId);
        $selectedTermId = $context['term_id'];
        $activeTerm = $context['active_term'];
        $isHistorical = $context['is_historical'];
        $allTerms = ActiveSemesterService::allTermsForSelect();
        $activeEnrollment = $student->activeEnrollment;

        // Get enrolled subject IDs for student if any exist in pivot table
        $enrolledSubjectIds = $student->subjects()->pluck('subjects.id')->toArray();

        // Query available quizzes
        $quizzesQuery = Quiz::with(['subject', 'academicYear', 'term', 'questions'])
            ->where('is_active', true);

        // If the student has specific subjects assigned in the pivot table, filter to those + general quizzes.
        // If the student has no specific subjects assigned in the pivot table, show all active quizzes.
        if (!empty($enrolledSubjectIds)) {
            $quizzesQuery->where(function ($q) use ($enrolledSubjectIds) {
                $q->whereIn('subject_id', $enrolledSubjectIds)
                  ->orWhereNull('subject_id');
            });
        }

        // Filter by semester unless student requested 'all_semesters'
        if ($selectedTermId && !$request->has('all_semesters')) {
            $quizzesQuery->where(function ($q) use ($selectedTermId) {
                $q->where('term_id', $selectedTermId)
                  ->orWhereNull('term_id');
            });
        }

        $availableQuizzes = $quizzesQuery->latest()->get();

        foreach ($availableQuizzes as $quiz) {
            $quiz->attempts_count = $quiz->attemptsCountForStudent($student->id);
            $quiz->remaining_attempts = $quiz->remainingAttemptsForStudent($student->id);
            $quiz->can_take = $quiz->canStudentAttempt($student->id);
            $quiz->active_attempt = $quiz->getActiveAttempt($student->id);
            $quiz->best_score = $quiz->bestAttempt($student->id)?->score;
        }

        $pastAttempts = QuizAttempt::with(['quiz.subject'])
            ->where('student_id', $student->id)
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->get();

        return view('student.quizzes.index', compact(
            'availableQuizzes',
            'pastAttempts',
            'activeTerm',
            'selectedTermId',
            'isHistorical',
            'allTerms',
            'activeEnrollment'
        ));
    }

    public function take($id)
    {
        $student = Auth::user()->student;
        $quiz = Quiz::with(['questions.options', 'subject'])->findOrFail($id);

        if (!$quiz->is_active) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'This assessment is currently inactive.');
        }

        if (!$quiz->isAvailable()) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'This quiz is not available at this time.');
        }

        $attempt = $quiz->getActiveAttempt($student->id);

        // Check if previous attempt expired
        if ($attempt && $attempt->isExpired()) {
            $this->autoExpireAttempt($attempt);
            return redirect()->route('student.quizzes.result', $attempt->id)
                ->with('error', 'Time limit expired. Your assessment has been finalized.');
        }

        // Start new attempt if allowed
        if (!$attempt) {
            if (!$quiz->canStudentAttempt($student->id)) {
                return redirect()->route('student.quizzes.index')
                    ->with('error', 'You have exhausted the maximum allowed attempts for this quiz.');
            }

            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $student->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        $questionsQuery = $quiz->questions()->with('options');

        if ($quiz->questions_per_attempt && $quiz->questions_per_attempt > 0) {
            // Seeded random selection from the question pool for this attempt
            $questions = $questionsQuery->inRandomOrder($attempt->id)
                ->limit($quiz->questions_per_attempt)
                ->get();
        } elseif ($quiz->shuffle_questions) {
            // Consistent randomized order based on attempt ID seed
            $questions = $questionsQuery->inRandomOrder($attempt->id)->get();
        } else {
            $questions = $questionsQuery->orderBy('order')->get();
        }

        // Shuffle options if requested
        if ($quiz->shuffle_options) {
            foreach ($questions as $question) {
                $question->setRelation('options', $question->options->shuffle());
            }
        }

        $savedAnswers = $attempt->answers()->get()->keyBy('question_id');
        $remainingSeconds = $attempt->getRemainingSeconds();

        return view('student.quizzes.take', compact(
            'quiz',
            'attempt',
            'questions',
            'savedAnswers',
            'remainingSeconds'
        ));
    }

    public function autosave(Request $request, $id)
    {
        $student = Auth::user()->student;
        $quiz = Quiz::findOrFail($id);
        $attempt = $quiz->getActiveAttempt($student->id);

        if (!$attempt) {
            return response()->json(['success' => false, 'message' => 'No active session found.'], 404);
        }

        if ($attempt->isExpired()) {
            return response()->json(['success' => false, 'expired' => true, 'message' => 'Time has expired.'], 403);
        }

        $questionId = $request->input('question_id');
        $selectedOptionId = $request->input('selected_option_id');
        $textAnswer = $request->input('text_answer');

        if ($questionId) {
            $question = $quiz->questions()->find($questionId);
            if ($question) {
                if ($question->type === 'essay' || $question->type === 'short_answer') {
                    QuizAnswer::updateOrCreate(
                        ['attempt_id' => $attempt->id, 'question_id' => $questionId],
                        [
                            'text_answer' => $textAnswer,
                            'is_graded' => false,
                            'points_earned' => 0,
                        ]
                    );
                } else {
                    $option = $question->options()->find($selectedOptionId);
                    $isCorrect = $option ? (bool)$option->is_correct : false;
                    $points = $isCorrect ? $question->points : 0;

                    QuizAnswer::updateOrCreate(
                        ['attempt_id' => $attempt->id, 'question_id' => $questionId],
                        [
                            'selected_option_id' => $selectedOptionId,
                            'is_correct' => $isCorrect,
                            'is_graded' => true,
                            'points_earned' => $points,
                        ]
                    );
                }
            }
        }

        return response()->json([
            'success' => true,
            'saved_at' => now()->toTimeString(),
        ]);
    }

    public function submit(Request $request, $id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        $student = Auth::user()->student;
        $attempt = $quiz->getActiveAttempt($student->id);

        if (!$attempt) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'No active attempt found to submit.');
        }

        DB::beginTransaction();
        try {
            $answers = $request->input('answers', []);
            $textAnswers = $request->input('text_answers', []);
            $totalPointsEarned = 0;
            $hasUngradedEssay = false;

            $attemptQuestions = $quiz->getQuestionsForAttempt($attempt);
            $totalPossiblePoints = $attemptQuestions->sum('points');
            if ($totalPossiblePoints <= 0) {
                $totalPossiblePoints = $quiz->total_points > 0 ? $quiz->total_points : 1;
            }

            foreach ($attemptQuestions as $question) {
                if ($question->type === 'essay' || $question->type === 'short_answer') {
                    $submittedText = $textAnswers[$question->id] ?? null;
                    QuizAnswer::updateOrCreate(
                        ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                        [
                            'text_answer' => $submittedText,
                            'is_graded' => false,
                            'points_earned' => 0,
                        ]
                    );
                    $hasUngradedEssay = true;
                } else {
                    $selectedOptionId = $answers[$question->id] ?? null;
                    $isCorrect = false;
                    $pointsEarned = 0;

                    if ($selectedOptionId) {
                        $selectedOption = $question->options()->find($selectedOptionId);
                        if ($selectedOption && $selectedOption->is_correct) {
                            $isCorrect = true;
                            $pointsEarned = $question->points;
                            $totalPointsEarned += $pointsEarned;
                        }
                    }

                    QuizAnswer::updateOrCreate(
                        ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                        [
                            'selected_option_id' => $selectedOptionId,
                            'is_correct' => $isCorrect,
                            'is_graded' => true,
                            'points_earned' => $pointsEarned,
                        ]
                    );
                }
            }

            $percentageScore = round(($totalPointsEarned / $totalPossiblePoints) * 100, 2);

            // Check if submission was triggered by timer expiration
            $isTimeExpired = $request->boolean('time_expired') || $attempt->isExpired();
            $status = $isTimeExpired ? 'expired' : 'submitted';

            $timeLimitSeconds = $quiz->time_limit_minutes ? $quiz->time_limit_minutes * 60 : 0;
            $timeTakenSeconds = $attempt->started_at ? now()->diffInSeconds($attempt->started_at) : 0;
            if ($isTimeExpired && $timeLimitSeconds > 0) {
                $timeTakenSeconds = $timeLimitSeconds;
            }

            $attempt->update([
                'submitted_at' => now(),
                'time_taken_seconds' => $timeTakenSeconds,
                'total_points_earned' => $totalPointsEarned,
                'score' => $percentageScore,
                'is_passed' => $percentageScore >= $quiz->passing_score,
                'needs_manual_grading' => $hasUngradedEssay,
                'status' => $status,
            ]);

            DB::commit();

            $message = $isTimeExpired
                ? 'Time limit reached. Your assessment has been automatically finalized and scored.'
                : ($hasUngradedEssay 
                    ? 'Assessment submitted successfully! Written responses will be graded by the lecturer.'
                    : 'Assessment completed and submitted successfully!');

            return redirect()->route('student.quizzes.result', $attempt->id)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit quiz: ' . $e->getMessage());
        }
    }

    public function result($id)
    {
        $student = Auth::user()->student;
        $attempt = QuizAttempt::with(['quiz.questions.options', 'quiz.subject', 'answers.selectedOption'])
            ->findOrFail($id);

        if ($attempt->student_id !== $student->id) {
            abort(403, 'Unauthorized access to assessment result.');
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->getQuestionsForAttempt($attempt);
        $answers = $attempt->answers->keyBy('question_id');
        $remainingAttempts = $quiz->remainingAttemptsForStudent($student->id);

        return view('student.quizzes.result', compact(
            'attempt',
            'quiz',
            'questions',
            'answers',
            'remainingAttempts'
        ));
    }

    protected function autoExpireAttempt(QuizAttempt $attempt)
    {
        $quiz = $attempt->quiz;
        $attemptQuestions = $quiz->getQuestionsForAttempt($attempt);
        $totalPossiblePoints = $attemptQuestions->sum('points');
        if ($totalPossiblePoints <= 0) {
            $totalPossiblePoints = $quiz->total_points > 0 ? $quiz->total_points : 1;
        }

        $totalPointsEarned = $attempt->answers()->where('is_correct', true)->sum('points_earned');
        $percentageScore = round(($totalPointsEarned / $totalPossiblePoints) * 100, 2);

        $timeLimitSeconds = $quiz->time_limit_minutes ? $quiz->time_limit_minutes * 60 : 0;

        $attempt->update([
            'submitted_at' => now(),
            'time_taken_seconds' => $timeLimitSeconds,
            'total_points_earned' => $totalPointsEarned,
            'score' => $percentageScore,
            'is_passed' => $percentageScore >= $quiz->passing_score,
            'status' => 'expired',
        ]);
    }
}
