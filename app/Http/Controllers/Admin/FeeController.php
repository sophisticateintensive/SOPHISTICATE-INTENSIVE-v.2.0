<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Fee;
use App\Models\Student;
use App\Models\Term;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeeController extends Controller
{
    /**
     * Display a listing of the fees.
     */
    public function index(Request $request)
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTermId = $request->filled('all_semesters') ? null : ($request->filled('term') ? $request->term : $semesterContext['term_id']);
        $selectedAcademicYearId = $request->filled('all_semesters') ? null : ($request->filled('academic_year') ? $request->academic_year : $semesterContext['academic_year_id']);

        $query = Fee::with(['student.user', 'academicYear', 'term']);

        // Default to active semester unless all_semesters requested
        if (!$request->filled('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'paid') {
                $query->whereRaw('paid >= amount');
            } elseif ($request->status == 'partial') {
                $query->whereRaw('paid > 0 AND paid < amount');
            } elseif ($request->status == 'overdue') {
                $query->whereRaw('paid < amount')
                    ->where('due_date', '<', now());
            }
        }

        // Filter by source of funding
        if ($request->filled('funding_source')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('source_of_funding', $request->funding_source);
            });
        }

        // Filter by programme
        if ($request->filled('programme')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('programme', $request->programme);
            });
        }

        // Search by student name or registration number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('reg_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // ===== Calculate totals from the FULL filtered query =====
        $totalsQuery = clone $query;
        
        $totalFeesAll = $totalsQuery->sum('amount');
        $totalPaidAll = $totalsQuery->sum('paid');
        $totalBalanceAll = $totalFeesAll - $totalPaidAll;
        
        $allFees = $totalsQuery->get();
        $overdueCountAll = $allFees->filter(function ($fee) {
            return $fee->is_overdue;
        })->count();
        
        $paidCountAll = $allFees->filter(function ($fee) {
            return $fee->is_fully_paid;
        })->count();
        
        $partialCountAll = $allFees->filter(function ($fee) {
            return !$fee->is_fully_paid && !$fee->is_overdue && $fee->balance > 0;
        })->count();

        // ===== Compute Funding Source Breakdown Analytics =====
        $fundingSourcesList = Student::FUNDING_SOURCES;
        $fundingBreakdown = [];
        foreach ($fundingSourcesList as $src) {
            $srcQuery = Fee::query()->whereHas('student', function ($q) use ($src) {
                $q->where('source_of_funding', $src);
            });

            if (!$request->filled('all_semesters')) {
                if ($selectedAcademicYearId) {
                    $srcQuery->where('academic_year_id', $selectedAcademicYearId);
                }
                if ($selectedTermId) {
                    $srcQuery->where('term_id', $selectedTermId);
                }
            }

            $srcFees = (clone $srcQuery)->sum('amount');
            $srcPaid = (clone $srcQuery)->sum('paid');
            $srcBalance = $srcFees - $srcPaid;
            $srcStudentCount = Student::where('source_of_funding', $src)->count();

            if ($srcStudentCount > 0 || $srcFees > 0) {
                $completionRate = $srcFees > 0 ? round(($srcPaid / $srcFees) * 100, 1) : 0;
                $fundingBreakdown[] = [
                    'source' => $src,
                    'students_count' => $srcStudentCount,
                    'expected_fees' => $srcFees,
                    'paid_fees' => $srcPaid,
                    'balance' => $srcBalance,
                    'completion_rate' => $completionRate,
                ];
            }
        }

        // Paginate for display
        $fees = $query->latest()->paginate(15);
        $fees->appends($request->all());

        // Get data for filters
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $activeTerm = $semesterContext['active_term'];
        $isHistorical = $semesterContext['is_historical'];
        $programmes = Student::distinct()->pluck('programme')->filter()->values();
        $selectedFundingSource = $request->get('funding_source');
        $selectedProgramme = $request->get('programme');

        return view('admin.fees.index', compact(
            'fees', 
            'academicYears', 
            'terms',
            'activeTerm',
            'selectedTermId',
            'selectedAcademicYearId',
            'isHistorical',
            'totalFeesAll',
            'totalPaidAll',
            'totalBalanceAll',
            'overdueCountAll',
            'paidCountAll',
            'partialCountAll',
            'fundingBreakdown',
            'fundingSourcesList',
            'programmes',
            'selectedFundingSource',
            'selectedProgramme'
        ));
    }

    /**
     * Show the form for creating a new fee.
     */
    public function create()
    {
        $currentYear = ActiveSemesterService::getActiveYear();
        $currentTerm = ActiveSemesterService::getActiveTerm();

        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $students = Student::with('user')->get();

        return view('admin.fees.create', compact('academicYears', 'terms', 'students', 'currentYear', 'currentTerm'));
    }

    /**
     * Store a newly created fee.
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
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'paid' => 'numeric|min:0|nullable',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if (!isset($validated['paid'])) {
            $validated['paid'] = 0;
        }

        $term = Term::find($validated['term_id']);
        if ($term && $term->is_locked) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Cannot create fee for locked semester '{$term->term_name}'. Unlock the semester first.");
        }

        Fee::create($validated);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee invoice generated successfully for active semester.');
    }

    /**
     * Display the specified fee.
     */
    public function show(Fee $fee)
    {
        $fee->load(['student.user', 'academicYear', 'term']);
        return view('admin.fees.show', compact('fee'));
    }

    /**
     * Show the form for editing the specified fee.
     */
    public function edit(Fee $fee)
    {
        if ($fee->term && $fee->term->is_locked) {
            return redirect()->route('admin.fees.index')
                ->with('error', "Cannot edit fee for locked semester '{$fee->term->term_name}'.");
        }

        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $students = Student::with('user')->get();

        return view('admin.fees.edit', compact('fee', 'academicYears', 'terms', 'students'));
    }

    /**
     * Update the specified fee.
     */
    public function update(Request $request, Fee $fee)
    {
        if ($fee->term && $fee->term->is_locked) {
            return redirect()->route('admin.fees.index')
                ->with('error', "Cannot update fee for locked semester '{$fee->term->term_name}'.");
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'paid' => 'numeric|min:0|nullable',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if (!isset($validated['paid'])) {
            $validated['paid'] = $fee->paid;
        }

        $fee->update($validated);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee updated successfully.');
    }

    /**
     * Remove the specified fee.
     */
    public function destroy(Fee $fee)
    {
        if ($fee->term && $fee->term->is_locked) {
            return redirect()->route('admin.fees.index')
                ->with('error', "Cannot delete fee from locked semester '{$fee->term->term_name}'.");
        }

        $fee->delete();

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee deleted successfully.');
    }

    /**
     * Record a payment for a fee.
     */
    public function recordPayment(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01|max:' . $fee->balance,
            'notes' => 'nullable|string',
        ]);

        $fee->paid += $validated['payment_amount'];
        $fee->updateStatus();

        return redirect()->route('admin.fees.show', $fee)
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Export fees to CSV
     */
    public function export(Request $request)
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTermId = $request->filled('all_semesters') ? null : ($request->filled('term') ? $request->term : $semesterContext['term_id']);
        $selectedAcademicYearId = $request->filled('all_semesters') ? null : ($request->filled('academic_year') ? $request->academic_year : $semesterContext['academic_year_id']);

        $query = Fee::with(['student.user', 'academicYear', 'term']);

        if (!$request->filled('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
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

        $fees = $query->latest()->get();
        $filename = 'fees_export_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($fees) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Fee Type',
                'Academic Year',
                'Term',
                'Amount',
                'Paid',
                'Balance',
                'Due Date',
                'Status'
            ]);

            foreach ($fees as $fee) {
                fputcsv($file, [
                    $fee->student->reg_number ?? 'N/A',
                    $fee->student->user->name ?? 'N/A',
                    $fee->type,
                    $fee->academicYear->year_name ?? 'N/A',
                    $fee->term->term_name ?? 'N/A',
                    $fee->amount,
                    $fee->paid,
                    $fee->balance,
                    $fee->due_date ? $fee->due_date->format('Y-m-d') : 'N/A',
                    $fee->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
