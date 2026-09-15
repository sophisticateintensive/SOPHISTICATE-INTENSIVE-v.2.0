@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-blue-500/10 text-blue-500 text-lg"><i class="fas fa-calendar-alt"></i></span>
                <h2 class="font-black text-2xl text-[var(--text-primary)] tracking-tight">Timetable Management</h2>
            </div>
            <p class="text-sm text-[var(--text-secondary)] mt-1 ml-11">Weekly schedule manager &mdash; configure weekly classes, venues, and lecturers.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.timetable.weekly', ['week_start' => ($selectedWeek !== 'all' ? $selectedWeek : null), 'term_id' => $selectedTermId]) }}"
               class="inline-flex items-center px-4 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-primary)] text-sm font-semibold rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                <i class="fas fa-th mr-2 text-blue-500 text-xs"></i> Weekly Grid View
            </a>
            <a href="{{ route('admin.timetable.export', request()->all()) }}"
               class="inline-flex items-center px-4 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-primary)] text-sm font-semibold rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all">
                <i class="fas fa-download mr-2 text-emerald-500 text-xs"></i> Export CSV
            </a>
            <a href="{{ route('admin.timetable.create', ['week_start' => ($selectedWeek !== 'all' ? $selectedWeek : null)]) }}"
               class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow hover:shadow-lg hover:scale-105 transition-all">
                <i class="fas fa-plus mr-2 text-xs"></i> Add Entry
            </a>
        </div>
    </div>
@endsection

