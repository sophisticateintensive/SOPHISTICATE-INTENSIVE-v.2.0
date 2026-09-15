@props([
    'selected' => null,
    'allTerms' => null,
    'activeTerm' => null,
    'isHistorical' => false,
    'showAllOption' => true,
    'formClass' => '',
])

@php
    $termsList = $allTerms ?? $allTermsForSelector ?? \App\Services\ActiveSemesterService::allTermsForSelect();
    $active = $activeTerm ?? \App\Services\ActiveSemesterService::getActiveTerm();
    $currentSelection = $selected ?? request('term_id') ?? request('term') ?? $active?->id;
@endphp

<div class="flex flex-col sm:flex-row sm:items-center gap-2.5">
    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 {{ $formClass }}" id="semester-filter-form">
        <!-- Preserve any existing query parameters except term/term_id/page/all_semesters -->
        @foreach(request()->except(['term_id', 'term', 'page', 'all_semesters']) as $key => $val)
            @if(is_array($val))
                @foreach($val as $item)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endif
        @endforeach

        <div class="relative flex items-center">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                <i class="fas fa-calendar-alt text-xs"></i>
            </div>
            
            <select name="term_id" onchange="document.getElementById('semester-filter-form').submit()"
                class="pl-8 pr-8 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-mono font-bold text-zinc-800 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm cursor-pointer transition">
                @if($showAllOption)
                    <option value="" {{ request()->has('all_semesters') || (!$currentSelection && !request()->has('term_id')) ? 'selected' : '' }}>
                        🌐 All Semesters (Historical Archive)
                    </option>
                @endif

                @foreach($termsList as $t)
                    @php
                        $isCurrent = $active && $t->id === $active->id;
                        $isSelected = $currentSelection == $t->id && !request()->has('all_semesters');
                    @endphp
                    <option value="{{ $t->id }}" {{ $isSelected ? 'selected' : '' }}>
                        {{ $t->academicYear?->year_name }} - {{ $t->term_name }} {{ $isCurrent ? '⚡ (ACTIVE)' : ($t->is_locked ? '🔒 (Locked)' : '') }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if($isHistorical)
        <a href="{{ url()->current() }}"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-xs font-mono font-bold hover:bg-amber-500/20 transition">
            <i class="fas fa-history text-[10px]"></i>
            <span>Historical View &bull; Switch to Active &rarr;</span>
        </a>
    @else
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[11px] font-mono font-bold">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Active Semester</span>
        </span>
    @endif
</div>
