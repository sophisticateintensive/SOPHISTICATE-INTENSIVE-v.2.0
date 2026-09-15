<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Fee;
use App\Models\Term;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        if (! $student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $activeEnrollment = $student->activeEnrollment;
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTermId = $requestedTermId ?: ($activeEnrollment?->term_id ?? $semesterContext['term_id']);
        $selectedAcademicYearId = $request->get('academic_year') ?: ($activeEnrollment?->academic_year_id ?? $semesterContext['academic_year_id']);

        $query = $student->fees()->with(['academicYear', 'term']);

        if (!$request->filled('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        // Totals query for accurate summaries
        $totalsQuery = clone $query;
        $totalFees = $totalsQuery->sum('amount');
        $totalPaid = $totalsQuery->sum('paid');
        $totalBalance = $totalFees - $totalPaid;
        
        $allFees = $totalsQuery->get();
        $overdueCount = $allFees->filter(fn($fee) => $fee->is_overdue)->count();
        $paymentCount = $allFees->where('paid', '>', 0)->count();

        $fees = $query->latest()->paginate(15);
        $fees->appends($request->all());

        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $activeTerm = $semesterContext['active_term'];
        $isHistorical = $activeEnrollment && $selectedTermId ? ($activeEnrollment->term_id != $selectedTermId) : $semesterContext['is_historical'];

        return view('student.fees.index', compact(
            'fees',
            'activeEnrollment',
            'totalFees',
            'totalPaid',
            'totalBalance',
            'overdueCount',
            'paymentCount',
            'academicYears',
            'terms',
            'activeTerm',
            'selectedTermId',
            'selectedAcademicYearId',
            'isHistorical'
        ));
    }
}