@section('content')

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300">
            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-700 dark:text-red-300">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Week Navigator Bar --}}
    <div class="mb-6 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 w-full md:w-auto justify-between md:justify-start">
            @if(!$isAllWeeks && $prevWeek)
                <a href="{{ route('admin.timetable.index', array_merge(request()->except('page'), ['week_start' => $prevWeek])) }}"
                   class="px-3 py-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-xs font-semibold text-[var(--text-secondary)] hover:text-blue-600 hover:border-blue-400 transition-all flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-chevron-left"></i> Previous Week
                </a>
            @endif

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-500/10 border border-blue-500/20">
                <i class="fas fa-calendar-week text-blue-500 text-sm"></i>
                <span class="text-sm font-bold text-blue-700 dark:text-blue-300">{{ $weekRangeLabel }}</span>
                @if($selectedWeek === \App\Models\Timetable::currentWeekStart())
                    <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-blue-600 text-white">This Week</span>
                @endif
            </div>

            @if(!$isAllWeeks && $nextWeek)
                <a href="{{ route('admin.timetable.index', array_merge(request()->except('page'), ['week_start' => $nextWeek])) }}"
                   class="px-3 py-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-xs font-semibold text-[var(--text-secondary)] hover:text-blue-600 hover:border-blue-400 transition-all flex items-center gap-1.5 shadow-sm">
                    Next Week <i class="fas fa-chevron-right"></i>
                </a>
            @endif
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <a href="{{ route('admin.timetable.index', array_merge(request()->except('page'), ['week_start' => \App\Models\Timetable::currentWeekStart()])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold {{ $selectedWeek === \App\Models\Timetable::currentWeekStart() ? 'bg-blue-600 text-white' : 'bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-blue-500' }} transition-all">
                Today's Week
            </a>

            <a href="{{ route('admin.timetable.index', array_merge(request()->except('page'), ['week_start' => 'all'])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold {{ $isAllWeeks ? 'bg-blue-600 text-white' : 'bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-blue-500' }} transition-all">
                All Weeks
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.timetable.index') }}" class="mb-6">
        <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl p-4 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <div>
                    <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1 block">Week</label>
                    <select name="week_start" class="w-full px-3 py-2.5 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] appearance-none cursor-pointer">
                        <option value="all" {{ $isAllWeeks ? 'selected' : '' }}>— All Weeks —</option>
                        <option value="{{ \App\Models\Timetable::currentWeekStart() }}" {{ $selectedWeek === \App\Models\Timetable::currentWeekStart() ? 'selected' : '' }}>
                            Current Week ({{ \App\Models\Timetable::formatWeekRange(\App\Models\Timetable::currentWeekStart()) }})
                        </option>
                        @foreach($availableWeeks as $w)
                            @if($w['value'] !== \App\Models\Timetable::currentWeekStart())
                                <option value="{{ $w['value'] }}" {{ $selectedWeek === $w['value'] ? 'selected' : '' }}>{{ $w['label'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1 block">Term / Semester</label>
                    <select name="term_id" class="w-full px-3 py-2.5 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] appearance-none cursor-pointer">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>{{ $term->academicYear->year_name ?? '' }} – {{ $term->term_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1 block">Day</label>
                    <select name="day_of_week" class="w-full px-3 py-2.5 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] appearance-none cursor-pointer">
                        <option value="">All Days</option>
                        @foreach($days as $day)
                            <option value="{{ $day }}" {{ request('day_of_week') == $day ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1 block">Subject</label>
                    <select name="subject_id" class="w-full px-3 py-2.5 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] appearance-none cursor-pointer">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->code }} – {{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1 block">Academic Year</label>
                    <select name="academic_year_id" class="w-full px-3 py-2.5 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] appearance-none cursor-pointer">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->year_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.timetable.index') }}" class="py-2.5 px-3 bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-secondary)] text-sm font-semibold rounded-xl text-center transition-all hover:bg-gray-100 dark:hover:bg-gray-800">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl shadow-sm overflow-hidden">
        @if($timetables->isEmpty())
            <div class="text-center py-20">
                <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-500 text-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="text-lg font-bold text-[var(--text-primary)] mb-1">No timetable entries scheduled</h3>
                <p class="text-sm text-[var(--text-secondary)] mb-4">
                    @if(!$isAllWeeks)
                        No classes scheduled for {{ $weekRangeLabel }}.
                    @else
                        No timetable entries found matching your filters.
                    @endif
                </p>
                <a href="{{ route('admin.timetable.create', ['week_start' => ($selectedWeek !== 'all' ? $selectedWeek : null)]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl">
                    <i class="fas fa-plus mr-2 text-xs"></i> Schedule Class for this Week
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs uppercase tracking-wider">
                            <th class="px-4 py-3 text-left font-semibold">Week</th>
                            <th class="px-4 py-3 text-left font-semibold">Day</th>
                            <th class="px-4 py-3 text-left font-semibold">Time</th>
                            <th class="px-4 py-3 text-left font-semibold">Subject</th>
                            <th class="px-4 py-3 text-left font-semibold">Venue</th>
                            <th class="px-4 py-3 text-left font-semibold">Lecturer</th>
                            <th class="px-4 py-3 text-left font-semibold">Term</th>
                            <th class="px-4 py-3 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]">
                        @foreach($timetables as $entry)
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-[var(--text-secondary)]">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-[var(--text-primary)] font-medium">
                                        {{ $entry->formatted_week }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-700 dark:text-blue-300 text-xs font-bold">
                                        {{ $entry->day_of_week }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-[var(--text-secondary)]">
                                    {{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-[var(--text-primary)]">{{ $entry->subject->name ?? '—' }}</div>
                                    <div class="text-xs text-[var(--text-muted)]">{{ $entry->subject->code ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-[var(--text-secondary)]">{{ $entry->venue ?: '—' }}</td>
                                <td class="px-4 py-3 text-[var(--text-secondary)]">{{ $entry->lecturer_name ?: '—' }}</td>
                                <td class="px-4 py-3 text-xs text-[var(--text-secondary)]">
                                    {{ $entry->term->term_name ?? '—' }}<br>
                                    <span class="text-[var(--text-muted)]">{{ $entry->academicYear->year_name ?? '' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.timetable.edit', $entry) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-500/10 text-blue-700 dark:text-blue-300 text-xs font-semibold rounded-lg hover:bg-blue-500/20 transition-all">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.timetable.destroy', $entry) }}" method="POST"
                                              onsubmit="return confirm('Delete this timetable entry?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-500/10 text-red-700 dark:text-red-300 text-xs font-semibold rounded-lg hover:bg-red-500/20 transition-all">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($timetables->hasPages())
                <div class="px-6 py-4 border-t border-[var(--border-color)]">
                    {{ $timetables->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
