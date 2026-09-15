@extends('layouts.student')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-mono">
                    <i class="fas fa-calendar-alt mr-1"></i> ACADEMIC SCHEDULE
                </span>
                <span class="text-xs text-zinc-400 font-mono">
                    @if($activeTerm) {{ $activeTerm->term_name }} &bull; {{ $activeTerm->academicYear->year_name ?? '2026' }} @else Weekly Timetable @endif
                </span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                Lecture Timetable
            </h1>
        </div>

        @if($availableWeeks->isNotEmpty() && $availableWeeks->count() > 1)
            <form method="GET" action="{{ route('student.timetable.index') }}" class="flex items-center gap-2">
                @if($selectedTermId) <input type="hidden" name="term_id" value="{{ $selectedTermId }}"> @endif
                <select name="week_start" onchange="this.form.submit()"
                        class="px-3.5 py-2 text-xs rounded-2xl bg-zinc-100 dark:bg-zinc-800/90 border border-zinc-200 dark:border-zinc-700/60 text-zinc-900 dark:text-zinc-200 font-mono font-bold cursor-pointer focus:ring-2 focus:ring-blue-500 shadow-sm transition">
                    <option value="{{ \App\Models\Timetable::currentWeekStart() }}" {{ $selectedWeek === \App\Models\Timetable::currentWeekStart() ? 'selected' : '' }}>
                        Current Week ({{ \App\Models\Timetable::formatWeekRange(\App\Models\Timetable::currentWeekStart()) }})
                    </option>
                    @foreach($availableWeeks as $w)
                        @if($w['value'] !== \App\Models\Timetable::currentWeekStart())
                            <option value="{{ $w['value'] }}" {{ $selectedWeek === $w['value'] ? 'selected' : '' }}>{{ $w['label'] }}</option>
                        @endif
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    {{-- Week Navigator Bar --}}
    <div class="cyber-card p-4 flex flex-col sm:flex-row items-center justify-between gap-3 font-mono">
        <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-start">
            <a href="{{ route('student.timetable.index', ['week_start' => $prevWeek, 'term_id' => $selectedTermId]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-white hover:bg-blue-600 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 transition flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-chevron-left text-[10px]"></i>
                <span>Prev Week</span>
            </a>

            <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                <i class="fas fa-calendar-week text-xs"></i>
                <span class="text-xs sm:text-sm font-black">{{ $weekRangeLabel }}</span>
                @if($isCurrentWeek)
                    <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-blue-600 text-white shadow-sm">This Week</span>
                @endif
            </div>

            <a href="{{ route('student.timetable.index', ['week_start' => $nextWeek, 'term_id' => $selectedTermId]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-white hover:bg-blue-600 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 transition flex items-center gap-1.5 shadow-sm">
                <span>Next Week</span>
                <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        @if(!$isCurrentWeek)
            <a href="{{ route('student.timetable.index', ['week_start' => \App\Models\Timetable::currentWeekStart(), 'term_id' => $selectedTermId]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white transition-all shadow-sm">
                <i class="fas fa-history mr-1"></i> Jump to Current Week
            </a>
        @endif
    </div>

    @if($entries->isEmpty())
        <div class="cyber-card text-center py-16 px-4 space-y-3">
            <div class="w-16 h-16 rounded-3xl bg-blue-500/10 text-blue-500 text-2xl flex items-center justify-center mx-auto shadow-sm">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h3 class="text-lg font-black text-zinc-900 dark:text-white">No Classes Scheduled</h3>
            <p class="text-xs text-zinc-400 max-w-sm mx-auto font-mono leading-relaxed">
                @if($activeEnrollment)
                    There are no scheduled class sessions for the week of {{ $weekRangeLabel }}. Check other weeks or consult your course outline.
                @else
                    You are not currently enrolled in any term. Contact the administration desk.
                @endif
            </p>
        </div>
    @else
        {{-- Day Cards --}}
        <div class="space-y-4">
            @foreach($days as $day)
                @php 
                    $dayEntries = $entries->get($day, collect());
                    $rawDayInfo = $dayDates[$day] ?? null;
                    $dayFormatted = is_array($rawDayInfo) ? ($rawDayInfo['formatted'] ?? '') : (is_string($rawDayInfo) ? $rawDayInfo : '');
                    $isToday = is_array($rawDayInfo) ? ($rawDayInfo['is_today'] ?? false) : false;
                @endphp
                @if($dayEntries->isNotEmpty())
                    <div class="cyber-card overflow-hidden {{ $isToday ? 'ring-2 ring-blue-500 shadow-xl shadow-blue-500/10' : '' }}">
                        
                        <div class="px-5 py-3.5 flex items-center justify-between {{ $isToday ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white' : 'bg-zinc-100 dark:bg-zinc-800/80 border-b border-zinc-200 dark:border-zinc-700/60' }} backdrop-blur-sm">
                            <div class="flex items-center gap-2.5">
                                <h3 class="font-black text-sm uppercase tracking-widest {{ $isToday ? 'text-white' : 'text-zinc-900 dark:text-white' }} font-mono">{{ $day }}</h3>
                                @if($dayFormatted)
                                    <span class="text-xs {{ $isToday ? 'text-blue-100' : 'text-zinc-400' }} font-mono font-medium">({{ $dayFormatted }})</span>
                                @endif
                            </div>
                            @if($isToday)
                                <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-black uppercase tracking-wider animate-pulse font-mono">
                                    Today
                                </span>
                            @endif
                        </div>

                        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($dayEntries as $entry)
                                <div class="rounded-2xl p-4 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-700/60 transition-transform hover:scale-[1.01] space-y-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <div class="font-bold text-zinc-900 dark:text-white text-sm leading-tight">{{ $entry->subject->name ?? 'Course Title' }}</div>
                                            <div class="text-blue-500 font-mono text-xs mt-0.5">{{ $entry->subject->code ?? '' }}</div>
                                        </div>
                                        <span class="flex-shrink-0 inline-flex items-center px-2 py-1 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-bold font-mono border border-blue-500/20">
                                            {{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }}
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-clock text-blue-500 w-3"></i>
                                            <span>{{ \Carbon\Carbon::parse($entry->start_time)->format('h:i A') }} &ndash; {{ \Carbon\Carbon::parse($entry->end_time)->format('h:i A') }}</span>
                                        </div>
                                        @if($entry->venue)
                                            <div class="flex items-center gap-1.5">
                                                <i class="fas fa-map-marker-alt text-blue-500 w-3"></i>
                                                <span>{{ $entry->venue }}</span>
                                            </div>
                                        @endif
                                        @if($entry->lecturer_name)
                                            <div class="flex items-center gap-1.5">
                                                <i class="fas fa-chalkboard-teacher text-blue-500 w-3"></i>
                                                <span>{{ $entry->lecturer_name }}</span>
                                            </div>
                                        @endif
                                        @if($entry->notes)
                                            <div class="flex items-center gap-1.5 text-zinc-400">
                                                <i class="fas fa-sticky-note text-blue-500 w-3"></i>
                                                <span>{{ $entry->notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Summary count --}}
        <div class="cyber-card p-4 text-center text-xs font-mono text-zinc-400">
            <i class="fas fa-info-circle mr-1 text-blue-500"></i>
            {{ $entries->flatten()->count() }} lecture session{{ $entries->flatten()->count() != 1 ? 's' : '' }} scheduled for {{ $weekRangeLabel }}
        </div>
    @endif

</div>
@endsection
