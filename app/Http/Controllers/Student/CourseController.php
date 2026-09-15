<?php
// app/Http/Controllers/Student/CourseController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Eagerly load active enrollment with its relationships
        $activeEnrollment = $student->activeEnrollment()
            ->with(['academicYear', 'term'])
            ->first();

        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        // Always default to active enrollment year/term unless user explicitly filters
        $academicYearId = $request->filled('academic_year')
            ? $request->academic_year
            : ($requestedTermId ? $semesterContext['academic_year_id'] : $activeEnrollment?->academic_year_id);

        $termId = $requestedTermId ?: ($activeEnrollment?->term_id ?? $semesterContext['term_id']);

        $status = $request->filled('status') ? $request->status : null;

        // Build subjects query — always include all pivot columns
        $query = $student->subjects()
            ->withPivot('academic_year_id', 'term_id', 'status', 'enrolled_date', 'completion_date', 'notes');

        if (!$request->filled('all_semesters')) {
            if ($academicYearId) {
                $query->where('student_subject.academic_year_id', $academicYearId);
            }
            if ($termId) {
                $query->where('student_subject.term_id', $termId);
            }
        }

        if ($status) {
            $query->where('student_subject.status', $status);
        }

        $subjects = $query->orderBy('code')->paginate(15);
        $subjects->appends($request->all());

        // Summary stats scoped to active or selected enrollment
        $totalSubjects    = $subjects->total();
        $totalCreditHours = $subjects->sum('credit_hours');

        $activeSubjects = $student->subjects()
            ->withPivot('academic_year_id', 'term_id', 'status')
            ->when($academicYearId, fn($q) => $q->where('student_subject.academic_year_id', $academicYearId))
            ->when($termId, fn($q) => $q->where('student_subject.term_id', $termId))
            ->get();

        $enrolledCount  = $activeSubjects->filter(fn($s) => $s->pivot->status === 'enrolled')->count();
        $completedCount = $activeSubjects->filter(fn($s) => $s->pivot->status === 'completed')->count();
        $droppedCount   = $activeSubjects->filter(fn($s) => $s->pivot->status === 'dropped')->count();

        $academicYears = AcademicYear::orderBy('id', 'desc')->get();
        $terms         = Term::with('academicYear')->get();
        $activeTerm    = $semesterContext['active_term'];
        $isHistorical  = $activeEnrollment && $termId ? ($activeEnrollment->term_id != $termId) : $semesterContext['is_historical'];

        return view('student.courses.index', compact(
            'subjects',
            'activeEnrollment',
            'totalSubjects',
            'totalCreditHours',
            'enrolledCount',
            'completedCount',
            'droppedCount',
            'academicYears',
            'terms',
            'academicYearId',
            'termId',
            'activeTerm',
            'isHistorical'
        ));
    }

    public function show($id)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $subject = $student->subjects()
            ->withPivot('academic_year_id', 'term_id', 'status', 'enrolled_date', 'completion_date', 'notes')
            ->where('subjects.id', $id)
            ->firstOrFail();

        $result = $student->results()
            ->where('subject_id', $id)
            ->with(['term', 'academicYear'])
            ->first();

        return view('student.courses.show', compact('subject', 'result'));
    }

    public function export(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $activeEnrollment = $student->activeEnrollment()->first();
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $academicYearId = $request->filled('academic_year')
            ? $request->academic_year
            : ($requestedTermId ? $semesterContext['academic_year_id'] : $activeEnrollment?->academic_year_id);

        $termId = $requestedTermId ?: ($activeEnrollment?->term_id ?? $semesterContext['term_id']);

        $query = $student->subjects()
            ->withPivot('academic_year_id', 'term_id', 'status', 'enrolled_date');

        if (!$request->filled('all_semesters')) {
            if ($academicYearId) {
                $query->where('student_subject.academic_year_id', $academicYearId);
            }
            if ($termId) {
                $query->where('student_subject.term_id', $termId);
            }
        }
        if ($request->filled('status')) {
            $query->where('student_subject.status', $request->status);
        }

        $subjects = $query->orderBy('code')->get();
        $filename = 'my_subjects_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($subjects) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Subject Code', 'Subject Name', 'Credit Hours', 'Academic Year', 'Term', 'Status', 'Enrollment Date']);

            foreach ($subjects as $subject) {
                fputcsv($file, [
                    $subject->code,
                    $subject->name,
                    $subject->credit_hours,
                    AcademicYear::find($subject->pivot->academic_year_id)?->year_name ?? 'N/A',
                    Term::find($subject->pivot->term_id)?->term_name                  ?? 'N/A',
                    ucfirst($subject->pivot->status),
                    $subject->pivot->enrolled_date
                        ? \Carbon\Carbon::parse($subject->pivot->enrolled_date)->format('M d, Y')
                        : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}