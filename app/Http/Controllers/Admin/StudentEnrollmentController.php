<?php
// app/Http/Controllers/Admin/StudentEnrollmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Term;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentEnrollmentController extends Controller
{
    /**
     * Display all enrollments
     */
    public function index(Request $request)
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTermId = $request->filled('all_semesters') ? null : ($request->filled('term') ? $request->term : $semesterContext['term_id']);
        $selectedAcademicYearId = $request->filled('all_semesters') ? null : ($request->filled('academic_year') ? $request->academic_year : $semesterContext['academic_year_id']);

        $query = StudentEnrollment::with(['student.user', 'academicYear', 'term']);

        // Default to active semester unless all_semesters requested
        if (!$request->filled('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by student name or reg number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('reg_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $enrollments = $query->latest()->paginate(15);
        $enrollments->appends($request->all());

        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $students = Student::with('user')->get();
        $activeTerm = $semesterContext['active_term'];
        $isHistorical = $semesterContext['is_historical'];

        return view('admin.enrollments.index', compact(
            'enrollments', 
            'academicYears', 
            'terms', 
            'students',
            'activeTerm',
            'selectedTermId',
            'selectedAcademicYearId',
            'isHistorical'
        ));
    }

    /**
     * Show form to enroll a student
     */
    public function create()
    {
        $students = Student::with('user')->get();
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $currentYear = ActiveSemesterService::getActiveYear();
        $currentTerm = ActiveSemesterService::getActiveTerm();

        return view('admin.enrollments.create', compact('students', 'academicYears', 'terms', 'currentYear', 'currentTerm'));
    }

    /**
     * Store a new enrollment
     */
    public function store(Request $request)
    {
        $activeYear = ActiveSemesterService::getActiveYear();
        $activeTerm = ActiveSemesterService::getActiveTerm();

        if (!$request->filled('academic_year_id') && $activeYear) {
            $request->merge(['academic_year_id' => $activeYear->id]);
        }
        if (!$request->filled('term_id') && $activeTerm) {
            $request->merge(['term_id' => $activeTerm->id]);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'programme' => 'required|string|max:255',
            'status' => 'required|in:active,graduated,suspended,withdrawn',
            'enrollment_date' => 'required|date',
            'expected_graduation_date' => 'nullable|date|after:enrollment_date',
            'notes' => 'nullable|string',
        ]);

        $term = Term::find($validated['term_id']);
        if ($term && $term->is_locked) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Cannot enroll students into locked semester '{$term->term_name}'. Unlock the semester first.");
        }

        // Check if student is already enrolled in this term/year
        $existing = StudentEnrollment::where('student_id', $validated['student_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('term_id', $validated['term_id'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Student is already enrolled in this academic year and term.');
        }

        StudentEnrollment::create($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Student enrolled successfully in active semester.');
    }

    /**
     * Show enrollment details
     */
    public function show(StudentEnrollment $enrollment)
    {
        $enrollment->load(['student.user', 'academicYear', 'term']);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    /**
     * Show form to edit enrollment
     */
    public function edit(StudentEnrollment $enrollment)
    {
        $students = Student::with('user')->get();
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'students', 'academicYears', 'terms'));
    }

    /**
     * Update enrollment
     */
    public function update(Request $request, StudentEnrollment $enrollment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'programme' => 'required|string|max:255',
            'status' => 'required|in:active,graduated,suspended,withdrawn',
            'enrollment_date' => 'required|date',
            'expected_graduation_date' => 'nullable|date|after:enrollment_date',
            'notes' => 'nullable|string',
        ]);

        $term = Term::find($validated['term_id']);
        if ($term && $term->is_locked) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Cannot update enrollment for locked semester '{$term->term_name}'.");
        }

        // Check if another enrollment exists for this student in same term/year
        $existing = StudentEnrollment::where('student_id', $validated['student_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('term_id', $validated['term_id'])
            ->where('id', '!=', $enrollment->id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Another enrollment already exists for this student in this academic year and term.');
        }

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment updated successfully.');
    }

    /**
     * Delete enrollment
     */
    public function destroy(StudentEnrollment $enrollment)
    {
        if ($enrollment->term && $enrollment->term->is_locked) {
            return redirect()->route('admin.enrollments.index')
                ->with('error', "Cannot delete enrollment from locked semester '{$enrollment->term->term_name}'.");
        }

        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }

    /**
     * Bulk enroll students
     */
    public function bulkEnroll(Request $request)
    {
        $activeYear = ActiveSemesterService::getActiveYear();
        $activeTerm = ActiveSemesterService::getActiveTerm();

        if (!$request->filled('academic_year_id') && $activeYear) {
            $request->merge(['academic_year_id' => $activeYear->id]);
        }
        if (!$request->filled('term_id') && $activeTerm) {
            $request->merge(['term_id' => $activeTerm->id]);
        }

        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'programme' => 'required|string|max:255',
            'enrollment_date' => 'required|date',
            'expected_graduation_date' => 'nullable|date|after:enrollment_date',
            'notes' => 'nullable|string',
        ]);

        $term = Term::find($validated['term_id']);
        if ($term && $term->is_locked) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Cannot bulk enroll students into locked semester '{$term->term_name}'.");
        }

        $successCount = 0;
        $failedCount = 0;
        $failedStudents = [];

        foreach ($validated['student_ids'] as $studentId) {
            $existing = StudentEnrollment::where('student_id', $studentId)
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('term_id', $validated['term_id'])
                ->first();

            if (!$existing) {
                StudentEnrollment::create([
                    'student_id' => $studentId,
                    'academic_year_id' => $validated['academic_year_id'],
                    'term_id' => $validated['term_id'],
                    'programme' => $validated['programme'],
                    'status' => 'active',
                    'enrollment_date' => $validated['enrollment_date'],
                    'expected_graduation_date' => $validated['expected_graduation_date'],
                    'notes' => $validated['notes'],
                ]);
                $successCount++;
            } else {
                $failedCount++;
                $student = Student::with('user')->find($studentId);
                $failedStudents[] = $student->user->name . ' (' . $student->reg_number . ')';
            }
        }

        $message = "Enrolled {$successCount} student(s) successfully for active semester.";
        if ($failedCount > 0) {
            $message .= " Failed: {$failedCount} student(s) already enrolled.";
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', $message)
            ->with('failedStudents', $failedStudents);
    }

    /**
     * Get students for a specific academic year and term
     */
    public function getEnrolledStudents($academicYearId, $termId)
    {
        $enrollments = StudentEnrollment::with(['student.user'])
            ->where('academic_year_id', $academicYearId)
            ->where('term_id', $termId)
            ->where('status', 'active')
            ->get();

        return response()->json($enrollments);
    }

    /**
     * Export enrollments to CSV
     */
    public function export(Request $request)
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTermId = $request->filled('all_semesters') ? null : ($request->filled('term') ? $request->term : $semesterContext['term_id']);
        $selectedAcademicYearId = $request->filled('all_semesters') ? null : ($request->filled('academic_year') ? $request->academic_year : $semesterContext['academic_year_id']);

        $query = StudentEnrollment::with(['student.user', 'academicYear', 'term']);

        if (!$request->filled('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('reg_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $enrollments = $query->latest()->get();
        $filename = 'enrollments_export_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Reg Number',
                'Student Name',
                'Programme',
                'Academic Year',
                'Term',
                'Status',
                'Enrollment Date',
                'Graduation Date'
            ]);

            foreach ($enrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->student->reg_number ?? 'N/A',
                    $enrollment->student->user->name ?? 'N/A',
                    $enrollment->programme,
                    $enrollment->academicYear->year_name ?? 'N/A',
                    $enrollment->term->term_name ?? 'N/A',
                    $enrollment->status,
                    $enrollment->enrollment_date ? $enrollment->enrollment_date->format('Y-m-d') : 'N/A',
                    $enrollment->expected_graduation_date ? $enrollment->expected_graduation_date->format('Y-m-d') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Enrollment statistics
     */
    public function statistics(Request $request)
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTermId = $semesterContext['term_id'];
        $selectedAcademicYearId = $semesterContext['academic_year_id'];

        $stats = [
            'total' => StudentEnrollment::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->count(),
            'active' => StudentEnrollment::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->where('status', 'active')->count(),
            'graduated' => StudentEnrollment::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->where('status', 'graduated')->count(),
            'suspended' => StudentEnrollment::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->where('status', 'suspended')->count(),
            'withdrawn' => StudentEnrollment::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->where('status', 'withdrawn')->count(),
        ];

        return response()->json($stats);
    }
}