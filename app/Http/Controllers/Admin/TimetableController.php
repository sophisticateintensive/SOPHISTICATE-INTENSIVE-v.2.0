<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Timetable;
use App\Services\ActiveSemesterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TimetableController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $requestedTermId  = $request->get('term_id');
        $semesterContext  = ActiveSemesterService::resolve($requestedTermId);
        $selectedTermId   = $requestedTermId ?: $semesterContext['term_id'];
        $isHistorical     = $semesterContext['is_historical'];
        $activeTerm       = $semesterContext['active_term'];
        $allTerms         = ActiveSemesterService::allTermsForSelect();

        // Week handling
        $requestedWeek = $request->get('week_start');
        $isAllWeeks    = ($requestedWeek === 'all');
        
        if (!$isAllWeeks) {
            $selectedWeek = $requestedWeek 
                ? Carbon::parse($requestedWeek)->startOfWeek(Carbon::MONDAY)->toDateString()
                : Timetable::currentWeekStart();
            
            $prevWeek = Carbon::parse($selectedWeek)->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
            $nextWeek = Carbon::parse($selectedWeek)->addWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
            $weekRangeLabel = Timetable::formatWeekRange($selectedWeek);
        } else {
            $selectedWeek   = 'all';
            $prevWeek       = null;
            $nextWeek       = null;
            $weekRangeLabel = 'All Weeks';
        }

        $query = Timetable::with(['subject', 'academicYear', 'term']);

        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        } elseif ($selectedTermId) {
            $query->where('term_id', $selectedTermId);
        }

        if (!$isAllWeeks && $selectedWeek) {
            $query->where('week_start_date', $selectedWeek);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $timetables = $query
            ->orderBy('week_start_date', 'desc')
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
            ->orderBy('start_time')
            ->paginate(20)
            ->appends($request->all());

        $academicYears = AcademicYear::orderBy('id', 'desc')->get();
        $terms         = Term::with('academicYear')->get();
        $subjects      = Subject::orderBy('name')->get();
        $days          = Timetable::DAYS;

        // Collect existing weeks in DB for dropdown selector
        $availableWeeks = Timetable::whereNotNull('week_start_date')
            ->distinct()
            ->orderBy('week_start_date', 'desc')
            ->pluck('week_start_date')
            ->map(fn($date) => [
                'value' => Carbon::parse($date)->toDateString(),
                'label' => Timetable::formatWeekRange($date)
            ])
            ->unique('value')
            ->values();

        return view('admin.timetable.index', compact(
            'timetables', 'academicYears', 'terms', 'subjects', 'days',
            'activeTerm', 'selectedTermId', 'isHistorical', 'allTerms',
            'selectedWeek', 'prevWeek', 'nextWeek', 'weekRangeLabel', 'isAllWeeks', 'availableWeeks'
        ));
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $academicYears = AcademicYear::orderBy('id', 'desc')->get();
        $terms         = Term::with('academicYear')->orderBy('id', 'desc')->get();
        $subjects      = Subject::orderBy('name')->get();
        $days          = Timetable::DAYS;
        $activeTerm    = ActiveSemesterService::getActiveTerm();
        $activeYear    = ActiveSemesterService::getActiveYear();

        $selectedWeek = $request->get('week_start') 
            ? Carbon::parse($request->get('week_start'))->startOfWeek(Carbon::MONDAY)->toDateString()
            : Timetable::currentWeekStart();
        $weekRangeLabel = Timetable::formatWeekRange($selectedWeek);

        return view('admin.timetable.create', compact(
            'academicYears', 'terms', 'subjects', 'days', 'activeTerm', 'activeYear',
            'selectedWeek', 'weekRangeLabel'
        ));
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'term_id'          => 'nullable|exists:terms,id',
            'week_start_date'  => 'required|date',
            'subject_id'       => 'required|exists:subjects,id',
            'day_of_week'      => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'venue'            => 'nullable|string|max:255',
            'lecturer_name'    => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        // Auto-assign active semester if not provided
        if (empty($validated['term_id'])) {
            $validated['term_id'] = ActiveSemesterService::getActiveTerm()?->id;
        }
        if (empty($validated['academic_year_id'])) {
            $validated['academic_year_id'] = ActiveSemesterService::getActiveYear()?->id;
        }

        // Normalize week_start_date to Monday
        $validated['week_start_date'] = Carbon::parse($validated['week_start_date'])->startOfWeek(Carbon::MONDAY)->toDateString();

        // Hard block: same subject + same day + same term + same week
        $subjectConflict = Timetable::where('term_id', $validated['term_id'])
            ->where('week_start_date', $validated['week_start_date'])
            ->where('subject_id', $validated['subject_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->exists();

        if ($subjectConflict) {
            return back()
                ->withErrors(['subject_id' => 'This subject already has a timetable entry on ' . $validated['day_of_week'] . ' for the week of ' . Timetable::formatWeekRange($validated['week_start_date']) . '.'])
                ->withInput();
        }

        // Venue conflict warning (same venue, overlapping time, same week)
        if ($request->filled('venue')) {
            $venueConflict = Timetable::where('term_id', $validated['term_id'])
                ->where('week_start_date', $validated['week_start_date'])
                ->where('day_of_week', $request->day_of_week)
                ->where('venue', $request->venue)
                ->where(fn ($q) => $q
                    ->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                )
                ->exists();

            if ($venueConflict) {
                session()->flash('warning', 'Warning: There is a potential venue conflict at ' . $request->venue . ' during this time slot for this week.');
            }
        }

        Timetable::create($validated);

        return redirect()->route('admin.timetable.index', ['week_start' => $validated['week_start_date'], 'term_id' => $validated['term_id']])
            ->with('success', 'Timetable entry added successfully for ' . Timetable::formatWeekRange($validated['week_start_date']) . '.');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function edit(Timetable $timetable)
    {
        $academicYears  = AcademicYear::orderBy('id', 'desc')->get();
        $terms          = Term::with('academicYear')->orderBy('id', 'desc')->get();
        $subjects       = Subject::orderBy('name')->get();
        $days           = Timetable::DAYS;
        $selectedWeek   = $timetable->week_start_date ? Carbon::parse($timetable->week_start_date)->toDateString() : Timetable::currentWeekStart();
        $weekRangeLabel = Timetable::formatWeekRange($selectedWeek);

        return view('admin.timetable.edit', compact(
            'timetable', 'academicYears', 'terms', 'subjects', 'days', 'selectedWeek', 'weekRangeLabel'
        ));
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id'          => 'required|exists:terms,id',
            'week_start_date'  => 'required|date',
            'subject_id'       => 'required|exists:subjects,id',
            'day_of_week'      => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'venue'            => 'nullable|string|max:255',
            'lecturer_name'    => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        // Normalize week_start_date to Monday
        $validated['week_start_date'] = Carbon::parse($validated['week_start_date'])->startOfWeek(Carbon::MONDAY)->toDateString();

        // Hard block: same subject + same day + same term + same week (excluding self)
        $subjectConflict = Timetable::where('term_id', $validated['term_id'])
            ->where('week_start_date', $validated['week_start_date'])
            ->where('subject_id', $request->subject_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('id', '!=', $timetable->id)
            ->exists();

        if ($subjectConflict) {
            return back()
                ->withErrors(['subject_id' => 'This subject already has a timetable entry on ' . $request->day_of_week . ' for the week of ' . Timetable::formatWeekRange($validated['week_start_date']) . '.'])
                ->withInput();
        }

        // Venue conflict warning (same venue, overlapping time, same week, excluding self)
        if ($request->filled('venue')) {
            $venueConflict = Timetable::where('term_id', $validated['term_id'])
                ->where('week_start_date', $validated['week_start_date'])
                ->where('day_of_week', $request->day_of_week)
                ->where('venue', $request->venue)
                ->where('id', '!=', $timetable->id)
                ->where(fn ($q) => $q
                    ->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                )
                ->exists();

            if ($venueConflict) {
                session()->flash('warning', 'Warning: There is a potential venue conflict at ' . $request->venue . ' during this time slot for this week.');
            }
        }

        $timetable->update($validated);

        return redirect()->route('admin.timetable.index', ['week_start' => $validated['week_start_date'], 'term_id' => $validated['term_id']])
            ->with('success', 'Timetable entry updated successfully.');
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy(Timetable $timetable)
    {
        $weekStart = $timetable->week_start_date ? Carbon::parse($timetable->week_start_date)->toDateString() : null;
        $termId    = $timetable->term_id;
        $timetable->delete();

        return redirect()->route('admin.timetable.index', array_filter(['week_start' => $weekStart, 'term_id' => $termId]))
            ->with('success', 'Timetable entry deleted.');
    }

    // ─── Weekly View ─────────────────────────────────────────────────────────

    public function weekly(Request $request)
    {
        $requestedTermId = $request->get('term_id');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);
        $selectedTermId  = $requestedTermId ?: $semesterContext['term_id'];
        $activeTerm      = $semesterContext['active_term'];
        $isHistorical    = $semesterContext['is_historical'];
        $allTerms        = ActiveSemesterService::allTermsForSelect();

        // Week handling
        $requestedWeek = $request->get('week_start');
        $selectedWeek  = $requestedWeek 
            ? Carbon::parse($requestedWeek)->startOfWeek(Carbon::MONDAY)->toDateString()
            : Timetable::currentWeekStart();

        $prevWeek       = Carbon::parse($selectedWeek)->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
        $nextWeek       = Carbon::parse($selectedWeek)->addWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekRangeLabel = Timetable::formatWeekRange($selectedWeek);

        // Day dates for headers (Monday to Saturday)
        $monday = Carbon::parse($selectedWeek)->startOfWeek(Carbon::MONDAY);
        $dayDates = [];
        foreach (Timetable::DAYS as $i => $day) {
            $dayDates[$day] = $monday->copy()->addDays($i)->format('M j');
        }

        $entries = Timetable::with(['subject', 'academicYear', 'term'])
            ->when($selectedTermId, fn ($q) => $q->where('term_id', $selectedTermId))
            ->where('week_start_date', $selectedWeek)
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $days = Timetable::DAYS;

        // Build a unique colour map per subject
        $palette = [
            'from-blue-500 to-indigo-600',
            'from-emerald-500 to-teal-600',
            'from-violet-500 to-purple-600',
            'from-rose-500 to-pink-600',
            'from-amber-500 to-orange-600',
            'from-cyan-500 to-sky-600',
            'from-lime-500 to-green-600',
            'from-fuchsia-500 to-pink-700',
        ];

        $subjectColors = [];
        $colorIndex    = 0;
        foreach ($entries->flatten() as $entry) {
            $sid = $entry->subject_id;
            if (!isset($subjectColors[$sid])) {
                $subjectColors[$sid] = $palette[$colorIndex % count($palette)];
                $colorIndex++;
            }
        }

        // Available weeks for dropdown
        $availableWeeks = Timetable::whereNotNull('week_start_date')
            ->distinct()
            ->orderBy('week_start_date', 'desc')
            ->pluck('week_start_date')
            ->map(fn($date) => [
                'value' => Carbon::parse($date)->toDateString(),
                'label' => Timetable::formatWeekRange($date)
            ])
            ->unique('value')
            ->values();

        return view('admin.timetable.weekly', compact(
            'entries', 'days', 'activeTerm', 'selectedTermId',
            'isHistorical', 'allTerms', 'subjectColors',
            'selectedWeek', 'prevWeek', 'nextWeek', 'weekRangeLabel', 'dayDates', 'availableWeeks'
        ));
    }

    // ─── Copy Week ───────────────────────────────────────────────────────────

    public function copyWeek(Request $request)
    {
        $validated = $request->validate([
            'source_week' => 'required|date',
            'target_week' => 'required|date|different:source_week',
            'term_id'     => 'nullable|exists:terms,id',
        ]);

        $sourceMonday = Carbon::parse($validated['source_week'])->startOfWeek(Carbon::MONDAY)->toDateString();
        $targetMonday = Carbon::parse($validated['target_week'])->startOfWeek(Carbon::MONDAY)->toDateString();

        $sourceEntries = Timetable::where('week_start_date', $sourceMonday)
            ->when(!empty($validated['term_id']), fn($q) => $q->where('term_id', $validated['term_id']))
            ->get();

        if ($sourceEntries->isEmpty()) {
            return back()->with('error', 'No schedule entries found in ' . Timetable::formatWeekRange($sourceMonday) . ' to copy.');
        }

        $copiedCount = 0;
        $skippedCount = 0;

        foreach ($sourceEntries as $entry) {
            // Check if target week already has this subject on the same day for this term
            $exists = Timetable::where('term_id', $entry->term_id)
                ->where('week_start_date', $targetMonday)
                ->where('subject_id', $entry->subject_id)
                ->where('day_of_week', $entry->day_of_week)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            $newEntry = $entry->replicate();
            $newEntry->week_start_date = $targetMonday;
            $newEntry->save();
            $copiedCount++;
        }

        $msg = "Copied {$copiedCount} class schedule(s) to week of " . Timetable::formatWeekRange($targetMonday) . ".";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} skipped as they already existed).";
        }

        return redirect()->route('admin.timetable.weekly', [
            'week_start' => $targetMonday,
            'term_id'    => $validated['term_id'] ?? null
        ])->with('success', $msg);
    }

    // ─── Export CSV ──────────────────────────────────────────────────────────

    public function export(Request $request): StreamedResponse
    {
        $requestedTermId = $request->get('term_id');
        $semesterContext = ActiveSemesterService::resolve($requestedTermId);
        $selectedTermId  = $requestedTermId ?: $semesterContext['term_id'];

        $query = Timetable::with(['subject', 'academicYear', 'term'])
            ->when($selectedTermId, fn ($q) => $q->where('term_id', $selectedTermId))
            ->when($request->filled('day_of_week'), fn ($q) => $q->where('day_of_week', $request->day_of_week));

        if ($request->filled('week_start') && $request->week_start !== 'all') {
            $monday = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY)->toDateString();
            $query->where('week_start_date', $monday);
        }

        $timetables = $query
            ->orderBy('week_start_date', 'desc')
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
            ->orderBy('start_time')
            ->get();

        $filename = 'timetable_' . ($semesterContext['term']?->term_name ?? 'all') . '_' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($timetables) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Week', 'Day', 'Start Time', 'End Time', 'Subject Code', 'Subject Name', 'Venue', 'Lecturer', 'Academic Year', 'Term', 'Notes']);

            foreach ($timetables as $t) {
                fputcsv($handle, [
                    $t->week_start_date ? Timetable::formatWeekRange($t->week_start_date) : 'N/A',
                    $t->day_of_week,
                    Carbon::parse($t->start_time)->format('H:i'),
                    Carbon::parse($t->end_time)->format('H:i'),
                    $t->subject?->code ?? '',
                    $t->subject?->name ?? '',
                    $t->venue ?? '',
                    $t->lecturer_name ?? '',
                    $t->academicYear?->year_name ?? '',
                    $t->term?->term_name ?? '',
                    $t->notes ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
