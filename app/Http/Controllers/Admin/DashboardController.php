<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTerm = $semesterContext['term'];
        $selectedYear = $semesterContext['year'];
        $selectedTermId = $semesterContext['term_id'];
        $selectedAcademicYearId = $semesterContext['academic_year_id'];
        $isHistorical = $semesterContext['is_historical'];

        // Filter metrics strictly by selected semester
        $studentsQuery = Student::query();
        if ($selectedTermId) {
            $studentsQuery->whereHas('enrollments', function ($q) use ($selectedTermId, $selectedAcademicYearId) {
                $q->where('term_id', $selectedTermId);
                if ($selectedAcademicYearId) {
                    $q->where('academic_year_id', $selectedAcademicYearId);
                }
            });
        }
        $totalStudents = $studentsQuery->count();

        // If no enrollments exist yet for this semester, fall back to total students count for display
        $overallTotalStudents = Student::count();

        $totalSubjects = Subject::count();

        $resultsQuery = Result::query();
        if ($selectedTermId) {
            $resultsQuery->where('term_id', $selectedTermId);
        }
        if ($selectedAcademicYearId) {
            $resultsQuery->where('academic_year_id', $selectedAcademicYearId);
        }
        $totalResults = $resultsQuery->count();

        $recentResults = (clone $resultsQuery)
            ->with(['student.user', 'subject', 'term', 'academicYear'])
            ->latest()
            ->take(5)
            ->get();

        $resultsToday = (clone $resultsQuery)->whereDate('created_at', today())->count();

        // Fees for selected semester
        $feesQuery = Fee::query();
        if ($selectedTermId) {
            $feesQuery->where('term_id', $selectedTermId);
        }
        if ($selectedAcademicYearId) {
            $feesQuery->where('academic_year_id', $selectedAcademicYearId);
        }

        $overdueFees = (clone $feesQuery)->whereRaw('paid < amount')->where('due_date', '<', now())->count();
        $totalFeesInvoiced = (clone $feesQuery)->sum('amount');
        $totalFeesCollected = (clone $feesQuery)->sum('paid');
        $totalFeesBalance = $totalFeesInvoiced - $totalFeesCollected;

        $activeTerm = $semesterContext['active_term'];
        $allTerms = ActiveSemesterService::allTermsForSelect();

        return view('admin.dashboard.index', compact(
            'totalStudents',
            'overallTotalStudents',
            'totalSubjects',
            'totalResults',
            'activeTerm',
            'selectedTerm',
            'selectedYear',
            'selectedTermId',
            'selectedAcademicYearId',
            'isHistorical',
            'allTerms',
            'recentResults',
            'resultsToday',
            'overdueFees',
            'totalFeesInvoiced',
            'totalFeesCollected',
            'totalFeesBalance'
        ));
    }
}