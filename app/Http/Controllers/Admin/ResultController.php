<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $hasCustomFilter = $request->filled('term_id') || $request->filled('term') || $request->filled('academic_year') || $request->filled('all_semesters');
        
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);
        $selectedTermId = $request->filled('all_semesters') ? null : ($request->filled('term') ? $request->term : $semesterContext['term_id']);
        $selectedAcademicYearId = $request->filled('all_semesters') ? null : ($request->filled('academic_year') ? $request->academic_year : $semesterContext['academic_year_id']);

        $query = Result::with(['student.user', 'subject', 'term', 'academicYear']);

        // Default to active semester unless user explicitly requested all_semesters or a different filter
        if (!$request->filled('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        // Filter by exam type
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('subject')) {
            $query->where('subject_id', $request->subject);
        }

        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('reg_number', 'LIKE', '%' . $request->student . '%')
                    ->orWhereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('name', 'LIKE', '%' . $request->student . '%');
                    });
            });
        }

        $results = $query->latest()->paginate(15);
        $results->appends($request->all());

        // Get data for filters
        $academicYears = AcademicYear::all();
        $terms         = Term::with('academicYear')->get();
        $subjects      = Subject::all();
        $activeTerm    = $semesterContext['active_term'];
        $isHistorical  = $semesterContext['is_historical'];

        return view('admin.results.index', compact(
            'results',
            'academicYears',
            'terms',
            'subjects',
            'activeTerm',
            'selectedTermId',
            'selectedAcademicYearId',
            'isHistorical'
        ));
    }

    /**
     * Export results to CSV/Excel
     */
    public function export(Request $request): StreamedResponse
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);
        $selectedTermId = $request->filled('all_semesters') ? null : ($request->filled('term') ? $request->term : $semesterContext['term_id']);
        $selectedAcademicYearId = $request->filled('all_semesters') ? null : ($request->filled('academic_year') ? $request->academic_year : $semesterContext['academic_year_id']);

        // Build query with filters
        $query = Result::with(['student.user', 'subject', 'term', 'academicYear']);

        if (!$request->filled('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('subject')) {
            $query->where('subject_id', $request->subject);
        }

        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('reg_number', 'LIKE', '%' . $request->student . '%')
                    ->orWhereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('name', 'LIKE', '%' . $request->student . '%');
                    });
            });
        }

        $results = $query->latest()->get();
        $filename = 'results_export_' . now()->format('Y-m-d_His');

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, [
                'Reg Number',
                'Student Name',
                'Email',
                'Programme',
                'Subject Code',
                'Subject Name',
                'Exam Type',
                'Academic Year',
                'Term',
                'Marks',
                'Grade',
                'Date Recorded',
            ]);

            foreach ($results as $result) {
                fputcsv($file, [
                    $result->student->reg_number ?? 'N/A',
                    $result->student->user->name ?? 'N/A',
                    $result->student->user->email ?? 'N/A',
                    $result->student->programme ?? 'N/A',
                    $result->subject->code ?? 'N/A',
                    $result->subject->name ?? 'N/A',
                    $result->exam_type == 'exam1' ? 'Exam 1' : 'Exam 2',
                    $result->academicYear->year_name ?? 'N/A',
                    $result->term->term_name ?? 'N/A',
                    number_format($result->marks, 2),
                    $result->grade,
                    $result->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentYear   = ActiveSemesterService::getActiveYear();
        $currentTerm   = ActiveSemesterService::getActiveTerm();
        $activeTerm    = $currentTerm;

        $academicYears = AcademicYear::all();
        $terms         = Term::with('academicYear')->get();
        $subjects      = Subject::all();
        $students      = Student::with('user')->get();

        return view('admin.results.create', compact('currentYear', 'currentTerm', 'activeTerm', 'academicYears', 'terms', 'subjects', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $activeYear = ActiveSemesterService::getActiveYear();
        $activeTerm = ActiveSemesterService::getActiveTerm();

        // Default to active semester if not supplied
        if (!$request->filled('academic_year_id') && $activeYear) {
            $request->merge(['academic_year_id' => $activeYear->id]);
        }
        if (!$request->filled('term_id') && $activeTerm) {
            $request->merge(['term_id' => $activeTerm->id]);
        }

        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'subject_id'       => 'required|exists:subjects,id',
            'exam_type'        => 'required|in:exam1,exam2',
            'term_id'          => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'marks'            => 'required|numeric|min:0|max:100',
            'grade'            => 'nullable|string|max:2',
        ]);

        // Check if selected term is locked
        $term = Term::find($validated['term_id']);
        if ($term && $term->is_locked) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Cannot record results for locked semester '{$term->term_name}'. Unlock the semester first.");
        }

        // Auto-calculate grade if not provided
        if (empty($validated['grade'])) {
            $validated['grade'] = $this->calculateGrade($validated['marks']);
        }

        // Check if result already exists for this specific exam type
        $existingResult = Result::where('student_id', $validated['student_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('term_id', $validated['term_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('exam_type', $validated['exam_type'])
            ->first();

        if ($existingResult) {
            $examTypeDisplay = $validated['exam_type'] == 'exam1' ? 'Exam 1' : 'Exam 2';
            return redirect()->back()
                ->withInput()
                ->with('error', "A result for {$examTypeDisplay} already exists for this student in this subject. Current marks: {$existingResult->marks}% (Grade: {$existingResult->grade})");
        }

        try {
            Result::create($validated);
            
            $examTypeDisplay = $validated['exam_type'] == 'exam1' ? 'Exam 1' : 'Exam 2';
            return redirect()->route('admin.results.index')
                ->with('success', "{$examTypeDisplay} result recorded successfully for active semester.");
        } catch (\Exception $e) {
            \Log::error('Error creating result: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while saving the result. Please check that this exam type is not already recorded.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Result $result)
    {
        $result->load(['student.user', 'subject', 'term.academicYear', 'academicYear']);

        return view('admin.results.show', compact('result'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Result $result)
    {
        // Guard locked term
        if ($result->term && $result->term->is_locked) {
            return redirect()->route('admin.results.index')
                ->with('error', "Cannot edit result from locked semester '{$result->term->term_name}'. Unlock the semester to make edits.");
        }

        $students      = Student::with('user')->get();
        $subjects      = Subject::all();
        $terms         = Term::with('academicYear')->get();
        $academicYears = AcademicYear::all();

        return view('admin.results.edit', compact('result', 'students', 'subjects', 'terms', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Result $result)
    {
        // Guard locked term
        if ($result->term && $result->term->is_locked) {
            return redirect()->route('admin.results.index')
                ->with('error', "Cannot update result from locked semester '{$result->term->term_name}'.");
        }

        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'subject_id'       => 'required|exists:subjects,id',
            'exam_type'        => 'required|in:exam1,exam2',
            'term_id'          => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'marks'            => 'required|numeric|min:0|max:100',
            'grade'            => 'nullable|string|max:2',
        ]);

        if (empty($validated['grade'])) {
            $validated['grade'] = $this->calculateGrade($validated['marks']);
        }

        $existingResult = Result::where('student_id', $validated['student_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('term_id', $validated['term_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('exam_type', $validated['exam_type'])
            ->where('id', '!=', $result->id)
            ->first();

        if ($existingResult) {
            $examTypeDisplay = $validated['exam_type'] == 'exam1' ? 'Exam 1' : 'Exam 2';
            return redirect()->back()
                ->withInput()
                ->with('error', "Another result for {$examTypeDisplay} already exists for this student, subject, and semester combination.");
        }

        $result->update($validated);

        return redirect()->route('admin.results.index')
            ->with('success', 'Result updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Result $result)
    {
        // Guard locked term
        if ($result->term && $result->term->is_locked) {
            return redirect()->route('admin.results.index')
                ->with('error', "Cannot delete result from locked semester '{$result->term->term_name}'.");
        }

        $result->delete();

        return redirect()->route('admin.results.index')
            ->with('success', 'Result deleted successfully.');
    }

    /**
     * Bulk delete results
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'result_ids' => 'required|array',
            'result_ids.*' => 'exists:results,id',
        ]);

        $results = Result::with('term')->whereIn('id', $validated['result_ids'])->get();

        $lockedCount = 0;
        $deletedCount = 0;

        foreach ($results as $result) {
            if ($result->term && $result->term->is_locked) {
                $lockedCount++;
            } else {
                $result->delete();
                $deletedCount++;
            }
        }

        $message = "Deleted {$deletedCount} results.";
        if ($lockedCount > 0) {
            $message .= " Skipped {$lockedCount} results from locked semesters.";
        }

        return redirect()->route('admin.results.index')->with('success', $message);
    }

    /**
     * Get results for a specific student (API endpoint)
     */
    public function getStudentResults($studentId, Request $request)
    {
        $query = Result::with(['subject', 'term', 'academicYear'])
            ->where('student_id', $studentId);

        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        return response()->json($query->get());
    }

    /**
     * Get subjects assigned to a student for results entry
     */
    public function getStudentSubjects($studentId, Request $request)
    {
        $student = Student::with('subjects')->findOrFail($studentId);
        $termId = $request->get('term_id') ?? ActiveSemesterService::getActiveTerm()?->id;

        $subjects = $student->subjects()
            ->when($termId, function ($q) use ($termId) {
                $q->wherePivot('term_id', $termId);
            })
            ->get();

        // If no subjects found for specific term, return all enrolled subjects
        if ($subjects->isEmpty()) {
            $subjects = $student->subjects;
        }

        return response()->json($subjects);
    }

    /**
     * Check if result already exists (AJAX)
     */
    public function checkExistingResult(Request $request)
    {
        $exists = Result::where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('exam_type', $request->exam_type)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Calculate grade from marks
     */
    private function calculateGrade($marks)
    {
        if ($marks >= 80) return 'A';
        if ($marks >= 70) return 'B';
        if ($marks >= 60) return 'C';
        if ($marks >= 50) return 'D';
        return 'F';
    }
}
