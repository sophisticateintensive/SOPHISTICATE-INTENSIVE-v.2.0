<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Services\ActiveSemesterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{
    /**
     * Display the student's personal timetable for the selected week and term.
     */
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(403, 'Student profile not found.');
        }

        // Load active enrollment once
        $activeEnrollment = $student->activeEnrollment()->first();

        $requestedTermId = $request->get('term_id');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);

        $termId         = $requestedTermId ?: ($activeEnrollment?->term_id ?? $semesterContext['term_id']);
        $academicYearId = $activeEnrollment?->academic_year_id ?? $semesterContext['academic_year_id'];

        // Week handling
        $requestedWeek = $request->get('week_start') ?: $request->get('week');
        $selectedWeek  = $requestedWeek
            ? Carbon::parse($requestedWeek)->startOfWeek(Carbon::MONDAY)->toDateString()
            : Timetable::currentWeekStart();

        $prevWeek       = Carbon::parse($selectedWeek)->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
        $nextWeek       = Carbon::parse($selectedWeek)->addWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekRangeLabel = Timetable::formatWeekRange($selectedWeek);

        // Day dates for headers (Monday to Saturday)
        $monday = Carbon::parse($selectedWeek)->startOfWeek(Carbon::MONDAY);
        $dayDates = [];
        $isCurrentWeek = ($selectedWeek === Timetable::currentWeekStart());
        $todayDayName  = now()->format('l');

        foreach (Timetable::DAYS as $i => $day) {
            $dayDates[$day] = [
                'formatted' => $monday->copy()->addDays($i)->format('M j'),
                'is_today'  => ($isCurrentWeek && $day === $todayDayName),
            ];
        }

        // Get the IDs of subjects this student is enrolled in for the selected term
        $enrolledSubjectIds = $student->subjects()
            ->when($termId,         fn ($q) => $q->where('student_subject.term_id', $termId))
            ->when($academicYearId, fn ($q) => $q->where('student_subject.academic_year_id', $academicYearId))
            ->pluck('subjects.id');

        // Fetch timetable entries for those subjects on the selected week
        $timetableQuery = Timetable::with(['subject', 'academicYear', 'term'])
            ->where('week_start_date', $selectedWeek)
            ->when($termId,         fn ($q) => $q->where('term_id', $termId))
            ->when($academicYearId, fn ($q) => $q->where('academic_year_id', $academicYearId));

        if ($enrolledSubjectIds->isNotEmpty()) {
            $timetableQuery->whereIn('subject_id', $enrolledSubjectIds);
        }

        $entries = $timetableQuery
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $days           = Timetable::DAYS;
        $activeTerm     = $semesterContext['active_term'];
        $selectedTermId = $termId;
        $isHistorical   = $semesterContext['is_historical'];
        $allTerms       = ActiveSemesterService::allTermsForSelect();

        // Available weeks for this student's schedule
        $availableWeeks = Timetable::whereNotNull('week_start_date')
            ->when($termId, fn($q) => $q->where('term_id', $termId))
            ->distinct()
            ->orderBy('week_start_date', 'desc')
            ->pluck('week_start_date')
            ->map(fn($date) => [
                'value' => Carbon::parse($date)->toDateString(),
                'label' => Timetable::formatWeekRange($date)
            ])
            ->unique('value')
            ->values();

        return view('student.timetable.index', compact(
            'entries',
            'days',
            'activeTerm',
            'selectedTermId',
            'isHistorical',
            'allTerms',
            'activeEnrollment',
            'selectedWeek',
            'prevWeek',
            'nextWeek',
            'weekRangeLabel',
            'dayDates',
            'isCurrentWeek',
            'availableWeeks'
        ));
    }
}
