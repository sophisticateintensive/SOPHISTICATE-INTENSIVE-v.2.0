@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.timetable.index', ['week_start' => $selectedWeek]) }}" class="p-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-blue-500 transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="font-black text-2xl text-[var(--text-primary)] tracking-tight">Weekly Timetable Grid</h2>
                <p class="text-sm text-[var(--text-secondary)] mt-0.5">
                    @if($activeTerm) {{ $activeTerm->term_name }} &mdash; {{ $activeTerm->academicYear->year_name ?? '' }} @else All terms @endif
                </p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-semester-selector :selected="$selectedTermId" :activeTerm="$activeTerm" :isHistorical="$isHistorical" :allTerms="$allTerms" />
            
            {{-- Copy to Next Week Button --}}
            <button type="button" onclick="document.getElementById('copyWeekModal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2.5 bg-[var(--bg-card)] border border-blue-500/30 text-blue-600 dark:text-blue-400 text-sm font-semibold rounded-xl hover:bg-blue-500/10 transition-all shadow-sm">
                <i class="fas fa-copy mr-2 text-xs"></i> Copy Schedule to Week
            </button>

            <a href="{{ route('admin.timetable.create', ['week_start' => $selectedWeek]) }}"
               class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow hover:shadow-lg hover:scale-105 transition-all">
                <i class="fas fa-plus mr-2 text-xs"></i> Add Entry
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $colors = [
            'bg-blue-500', 'bg-emerald-500', 'bg-violet-500', 'bg-rose-500',
            'bg-amber-500', 'bg-cyan-500', 'bg-lime-500', 'bg-fuchsia-500',
            'bg-orange-500', 'bg-teal-500',
        ];
        $ci = 0;
        $colorMap = [];
        foreach($entries->flatten() as $e) {
            if (!isset($colorMap[$e->subject_id])) {
                $colorMap[$e->subject_id] = $colors[$ci % count($colors)];
                $ci++;
            }
        }
    @endphp

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
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
            <a href="{{ route('admin.timetable.weekly', ['week_start' => $prevWeek, 'term_id' => $selectedTermId]) }}"
               class="px-3.5 py-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-xs font-semibold text-[var(--text-secondary)] hover:text-blue-600 hover:border-blue-400 transition-all flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-chevron-left"></i> Previous Week
            </a>

            <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-500/10 border border-blue-500/20">
                <i class="fas fa-calendar-week text-blue-500 text-sm"></i>
                <span class="text-sm font-bold text-blue-700 dark:text-blue-300">{{ $weekRangeLabel }}</span>
                @if($selectedWeek === \App\Models\Timetable::currentWeekStart())
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-600 text-white">This Week</span>
                @endif
            </div>

            <a href="{{ route('admin.timetable.weekly', ['week_start' => $nextWeek, 'term_id' => $selectedTermId]) }}"
               class="px-3.5 py-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-xs font-semibold text-[var(--text-secondary)] hover:text-blue-600 hover:border-blue-400 transition-all flex items-center gap-1.5 shadow-sm">
                Next Week <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        {{-- Jump to week dropdown --}}
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <form method="GET" action="{{ route('admin.timetable.weekly') }}" class="flex items-center gap-2">
                @if($selectedTermId) <input type="hidden" name="term_id" value="{{ $selectedTermId }}"> @endif
                <select name="week_start" onchange="this.form.submit()"
                        class="px-3 py-2 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-[var(--text-primary)] font-semibold cursor-pointer focus:ring-2 focus:ring-blue-500">
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
        </div>
    </div>

    @if($entries->isEmpty())
        <div class="text-center py-20 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl">
            <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-500 text-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="text-lg font-bold text-[var(--text-primary)] mb-1">No schedule configured for {{ $weekRangeLabel }}</h3>
            <p class="text-sm text-[var(--text-secondary)] mb-5">You can add class entries manually or copy the schedule from another week.</p>
            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="document.getElementById('copyWeekModal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2.5 bg-[var(--bg-card)] border border-blue-500/30 text-blue-600 dark:text-blue-400 text-sm font-semibold rounded-xl hover:bg-blue-500/10 transition-all">
                    <i class="fas fa-copy mr-2 text-xs"></i> Copy From Another Week
                </button>
                <a href="{{ route('admin.timetable.create', ['week_start' => $selectedWeek]) }}" class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow hover:bg-blue-700 transition-all">
                    <i class="fas fa-plus mr-2 text-xs"></i> Add First Class for this Week
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            @foreach($days as $day)
                <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white flex items-center justify-between">
                        <div>
                            <h3 class="font-black text-sm uppercase tracking-wider">{{ $day }}</h3>
                            <p class="text-blue-200 text-xs font-mono font-medium">{{ $dayDates[$day] ?? '' }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-white/20 font-bold">
                            {{ $entries->get($day, collect())->count() }}
                        </span>
                    </div>
                    <div class="p-3 space-y-2 flex-1 min-h-[140px]">
                        @forelse($entries->get($day, collect()) as $entry)
                            @php $bgColor = $colorMap[$entry->subject_id] ?? 'bg-blue-500'; @endphp
                            <div class="rounded-xl p-3 text-white text-xs {{ $bgColor }} shadow-sm relative group">
                                <div class="font-bold leading-tight mb-1">{{ $entry->subject->name ?? 'Unknown' }}</div>
                                <div class="text-[11px] opacity-90 font-mono mb-1">
                                    {{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}
                                </div>
                                @if($entry->venue)
                                    <div class="opacity-80 text-[11px]"><i class="fas fa-map-marker-alt mr-1"></i>{{ $entry->venue }}</div>
                                @endif
                                @if($entry->lecturer_name)
                                    <div class="opacity-80 text-[11px]"><i class="fas fa-chalkboard-teacher mr-1"></i>{{ $entry->lecturer_name }}</div>
                                @endif

                                <div class="mt-2 pt-1.5 border-t border-white/20 flex items-center justify-end gap-2 opacity-90">
                                    <a href="{{ route('admin.timetable.edit', $entry) }}" class="text-[10px] hover:underline font-semibold text-white">
                                        <i class="fas fa-edit mr-0.5"></i> Edit
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="flex items-center justify-center h-24 text-[var(--text-muted)] text-xs italic">
                                No classes scheduled
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl text-sm text-[var(--text-secondary)]">
            <span class="font-semibold text-[var(--text-primary)]">Subject Legend:</span>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach($entries->flatten()->unique('subject_id') as $entry)
                    @php $bgColor = $colorMap[$entry->subject_id] ?? 'bg-blue-500'; @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-white {{ $bgColor }}">
                        {{ $entry->subject->code ?? '' }} &mdash; {{ $entry->subject->name ?? '' }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Copy Schedule Modal --}}
    <div id="copyWeekModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-copy text-lg"></i>
                    <h3 class="font-bold text-lg text-[var(--text-primary)]">Copy Weekly Schedule</h3>
                </div>
                <button type="button" onclick="document.getElementById('copyWeekModal').classList.add('hidden')" class="text-[var(--text-muted)] hover:text-[var(--text-primary)]">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <p class="text-xs text-[var(--text-secondary)] mb-4">
                Duplicate all scheduled class entries from one week into another week. Existing entries in the target week will not be overwritten.
            </p>

            <form action="{{ route('admin.timetable.copy-week') }}" method="POST" class="space-y-4">
                @csrf
                @if($selectedTermId) <input type="hidden" name="term_id" value="{{ $selectedTermId }}"> @endif

                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1">Source Week (Copy From)</label>
                    <input type="date" name="source_week" value="{{ $selectedWeek }}" required
                           class="w-full px-3 py-2 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl font-mono text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1">Target Week (Copy To)</label>
                    <input type="date" name="target_week" value="{{ $nextWeek }}" required
                           class="w-full px-3 py-2 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl font-mono text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow">
                        <i class="fas fa-clone mr-1"></i> Copy Schedule
                    </button>
                    <button type="button" onclick="document.getElementById('copyWeekModal').classList.add('hidden')" class="px-4 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-secondary)] text-sm font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
