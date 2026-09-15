<?php
// app/Http/Controllers/Admin/StudentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of students with filters.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'activeEnrollment.academicYear', 'activeEnrollment.term']);

        // Apply search filter (searches across ALL students)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reg_number', 'LIKE', "%{$search}%")
                  ->orWhere('programme', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('parent_name', 'LIKE', "%{$search}%")
                  ->orWhere('parent_phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('academic_year')) {
            $query->whereHas('activeEnrollment', function($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year);
            });
        }

        if ($request->filled('term')) {
            $query->whereHas('activeEnrollment', function($q) use ($request) {
                $q->where('term_id', $request->term);
            });
        }

        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
        }

        $students = $query->latest()->paginate(10);
        
        // Preserve filters in pagination
        $students->appends($request->all());

        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $programmes = Student::distinct()->pluck('programme');

        return view('admin.students.index', compact('students', 'academicYears', 'terms', 'programmes'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();

        return view('admin.students.create', compact('academicYears', 'terms'));
    }

    /**
     * Get the next available registration number (DB-checked).
     */
    public function nextRegNumber(): \Illuminate\Http\JsonResponse
    {
        $last = Student::where('reg_number', 'like', 'SOP%')
            ->orderByRaw('CAST(SUBSTRING(reg_number, 4) AS UNSIGNED) DESC')
            ->value('reg_number');

        if ($last) {
            $next = intval(substr($last, 3)) + 1;
        } else {
            $next = 1; // First student ever → SOP001
        }

        $regNumber = 'SOP' . str_pad($next, 3, '0', STR_PAD_LEFT);

        return response()->json(['reg_number' => $regNumber]);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                     => 'required|string|max:255',
            'email'                    => 'required|email|unique:users,email',
            'password'                 => 'required|string|min:8|confirmed',
            'reg_number'               => 'required|string|unique:students,reg_number',
            'programme'                => 'required|string|max:255',
            'source_of_funding'        => 'required|in:' . implode(',', Student::FUNDING_SOURCES),
            'funding_source_other'     => 'nullable|string|max:255',
            'phone'                    => 'nullable|string|max:50',
            'parent_name'              => 'nullable|string|max:255',
            'parent_phone'             => 'nullable|string|max:50',
            'address'                  => 'nullable|string|max:500',
            'emergency_contact'        => 'nullable|string|max:255',
            'academic_year_id'         => 'required|exists:academic_years,id',
            'term_id'                  => 'required|exists:terms,id',
            'enrollment_date'          => 'required|date',
            'expected_graduation_date' => 'nullable|date|after:enrollment_date',
            'notes'                    => 'nullable|string',
        ]);

        // Create user account first
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'student',
        ]);

        // Create student profile linked to the user
        $student = Student::create([
            'user_id'              => $user->id,
            'reg_number'           => $validated['reg_number'],
            'programme'            => $validated['programme'],
            'source_of_funding'    => $validated['source_of_funding'],
            'funding_source_other' => $validated['funding_source_other'] ?? null,
            'phone'                => $validated['phone'] ?? null,
            'parent_name'          => $validated['parent_name'] ?? null,
            'parent_phone'         => $validated['parent_phone'] ?? null,
            'address'              => $validated['address'] ?? null,
            'emergency_contact'    => $validated['emergency_contact'] ?? null,
        ]);

        // Create enrollment record
        StudentEnrollment::create([
            'student_id'               => $student->id,
            'academic_year_id'         => $validated['academic_year_id'],
            'term_id'                  => $validated['term_id'],
            'programme'                => $validated['programme'],
            'status'                   => 'active',
            'enrollment_date'          => $validated['enrollment_date'],
            'expected_graduation_date' => $validated['expected_graduation_date'],
            'notes'                    => $validated['notes'],
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created and enrolled successfully with contact details.');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'enrollments.academicYear', 'enrollments.term', 'subjects']);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $student->load(['activeEnrollment']);
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();

        return view('admin.students.edit', compact('student', 'academicYears', 'terms'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'                     => 'required|string|max:255',
            'email'                    => 'required|email|unique:users,email,' . $student->user_id,
            'reg_number'               => 'required|string|unique:students,reg_number,' . $student->id,
            'programme'                => 'required|string|max:255',
            'source_of_funding'        => 'required|in:' . implode(',', Student::FUNDING_SOURCES),
            'funding_source_other'     => 'nullable|string|max:255',
            'phone'                    => 'nullable|string|max:50',
            'parent_name'              => 'nullable|string|max:255',
            'parent_phone'             => 'nullable|string|max:50',
            'address'                  => 'nullable|string|max:500',
            'emergency_contact'        => 'nullable|string|max:255',
            'academic_year_id'         => 'required|exists:academic_years,id',
            'term_id'                  => 'required|exists:terms,id',
            'status'                   => 'required|in:active,graduated,suspended,withdrawn',
            'enrollment_date'          => 'required|date',
            'expected_graduation_date' => 'nullable|date|after:enrollment_date',
            'notes'                    => 'nullable|string',
            'manual_password'          => 'nullable|string|min:8',
        ]);

        // Update user information
        $userData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];
        
        // Update password if provided
        if (!empty($validated['manual_password'])) {
            $userData['password'] = Hash::make($validated['manual_password']);
        }
        
        $student->user->update($userData);

        // Update student information
        $student->update([
            'reg_number'           => $validated['reg_number'],
            'programme'            => $validated['programme'],
            'source_of_funding'    => $validated['source_of_funding'],
            'funding_source_other' => $validated['funding_source_other'] ?? null,
            'phone'                => $validated['phone'] ?? null,
            'parent_name'          => $validated['parent_name'] ?? null,
            'parent_phone'         => $validated['parent_phone'] ?? null,
            'address'              => $validated['address'] ?? null,
            'emergency_contact'    => $validated['emergency_contact'] ?? null,
        ]);

        // Update or create enrollment
        $activeEnrollment = $student->activeEnrollment;

        if ($activeEnrollment) {
            $activeEnrollment->update([
                'academic_year_id'         => $validated['academic_year_id'],
                'term_id'                  => $validated['term_id'],
                'programme'                => $validated['programme'],
                'status'                   => $validated['status'],
                'enrollment_date'          => $validated['enrollment_date'],
                'expected_graduation_date' => $validated['expected_graduation_date'],
                'notes'                    => $validated['notes'],
            ]);
        } else {
            StudentEnrollment::create([
                'student_id'               => $student->id,
                'academic_year_id'         => $validated['academic_year_id'],
                'term_id'                  => $validated['term_id'],
                'programme'                => $validated['programme'],
                'status'                   => $validated['status'],
                'enrollment_date'          => $validated['enrollment_date'],
                'expected_graduation_date' => $validated['expected_graduation_date'],
                'notes'                    => $validated['notes'],
            ]);
        }

        // Add success message with password reset notification if applicable
        $message = 'Student updated successfully.';
        if (!empty($validated['manual_password'])) {
            $message = 'Student updated and password reset successfully.';
        }

        return redirect()->route('admin.students.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        $student->enrollments()->delete();
        $student->user->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Enroll a student in a new academic year/term.
     */
    public function enrollStudent(Request $request, Student $student)
    {
        $validated = $request->validate([
            'academic_year_id'         => 'required|exists:academic_years,id',
            'term_id'                  => 'required|exists:terms,id',
            'programme'                => 'required|string|max:255',
            'enrollment_date'          => 'required|date',
            'expected_graduation_date' => 'nullable|date|after:enrollment_date',
            'notes'                    => 'nullable|string',
        ]);

        $existingEnrollment = $student->enrollmentFor($validated['academic_year_id'], $validated['term_id']);

        if ($existingEnrollment) {
            return redirect()->back()
                ->with('error', 'Student is already enrolled in this academic year and term.');
        }

        StudentEnrollment::create([
            'student_id'               => $student->id,
            'academic_year_id'         => $validated['academic_year_id'],
            'term_id'                  => $validated['term_id'],
            'programme'                => $validated['programme'],
            'status'                   => 'active',
            'enrollment_date'          => $validated['enrollment_date'],
            'expected_graduation_date' => $validated['expected_graduation_date'],
            'notes'                    => $validated['notes'],
        ]);

        return redirect()->back()->with('success', 'Student enrolled successfully.');
    }

    /**
     * Export students to CSV.
     */
    public function export(Request $request)
    {
        $query = Student::with(['user', 'activeEnrollment.academicYear', 'activeEnrollment.term']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reg_number', 'LIKE', "%{$search}%")
                  ->orWhere('programme', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('parent_name', 'LIKE', "%{$search}%")
                  ->orWhere('parent_phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('academic_year')) {
            $query->whereHas('activeEnrollment', function($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year);
            });
        }

        if ($request->filled('term')) {
            $query->whereHas('activeEnrollment', function($q) use ($request) {
                $q->where('term_id', $request->term);
            });
        }

        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
        }

        $students = $query->get();

        // Generate CSV
        $filename = 'students_export_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($file, [
                'Reg Number',
                'Name',
                'Email',
                'Phone',
                'Parent / Guardian Name',
                'Parent Phone',
                'Physical Address',
                'Emergency Contact',
                'Programme',
                'Academic Year',
                'Term',
                'Status'
            ]);
            
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->reg_number,
                    $student->user->name,
                    $student->user->email,
                    $student->phone ?? 'N/A',
                    $student->parent_name ?? 'N/A',
                    $student->parent_phone ?? 'N/A',
                    $student->address ?? 'N/A',
                    $student->emergency_contact ?? 'N/A',
                    $student->programme,
                    $student->activeEnrollment?->academicYear?->year_name ?? 'N/A',
                    $student->activeEnrollment?->term?->term_name ?? 'N/A',
                    $student->activeEnrollment?->status ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Reset student password.
     */
    public function resetPassword(Request $request, Student $student)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $student->user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully.');
    }

    /**
     * Toggle student account active / deactivated status.
     */
    public function toggleAccountStatus(Student $student)
    {
        $current = $student->user->is_active ?? true;
        $student->user->update(['is_active' => !$current]);

        $label = !$current ? 'activated' : 'deactivated';

        return back()->with('success', "Student account has been {$label} successfully.");
    }
}
