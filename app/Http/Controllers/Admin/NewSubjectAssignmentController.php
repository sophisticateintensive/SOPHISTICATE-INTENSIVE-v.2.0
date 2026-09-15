<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewSubjectAssignmentController extends Controller
{
    /**
     * Display a listing of all students with their subjects.
     */
    public function index()
    {
        $students = Student::with(['user', 'subjects' => function ($query) {
            $query->withPivot('status', 'academic_year_id', 'term_id', 'enrolled_date', 'completion_date', 'notes');
        }])->get();

        $subjects      = Subject::all();
        $academicYears = AcademicYear::all();
        $terms         = Term::with('academicYear')->get();
        $activeTerm    = Term::where('is_current', true)->first();
        
        // Get unique programmes for filter dropdown
        $programmes = Student::distinct()->pluck('programme')->filter()->values();

        $totalStudents = $students->count();
        $totalCourses = $subjects->count();
        $totalEnrollments = $students->sum(function ($s) { return $s->subjects->count(); });
        $noCourses = $students->filter(function ($s) { return $s->subjects->isEmpty(); })->count();

        return view('admin.new-subject-assignment.index', compact(
            'students', 'subjects', 'academicYears', 'terms', 'activeTerm', 'programmes',
            'totalStudents', 'totalCourses', 'totalEnrollments', 'noCourses'
        ));
    }

    /**
     * Show the form for managing a specific student's subjects.
     */
    public function show(Student $student)
    {
        // Load the student with all necessary relationships
        $student->load(['user', 'subjects' => function ($query) {
            $query->withPivot('status', 'academic_year_id', 'term_id', 'enrolled_date', 'completion_date', 'notes');
        }]);

        // Double-check that user is loaded
        if (! $student->relationLoaded('user')) {
            $student->load('user');
        }

        $assignedSubjectIds = $student->subjects->pluck('id')->toArray();
        $availableSubjects  = Subject::whereNotIn('id', $assignedSubjectIds)->get();
        $academicYears      = AcademicYear::all();
        $terms              = Term::with('academicYear')->get();
        $activeTerm         = Term::where('is_current', true)->first();

        return view('admin.new-subject-assignment.show', compact('student', 'availableSubjects', 'academicYears', 'terms', 'activeTerm'));
    }

    /**
     * Assign subjects to a student.
     */
    public function assign(Request $request, Student $student)
    {
        $request->validate([
            'subject_ids'      => 'required|array',
            'subject_ids.*'    => 'exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id'          => 'required|exists:terms,id',
            'notes'            => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $subjectsToAttach = [];
            foreach ($request->subject_ids as $subjectId) {
                if (! $student->subjects()->where('subject_id', $subjectId)->exists()) {
                    $subjectsToAttach[$subjectId] = [
                        'academic_year_id' => $request->academic_year_id,
                        'term_id'          => $request->term_id,
                        'status'           => 'enrolled',
                        'enrolled_date'    => now(),
                        'notes'            => $request->notes,
                    ];
                }
            }

            if (! empty($subjectsToAttach)) {
                $student->subjects()->attach($subjectsToAttach);
            }

            DB::commit();

            $count   = count($subjectsToAttach);
            $message = $count > 0
                ? $count . ' subject(s) assigned successfully.'
                : 'No new subjects were assigned (they may already be assigned).';

            return redirect()->route('admin.new-subject-assignment.show', $student)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to assign subjects. Please try again.');
        }
    }

    /**
     * Export filtered students to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = Student::with(['user', 'subjects']);

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })->orWhere('reg_number', 'like', "%{$search}%")
                      ->orWhere('programme', 'like', "%{$search}%");
                });
            }

            // Programme filter
            if ($request->filled('programme')) {
                $query->where('programme', $request->programme);
            }

            // Subject/Course filter
            if ($request->filled('subject')) {
                $query->whereHas('subjects', function ($q) use ($request) {
                    $q->where('subject_id', $request->subject);
                });
            }

            $students = $query->get();

            $filename = 'students_course_assignment_' . date('Y-m-d_His') . '.csv';
            
            $handle = fopen('php://temp', 'w+');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            
            // Headers
            fputcsv($handle, [
                'S/N', 
                'Student Name', 
                'Registration Number', 
                'Programme', 
                'Assigned Courses', 
                'Course Codes', 
                'Total Credit Hours',
                'Enrolled Count',
                'Completed Count',
                'Dropped Count'
            ]);
            
            $sn = 1;
            foreach ($students as $student) {
                $courseNames = $student->subjects->pluck('name')->join(', ');
                $courseCodes = $student->subjects->pluck('code')->join(', ');
                $totalCredits = $student->subjects->sum('credit_hours');
                $enrolledCount = $student->subjects->where('pivot.status', 'enrolled')->count();
                $completedCount = $student->subjects->where('pivot.status', 'completed')->count();
                $droppedCount = $student->subjects->where('pivot.status', 'dropped')->count();
                
                fputcsv($handle, [
                    $sn++,
                    $student->user->name ?? 'Unknown',
                    $student->reg_number ?? 'N/A',
                    $student->programme ?? 'N/A',
                    $courseNames ?: 'No courses assigned',
                    $courseCodes ?: 'N/A',
                    $totalCredits,
                    $enrolledCount,
                    $completedCount,
                    $droppedCount,
                ]);
            }
            
            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);
            
            return response($csvContent, 200)
                ->header('Content-Type', 'text/csv; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of a subject for a student.
     */
    public function updateStatus(Request $request, Student $student, Subject $subject)
    {
        $request->validate([
            'status' => 'required|in:enrolled,completed,dropped',
            'notes'  => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $pivotData = [
                'status' => $request->status,
                'notes'  => $request->notes,
            ];

            if ($request->status === 'completed') {
                $pivotData['completion_date'] = now();
            }

            if ($request->status === 'enrolled') {
                $pivotData['enrolled_date'] = now();
            }

            $student->subjects()->updateExistingPivot($subject->id, $pivotData);

            DB::commit();

            return redirect()->back()->with('success', 'Subject status updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update subject status. Please try again.');
        }
    }

    /**
     * Remove a subject from a student.
     */
    public function remove(Student $student, Subject $subject)
    {
        try {
            DB::beginTransaction();

            $hasResults = $student->results()
                ->where('subject_id', $subject->id)
                ->exists();

            if ($hasResults) {
                return redirect()->back()->with('error', 'Cannot remove subject because results exist.');
            }

            $student->subjects()->detach($subject->id);

            DB::commit();

            return redirect()->back()->with('success', 'Subject removed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to remove subject. Please try again.');
        }
    }

    /**
     * Bulk assign subjects to multiple students.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'student_ids'      => 'required|array',
            'student_ids.*'    => 'exists:students,id',
            'subject_ids'      => 'required|array',
            'subject_ids.*'    => 'exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id'          => 'required|exists:terms,id',
        ]);

        try {
            DB::beginTransaction();

            $students      = Student::whereIn('id', $request->student_ids)->get();
            $assignedCount = 0;

            foreach ($students as $student) {
                foreach ($request->subject_ids as $subjectId) {
                    if (! $student->subjects()->where('subject_id', $subjectId)->exists()) {
                        $student->subjects()->attach($subjectId, [
                            'academic_year_id' => $request->academic_year_id,
                            'term_id'          => $request->term_id,
                            'status'           => 'enrolled',
                            'enrolled_date'    => now(),
                        ]);
                        $assignedCount++;
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.new-subject-assignment.index')
                ->with('success', $assignedCount . ' subject assignments created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to bulk assign subjects. Please try again.');
        }
    }

    /**
     * Get student's subject summary (for AJAX)
     */
    public function getSummary(Student $student)
    {
        $student->load(['subjects' => function ($query) {
            $query->withPivot('status');
        }]);

        $summary = [
            'total_subjects' => $student->subjects->count(),
            'enrolled'       => $student->subjects->where('pivot.status', 'enrolled')->count(),
            'completed'      => $student->subjects->where('pivot.status', 'completed')->count(),
            'dropped'        => $student->subjects->where('pivot.status', 'dropped')->count(),
            'credit_hours'   => $student->subjects->sum('credit_hours'),
        ];

        return response()->json($summary);
    }

    /**
     * Get available subjects for a student (for AJAX)
     */
    public function getAvailableSubjects(Student $student)
    {
        $assignedSubjectIds = $student->subjects()->pluck('subject_id')->toArray();
        $availableSubjects  = Subject::whereNotIn('id', $assignedSubjectIds)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'credit_hours']);

        return response()->json($availableSubjects);
    }

    /**
     * Quick assign a single subject to a student (for AJAX)
     */
    public function quickAssign(Request $request, Student $student)
    {
        $request->validate([
            'subject_id'       => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id'          => 'required|exists:terms,id',
        ]);

        try {
            if ($student->subjects()->where('subject_id', $request->subject_id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject already assigned to this student.',
                ], 422);
            }

            $student->subjects()->attach($request->subject_id, [
                'academic_year_id' => $request->academic_year_id,
                'term_id'          => $request->term_id,
                'status'           => 'enrolled',
                'enrolled_date'    => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subject assigned successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign subject.',
            ], 500);
        }
    }

    /**
     * Create bulk assignment form (if needed)
     */
    public function createBulk()
    {
        $students = Student::with('user')->get();
        $subjects = Subject::all();
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $activeTerm = Term::where('is_current', true)->first();

        return view('admin.new-subject-assignment.bulk', compact('students', 'subjects', 'academicYears', 'terms', 'activeTerm'));
    }
}
