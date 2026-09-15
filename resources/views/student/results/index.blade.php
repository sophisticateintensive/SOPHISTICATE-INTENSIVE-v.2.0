@extends('layouts.student')

@section('content')
@php
    $averageMarks = $results->avg('marks') ?? 0;
    $highestMark = $results->max('marks') ?? 0;
    $passCount = $results->where('marks', '>=', 40)->count();
    $passRate = $results->count() > 0 ? ($passCount / $results->count()) * 100 : 0;
@endphp

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-500/10 text-purple-500 border border-purple-500/20 font-mono">
                    <i class="fas fa-award mr-1"></i> ACADEMIC TRANSCRIPT
                </span>
                <span class="text-xs text-zinc-400 font-mono">{{ $results->total() }} Recorded Grades</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                Grade Ledger
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-semester-selector 
                :selected="$selectedTermId" 
                :activeTerm="$activeTerm" 
                :isHistorical="$isHistorical" 
                :allTerms="$terms"
            />

            <a href="{{ route('student.results.transcript', ['term_id' => $selectedTermId, 'academic_year' => $selectedAcademicYearId]) }}"
                class="px-4 py-2 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-mono font-bold hover:scale-105 transition flex items-center gap-2 self-start sm:self-auto shadow-md">
                <i class="fas fa-file-pdf"></i>
                <span>Official PDF</span>
            </a>

            <button onclick="window.print()"
                class="px-4 py-2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-mono font-bold hover:scale-105 transition flex items-center gap-2 self-start sm:self-auto border border-zinc-200 dark:border-zinc-700">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    @if($isHistorical)
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-4 font-mono text-xs shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-sm"></i>
                </div>
                <div>
                    <span class="font-bold uppercase tracking-wider block">PAST SEMESTER ARCHIVE</span>
                    <span>Viewing results from previous semester. These historical marks are locked and permanently recorded.</span>
                </div>
            </div>
            <a href="{{ route('student.results.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 text-zinc-950 font-bold hover:scale-105 transition flex-shrink-0">
                Back to Current &rarr;
            </a>
        </div>
    @endif

    <!-- Telemetry Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 font-mono">
        <div class="cyber-card p-5">
            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Average Score</span>
            <p class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white mt-1">{{ number_format($averageMarks, 1) }}%</p>
            <span class="text-[10px] text-zinc-500 mt-1 block">GPA Metric</span>
        </div>

        <div class="cyber-card p-5">
            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Highest Score</span>
            <p class="text-2xl sm:text-3xl font-black text-emerald-500 mt-1">{{ number_format($highestMark, 1) }}%</p>
            <span class="text-[10px] text-emerald-500/70 mt-1 block">Top Assessment</span>
        </div>

        <div class="cyber-card p-5">
            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Pass Benchmark</span>
            <p class="text-2xl sm:text-3xl font-black text-purple-500 mt-1">{{ number_format($passRate, 0) }}%</p>
            <span class="text-[10px] text-purple-500/70 mt-1 block">Passed Modules</span>
        </div>

        <div class="cyber-card p-5">
            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Total Exams</span>
            <p class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white mt-1">{{ $results->total() }}</p>
            <span class="text-[10px] text-zinc-500 mt-1 block">Evaluations</span>
        </div>
    </div>

    <!-- Results Table Card -->
    <div class="cyber-card p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
            <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Exam Results Ledger</span>
            <span class="text-xs text-zinc-500 font-mono">{{ $results->total() }} Records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="text-[10px] font-black uppercase text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="py-3 px-2">Course / Subject</th>
                        <th class="py-3 px-2">Assessment</th>
                        <th class="py-3 px-2">Marks</th>
                        <th class="py-3 px-2">Grade</th>
                        <th class="py-3 px-2 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-sans">
                    @forelse($results as $r)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition">
                            <td class="py-4 px-2 font-bold text-zinc-900 dark:text-white">
                                {{ $r->subject->name ?? 'Course' }}
                                <span class="text-[10px] text-zinc-400 block font-mono">{{ $r->subject->code ?? '' }}</span>
                            </td>
                            <td class="py-4 px-2 text-zinc-400 font-mono text-xs">
                                {{ $r->exam_type == 'exam1' ? 'Exam 1' : 'Exam 2' }} &bull; {{ $r->term->term_name ?? 'Term 1' }}
                            </td>
                            <td class="py-4 px-2 font-mono font-black text-sm text-zinc-900 dark:text-white">
                                {{ number_format($r->marks, 1) }}%
                            </td>
                            <td class="py-4 px-2">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-black border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white">
                                    Grade {{ $r->grade }}
                                </span>
                            </td>
                            <td class="py-4 px-2 text-right">
                                <a href="{{ route('student.results.show', $r) }}" class="text-xs font-bold text-blue-500 hover:underline font-mono">
                                    Report &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-zinc-400 font-mono text-xs">
                                No exam results recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($results->hasPages())
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $results->links() }}
            </div>
        @endif
    </div>

</div>
@endsection