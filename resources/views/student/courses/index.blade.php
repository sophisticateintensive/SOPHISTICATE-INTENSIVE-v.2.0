@extends('layouts.student')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-500 border border-blue-500/20 font-mono">
                    <i class="fas fa-layer-group mr-1"></i> CURRICULUM
                </span>
                <span class="text-xs text-zinc-400 font-mono">{{ $totalCreditHours }} Total Credits</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                Enrolled Courses
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-semester-selector 
                :selected="$termId" 
                :activeTerm="$activeTerm" 
                :isHistorical="$isHistorical" 
                :allTerms="$terms"
            />

            <a href="{{ route('student.subjects.export', request()->all()) }}"
                class="px-4 py-2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-mono font-bold hover:scale-105 transition flex items-center gap-2 self-start sm:self-auto border border-zinc-200 dark:border-zinc-700">
                <i class="fas fa-file-csv"></i>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    @if($isHistorical)
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-4 font-mono text-xs shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-sm"></i>
                </div>
                <div>
                    <span class="font-bold uppercase tracking-wider block">PAST CURRICULUM ARCHIVE</span>
                    <span>Viewing courses enrolled in previous semester.</span>
                </div>
            </div>
            <a href="{{ route('student.subjects.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 text-zinc-950 font-bold hover:scale-105 transition flex-shrink-0">
                Back to Current &rarr;
            </a>
        </div>
    @endif

    <!-- Active Session Pill -->
    @if($activeEnrollment)
        <div class="cyber-card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-2xl bg-zinc-900 text-[#ccff00] dark:bg-white dark:text-zinc-900 flex items-center justify-center text-base font-black font-mono">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <span class="text-[9px] uppercase font-bold text-zinc-400 font-mono">Academic Term</span>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white font-mono">
                        {{ $activeEnrollment->academicYear->year_name ?? '2026' }} &bull; {{ $activeEnrollment->term->term_name ?? 'Term 1' }}
                    </h3>
                </div>
            </div>
            <span class="px-3 py-1 text-xs font-mono font-bold rounded-full bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 capitalize self-start sm:self-auto">
                Status: {{ $activeEnrollment->status }}
            </span>
        </div>
    @endif

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($subjects as $subject)
            <div class="cyber-card p-6 flex flex-col justify-between space-y-4 group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700">
                            {{ $subject->code }}
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 font-mono">
                            {{ $subject->pivot->status ?? 'Enrolled' }}
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white group-hover:text-blue-500 transition-colors leading-tight">
                            {{ $subject->name }}
                        </h3>
                        <p class="text-xs text-zinc-400 mt-1.5 line-clamp-2 leading-relaxed">
                            {{ $subject->description ?? 'Comprehensive module covering curriculum competencies, lab practicals, and assessments.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-xs font-mono">
                    <span class="text-zinc-500">
                        {{ $subject->credit_hours ?? 3 }} Credits
                    </span>
                    <a href="{{ route('student.subjects.show', $subject) }}"
                        class="font-bold text-blue-500 hover:underline">
                        Details &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-16 text-center cyber-card text-zinc-400 space-y-2">
                <i class="fas fa-layer-group text-3xl opacity-40"></i>
                <p class="text-xs font-bold font-mono">No courses registered for this academic term.</p>
            </div>
        @endforelse
    </div>

    @if($subjects->hasPages())
        <div class="pt-2">
            {{ $subjects->links() }}
        </div>
    @endif

</div>
@endsection