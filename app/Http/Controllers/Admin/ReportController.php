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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $selectedTerm = $semesterContext['term'];
        $selectedYear = $semesterContext['year'];
        $selectedTermId = $semesterContext['term_id'];
        $selectedAcademicYearId = $semesterContext['academic_year_id'];
        $activeTerm = $semesterContext['active_term'];
        $isHistorical = $semesterContext['is_historical'];
        $allTerms = ActiveSemesterService::allTermsForSelect();

        // Get summary statistics scoped to semester
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
        if ($totalStudents === 0) {
            $totalStudents = Student::count();
        }

        $resultsQuery = Result::query();
        if ($selectedTermId) {
            $resultsQuery->where('term_id', $selectedTermId);
        }
        if ($selectedAcademicYearId) {
            $resultsQuery->where('academic_year_id', $selectedAcademicYearId);
        }
        $totalResults = $resultsQuery->count();

        $feesQuery = Fee::query();
        if ($selectedTermId) {
            $feesQuery->where('term_id', $selectedTermId);
        }
        if ($selectedAcademicYearId) {
            $feesQuery->where('academic_year_id', $selectedAcademicYearId);
        }
        $totalFees = (clone $feesQuery)->sum('amount');
        $totalPaid = (clone $feesQuery)->sum('paid');
        $collectionRate = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 1) : 0;

        // Grade distribution for selected semester
        $grades = (clone $resultsQuery)
            ->selectRaw('grade, count(*) as count')
            ->groupBy('grade')
            ->get()
            ->pluck('count', 'grade')
            ->toArray();

        // Performance by programme — dynamic from student records
        $programmes = Student::distinct()->pluck('programme')->filter()->values()->toArray();
        if (empty($programmes)) {
            $programmes = [];
        }
        $programmePerformance = [];

        foreach ($programmes as $programme) {
            $studentsInProgramme = Student::where('programme', $programme)->count();
            
            $resultsInProgQuery = (clone $resultsQuery)->whereHas('student', function ($q) use ($programme) {
                $q->where('programme', $programme);
            });
            $resultsInProgramme = $resultsInProgQuery->get();

            $avgScore = $resultsInProgramme->avg('marks') ?? 0;
            $passCount = $resultsInProgramme->where('marks', '>=', 40)->count();
            $passRate = $resultsInProgramme->count() > 0 ? round(($passCount / $resultsInProgramme->count()) * 100, 1) : 0;
            $distinctionCount = $resultsInProgramme->where('marks', '>=', 70)->count();

            $programmePerformance[$programme] = [
                'students' => $studentsInProgramme,
                'avg_score' => $avgScore,
                'pass_rate' => $passRate,
                'distinctions' => $distinctionCount,
            ];
        }

        // Top performing students in this semester
        $topStudents = Student::with(['user', 'results' => function ($q) use ($selectedTermId, $selectedAcademicYearId) {
                if ($selectedTermId) $q->where('term_id', $selectedTermId);
                if ($selectedAcademicYearId) $q->where('academic_year_id', $selectedAcademicYearId);
            }])
            ->get()
            ->map(function ($student) {
                $avgScore = $student->results->avg('marks') ?? 0;
                $student->avg_score = $avgScore;
                return $student;
            })
            ->where('avg_score', '>', 0)
            ->sortByDesc('avg_score')
            ->take(5);

        // Overdue fees in this semester
        $overdueFees = (clone $feesQuery)
            ->with(['student.user'])
            ->whereRaw('paid < amount')
            ->where('due_date', '<', now())
            ->get()
            ->groupBy('student_id')
            ->map(function ($fees) {
                $total = $fees->sum('balance');
                return [
                    'student' => $fees->first()->student,
                    'total' => $total,
                    'count' => $fees->count(),
                ];
            })
            ->sortByDesc('total');

        // Funding Source Financial Report for selected semester
        $fundingSourcesList = Student::FUNDING_SOURCES;
        $fundingSourceReport = [];
        foreach ($fundingSourcesList as $src) {
            $srcFeeQuery = (clone $feesQuery)->whereHas('student', function ($q) use ($src) {
                $q->where('source_of_funding', $src);
            });
            $srcStudentsCount = (clone $studentsQuery)->where('source_of_funding', $src)->count();
            $srcExpected = (clone $srcFeeQuery)->sum('amount');
            $srcPaid = (clone $srcFeeQuery)->sum('paid');
            $srcBalance = $srcExpected - $srcPaid;
            $srcRate = $srcExpected > 0 ? round(($srcPaid / $srcExpected) * 100, 1) : 0;

            if ($srcStudentsCount > 0 || $srcExpected > 0) {
                $fundingSourceReport[] = [
                    'source' => $src,
                    'students' => $srcStudentsCount,
                    'expected_fees' => $srcExpected,
                    'paid' => $srcPaid,
                    'outstanding' => $srcBalance,
                    'collection_rate' => $srcRate,
                ];
            }
        }

        return view('admin.reports.index', compact(
            'totalStudents',
            'totalResults',
            'totalFees',
            'totalPaid',
            'collectionRate',
            'grades',
            'programmePerformance',
            'topStudents',
            'overdueFees',
            'fundingSourceReport',
            'activeTerm',
            'selectedTerm',
            'selectedYear',
            'selectedTermId',
            'selectedAcademicYearId',
            'isHistorical',
            'allTerms'
        ));
    }

    public function exportResults(Request $request): StreamedResponse
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);
        $selectedTermId = $semesterContext['term_id'];
        $selectedAcademicYearId = $semesterContext['academic_year_id'];

        $query = Result::with(['student.user', 'subject', 'term', 'academicYear']);
        if ($selectedTermId) {
            $query->where('term_id', $selectedTermId);
        }
        if ($selectedAcademicYearId) {
            $query->where('academic_year_id', $selectedAcademicYearId);
        }

        $results = $query->get();
        $filename = 'academic_results_report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($results) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Reg Number', 'Student Name', 'Programme', 'Subject', 'Exam Type', 'Academic Year', 'Term', 'Marks', 'Grade']);

            foreach ($results as $result) {
                fputcsv($file, [
                    $result->student->reg_number ?? 'N/A',
                    $result->student->user->name ?? 'N/A',
                    $result->student->programme ?? 'N/A',
                    $result->subject->name ?? 'N/A',
                    $result->exam_type == 'exam1' ? 'Exam 1' : 'Exam 2',
                    $result->academicYear->year_name ?? 'N/A',
                    $result->term->term_name ?? 'N/A',
                    $result->marks,
                    $result->grade,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    public function exportFees(Request $request): StreamedResponse
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);
        $selectedTermId = $semesterContext['term_id'];
        $selectedAcademicYearId = $semesterContext['academic_year_id'];

        $query = Fee::with(['student.user', 'academicYear', 'term']);
        if ($selectedTermId) {
            $query->where('term_id', $selectedTermId);
        }
        if ($selectedAcademicYearId) {
            $query->where('academic_year_id', $selectedAcademicYearId);
        }

        $fees = $query->get();
        $filename = 'fees_financial_report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($fees) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Reg Number', 'Student Name', 'Academic Year', 'Term', 'Fee Type', 'Amount', 'Paid', 'Balance', 'Status']);

            foreach ($fees as $fee) {
                fputcsv($file, [
                    $fee->student->reg_number ?? 'N/A',
                    $fee->student->user->name ?? 'N/A',
                    $fee->academicYear->year_name ?? 'N/A',
                    $fee->term->term_name ?? 'N/A',
                    $fee->type,
                    $fee->amount,
                    $fee->paid,
                    $fee->balance,
                    $fee->status,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    public function exportStudents(): StreamedResponse
    {
        $students = Student::with(['user', 'activeEnrollment.academicYear', 'activeEnrollment.term'])->get();
        $filename = 'students_summary_report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($students) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Reg Number', 'Name', 'Email', 'Programme', 'Active Semester', 'Phone']);

            foreach ($students as $student) {
                $enrollment = $student->activeEnrollment;
                $semesterText = $enrollment ? ($enrollment->academicYear->year_name . ' - ' . $enrollment->term->term_name) : 'Not Enrolled';

                fputcsv($file, [
                    $student->reg_number,
                    $student->user->name,
                    $student->user->email,
                    $student->programme,
                    $semesterText,
                    $student->phone ?? 'N/A',
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}