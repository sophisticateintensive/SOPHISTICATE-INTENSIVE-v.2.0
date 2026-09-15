@extends('layouts.student')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between gap-4 pt-2">
        <div>
            <span class="text-xs font-mono uppercase font-bold text-zinc-400">Course Syllabus</span>
            <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ $subject->name }}</h1>
        </div>
        <a href="{{ route('student.subjects.index') }}"
            class="px-3.5 py-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold hover:scale-105 transition">
            &larr; Courses
        </a>
    </div>

    <!-- Main Overview Card -->
    <div class="cyber-pass-card rounded-4xl p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-zinc-900 text-[#ccff00] dark:bg-white dark:text-zinc-900 flex items-center justify-center text-xl font-mono font-black shadow-md">
                    {{ substr($subject->code, 0, 3) }}
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-lg bg-zinc-800 text-[#ccff00] text-xs font-mono font-bold">{{ $subject->code }}</span>
                    <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $subject->name }}</h2>
                </div>
            </div>
            <span class="px-3 py-1 text-xs font-mono font-bold rounded-full bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 capitalize self-start sm:self-auto">
                Status: {{ $subject->pivot->status ?? 'Enrolled' }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-800 font-mono text-center">
            <div class="p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80">
                <span class="text-[10px] text-zinc-400 block uppercase">Credit Weight</span>
                <span class="text-base font-black text-zinc-900 dark:text-white">{{ $subject->credit_hours ?? 3 }} Hours</span>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80">
                <span class="text-[10px] text-zinc-400 block uppercase">Academic Year</span>
                <span class="text-base font-black text-zinc-900 dark:text-white">{{ \App\Models\AcademicYear::find($subject->pivot->academic_year_id)?->year_name ?? '2026' }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80">
                <span class="text-[10px] text-zinc-400 block uppercase">Enrolled Term</span>
                <span class="text-base font-black text-zinc-900 dark:text-white">{{ \App\Models\Term::find($subject->pivot->term_id)?->term_name ?? 'Term 1' }}</span>
            </div>
        </div>
    </div>

    <!-- Syllabus Description -->
    <div class="cyber-card p-6 sm:p-8 space-y-3">
        <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Curriculum Scope</span>
        <p class="text-xs sm:text-sm text-zinc-400 leading-relaxed font-sans">
            {{ $subject->description ?? 'This module provides a rigorous, intensive curriculum focusing on analytical foundations, practical assessments, and continuous mastery testing.' }}
        </p>
    </div>

</div>
@endsection