@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.academic-years.index') }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Academic Years
                </a>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide mt-1">
                {{ $academicYear->year_name }}
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-0.5">Academic session details, associated semesters and active student enrollments</p>
        </div>

        <div class="flex items-center gap-2">
            @if(!$academicYear->is_current)
                <form action="{{ route('admin.academic-years.set-current', $academicYear) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md transition">
                        Set as Active Session
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.academic-years.edit', $academicYear) }}"
               class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-md transition">
                Edit Year
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Session Overview Card -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-lg mb-8">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-[var(--border-color)]">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-black text-[var(--text-primary)]">{{ $academicYear->year_name }}</h3>
                        @if($academicYear->is_current)
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-500 text-white">ACTIVE</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">ARCHIVED</span>
                        @endif
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] mt-1 font-mono">
                        Date Span: {{ $academicYear->start_date ? \Carbon\Carbon::parse($academicYear->start_date)->format('M d, Y') : 'Start Date N/A' }} — {{ $academicYear->end_date ? \Carbon\Carbon::parse($academicYear->end_date)->format('M d, Y') : 'End Date N/A' }}
                    </p>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl px-4 py-2.5 text-center">
                    <div class="text-lg font-black text-blue-600 dark:text-blue-400">{{ $academicYear->terms->count() }}</div>
                    <div class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Semesters</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl px-4 py-2.5 text-center">
                    <div class="text-lg font-black text-purple-600 dark:text-purple-400">{{ $academicYear->studentEnrollments->count() }}</div>
                    <div class="text-[10px] font-bold text-purple-500 uppercase tracking-wider">Enrollments</div>
                </div>
            </div>
        </div>

        <!-- Associated Semesters / Terms -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-base font-bold text-[var(--text-primary)] flex items-center gap-2">
                    <i class="fas fa-layer-group text-blue-500"></i>
                    Associated Semesters / Terms
                </h4>
                <a href="{{ route('admin.terms.create') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    + Add New Term
                </a>
            </div>

            @if($academicYear->terms->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($academicYear->terms as $term)
                        <div class="bg-[var(--bg-card)] rounded-xl p-4 border border-[var(--border-color)] shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-bold text-sm text-[var(--text-primary)]">{{ $term->term_name }}</h5>
                                    @if($term->is_current)
                                        <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-emerald-500 text-white">ACTIVE</span>
                                    @endif
                                </div>
                                <p class="text-xs text-[var(--text-secondary)] font-mono">
                                    Status: {{ $term->is_locked ? 'Locked (Read-Only)' : 'Open for updates' }}
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-[var(--border-color)] flex items-center justify-between">
                                <a href="{{ route('admin.terms.edit', $term) }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">Edit Term</a>
                                @if(!$term->is_current)
                                    <form action="{{ route('admin.terms.set-active', $term) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-emerald-600 hover:underline">Set Active</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-[var(--text-muted)] py-4 text-center">No semesters / terms defined for this academic year yet.</p>
            @endif
        </div>
    </div>
@endsection