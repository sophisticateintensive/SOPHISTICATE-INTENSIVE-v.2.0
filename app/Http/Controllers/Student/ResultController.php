<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Result;
use App\Models\Term;
use App\Services\ActiveSemesterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
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
        
        // Resolve semester context
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);
        
        $selectedTermId = $requestedTermId ?: ($activeEnrollment?->term_id ?? $semesterContext['term_id']);
        $selectedAcademicYearId = $request->get('academic_year') ?: ($activeEnrollment?->academic_year_id ?? $semesterContext['academic_year_id']);

        $query = $student->results()
            ->with(['subject', 'term', 'academicYear']);

        if (!$request->filled('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        $results = $query->latest()->paginate(15);
        $results->appends($request->all());

        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();
        $activeTerm = $semesterContext['active_term'];
        $isHistorical = $activeEnrollment && $selectedTermId ? ($activeEnrollment->term_id != $selectedTermId) : $semesterContext['is_historical'];

        return view('student.results.index', compact(
            'results',
            'activeEnrollment',
            'academicYears',
            'terms',
            'activeTerm',
            'selectedTermId',
            'selectedAcademicYearId',
            'isHistorical'
        ));
    }

    public function show(Result $result)
    {
        $student = Auth::user()->student;

        if ($result->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this result.');
        }

        $result->load(['subject', 'term.academicYear', 'academicYear']);

        return view('student.results.show', compact('result'));
    }

    /**
     * Download official academic transcript as PDF.
     */
    public function downloadTranscript(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $activeEnrollment = $student->activeEnrollment;
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTermId = $requestedTermId ?: ($activeEnrollment?->term_id ?? $semesterContext['term_id']);
        $selectedAcademicYearId = $request->get('academic_year') ?: ($activeEnrollment?->academic_year_id ?? $semesterContext['academic_year_id']);

        $query = $student->results()->with(['subject', 'term', 'academicYear']);

        if (!$request->boolean('all_semesters')) {
            if ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            }
            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }
        }

        $results = $query->orderBy('academic_year_id')->orderBy('term_id')->get();

        $selectedTerm = $selectedTermId ? Term::find($selectedTermId) : null;
        $selectedYear = $selectedAcademicYearId ? AcademicYear::find($selectedAcademicYearId) : null;

        $averageMarks = $results->avg('marks');
        $totalCredits = $results->sum('subject.credit_hours');
        $passedCount = $results->where('marks', '>=', 40)->count();
        $generatedAt = now()->setTimezone('Africa/Blantyre')->format('F d, Y \a\t H:i T');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.results.transcript-pdf', compact(
            'student',
            'results',
            'selectedTerm',
            'selectedYear',
            'averageMarks',
            'totalCredits',
            'passedCount',
            'generatedAt'
        ))->setPaper('a4', 'portrait');

        $filename = 'Official_Transcript_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $student->reg_number) . '.pdf';

        return $pdf->download($filename);
    }
}
