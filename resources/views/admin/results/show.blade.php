@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Assessment Transcript Details
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Official examination result record for {{ $result->student->user->name ?? 'Student' }}</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.results.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Back to Results
            </a>
            <a href="{{ route('admin.results.edit', $result) }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs"></i>
                Edit Result
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Main Card -->
    <div class="bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-[var(--border-color)]">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-bold shadow-md">
                    <i class="fas fa-award"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold">{{ $result->student->user->name ?? 'Student' }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-white/20 text-white uppercase">
                            {{ $result->exam_type == 'exam1' ? 'Exam 1' : 'Exam 2' }}
                        </span>
                    </div>
                    <p class="text-xs text-blue-100 mt-1 font-mono">Reg: {{ $result->student->reg_number ?? 'N/A' }} &bull; {{ $result->student->programme ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="self-start sm:self-auto text-right">
                <span class="text-xs text-blue-200 block uppercase tracking-wider">Recorded Date</span>
                <span class="text-sm font-bold text-white">{{ $result->created_at ? $result->created_at->format('F d, Y') : 'N/A' }}</span>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Bento Metric Highlight -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Score -->
                <div class="p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] text-center">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Assessment Score</span>
                    <p class="text-3xl font-black text-[var(--text-primary)] mt-1">{{ number_format($result->marks, 1) }}%</p>
                    <span class="text-xs text-[var(--text-muted)]">Out of 100%</span>
                </div>

                <!-- Letter Grade -->
                @php
                    $gradeColor = match($result->grade) {
                        'A' => 'text-emerald-500',
                        'B' => 'text-blue-500',
                        'C' => 'text-amber-500',
                        'D' => 'text-orange-500',
                        default => 'text-red-500'
                    };
                @endphp
                <div class="p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] text-center">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Letter Grade</span>
                    <p class="text-3xl font-black {{ $gradeColor }} mt-1">Grade {{ $result->grade }}</p>
                    <span class="text-xs text-[var(--text-muted)]">{{ $result->marks >= 70 ? 'Distinction' : ($result->marks >= 60 ? 'Merit' : ($result->marks >= 50 ? 'Credit' : ($result->marks >= 40 ? 'Pass' : 'Fail'))) }}</span>
                </div>

                <!-- Status -->
                <div class="p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] text-center">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Academic Standing</span>
                    <p class="text-2xl font-black {{ $result->marks >= 40 ? 'text-emerald-500' : 'text-red-500' }} mt-1">
                        {{ $result->marks >= 40 ? 'PASSED' : 'FAILED' }}
                    </p>
                    <span class="text-xs text-[var(--text-muted)]">{{ $result->marks >= 40 ? 'Threshold Met' : 'Remedial Required' }}</span>
                </div>
            </div>

            <!-- Subject & Academic Context -->
            <div class="p-5 rounded-2xl bg-[var(--glass-bg)] border border-[var(--border-color)] space-y-4">
                <h4 class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fas fa-book text-blue-500"></i> Course & Academic Context
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-[var(--text-muted)] block">Subject Name & Code</span>
                        <p class="font-bold text-[var(--text-primary)] mt-0.5">{{ $result->subject->name ?? 'Course' }} ({{ $result->subject->code ?? '' }})</p>
                    </div>
                    <div>
                        <span class="text-xs text-[var(--text-muted)] block">Credit Hours / Weight</span>
                        <p class="font-bold text-[var(--text-primary)] mt-0.5">{{ $result->subject->credit_hours ?? 3 }} Hours</p>
                    </div>
                    <div>
                        <span class="text-xs text-[var(--text-muted)] block">Academic Term</span>
                        <p class="font-bold text-[var(--text-primary)] mt-0.5">{{ $result->term->term_name ?? 'Term' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-[var(--text-muted)] block">Academic Year</span>
                        <p class="font-bold text-[var(--text-primary)] mt-0.5">{{ $result->academicYear->year_name ?? 'Academic Year' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
