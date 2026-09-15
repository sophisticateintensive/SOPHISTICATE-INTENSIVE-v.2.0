@extends('layouts.student')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between gap-4 pt-2">
        <div>
            <span class="text-xs font-mono uppercase font-bold text-zinc-400">Examination Report</span>
            <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Grade Evaluation</h1>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('student.results.index') }}"
                class="px-3.5 py-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold hover:scale-105 transition">
                &larr; Ledger
            </a>
            <button onclick="window.print()"
                class="px-3.5 py-1.5 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-xs font-mono font-black hover:scale-105 transition">
                Print
            </button>
        </div>
    </div>

    <!-- Highlight Pass -->
    <div class="cyber-pass-card rounded-4xl p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-lg bg-zinc-800 text-[#ccff00] text-xs font-mono font-bold">{{ $result->subject->code ?? 'N/A' }}</span>
                <h2 class="text-2xl font-black text-zinc-900 dark:text-white mt-1.5">{{ $result->subject->name ?? 'Course Title' }}</h2>
                <p class="text-xs text-zinc-400 font-mono mt-1">
                    {{ $result->term->term_name ?? 'Active Term' }} &bull; {{ $result->academicYear->year_name ?? '2026' }}
                </p>
            </div>
            <span class="px-3 py-1 bg-zinc-800 text-white rounded-full text-xs font-mono font-bold uppercase self-start sm:self-auto">
                {{ $result->exam_type == 'exam1' ? 'Exam 1' : 'Exam 2' }}
            </span>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-mono text-center">
            <div class="p-6 rounded-3xl bg-zinc-100 dark:bg-zinc-800/80">
                <span class="text-[10px] text-zinc-400 uppercase font-bold block">Score Achieved</span>
                <p class="text-4xl sm:text-5xl font-black text-zinc-900 dark:text-white mt-2">{{ number_format($result->marks, 1) }}%</p>
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 mt-4 overflow-hidden">
                    <div class="h-full bg-zinc-900 dark:bg-[#ccff00] rounded-full" style="width: {{ min($result->marks, 100) }}%"></div>
                </div>
            </div>

            <div class="p-6 rounded-3xl bg-zinc-100 dark:bg-zinc-800/80 flex flex-col justify-center items-center">
                <span class="text-[10px] text-zinc-400 uppercase font-bold block">Letter Grade</span>
                <span class="text-5xl font-black text-zinc-900 dark:text-white mt-2">
                    {{ $result->grade }}
                </span>
                <span class="text-xs font-bold px-3 py-0.5 rounded-full mt-2 font-mono {{ $result->marks >= 70 ? 'bg-emerald-500/10 text-emerald-500' : ($result->marks >= 50 ? 'bg-blue-500/10 text-blue-500' : 'bg-rose-500/10 text-rose-500') }}">
                    {{ $result->marks >= 70 ? 'Distinction' : ($result->marks >= 50 ? 'Credit / Pass' : 'Needs Improvement') }}
                </span>
            </div>
        </div>
    </div>

</div>
@endsection
