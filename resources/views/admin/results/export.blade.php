@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.results.index') }}" class="hover:text-blue-500 transition">Academic Results</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">Export Preview</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Institutional Academic Export Hub
            </h2>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.results.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-arrow-left mr-1.5"></i>
                Back to Results
            </a>
            <a href="{{ route('admin.results.export', request()->all()) }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-bold rounded-xl shadow hover:scale-105 transition">
                <i class="fas fa-file-download mr-1.5"></i>
                Download CSV Dataset
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $totalRecords = $results->count();
        $avgScore = $totalRecords > 0 ? number_format($results->avg('marks'), 1) : 0;
        $maxScore = $totalRecords > 0 ? number_format($results->max('marks'), 1) : 0;
        $passCount = $results->where('marks', '>=', 50)->count();
        $passRate = $totalRecords > 0 ? round(($passCount / $totalRecords) * 100, 1) : 0;
    @endphp

    <div class="max-w-6xl mx-auto space-y-8">

        <!-- ========== HERO BANNER (STUDENT MANAGEMENT DESIGN) ========== -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 shadow-2xl">
            <!-- Animated blobs -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        {{ $totalRecords }} Result Records Scoped for Export
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Results Dataset Telemetry</h1>
                    <p class="text-blue-100/80 mt-1">Review the filtered dataset and statistical distribution prior to CSV export</p>
                </div>

                <!-- Floating Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 font-mono">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-white">{{ $totalRecords }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Records</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-emerald-300">{{ $avgScore }}%</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Avg Score</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-purple-300">{{ $maxScore }}%</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Top Mark</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-amber-300">{{ $passRate }}%</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Pass Rate</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== PREVIEW TABLE (GLASS) ========== -->
        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-wide">Export Dataset Ledger Preview</h3>
                        <p class="text-xs text-blue-200 mt-0.5">Showing first 10 candidate rows in the export queue</p>
                    </div>
                </div>

                <a href="{{ route('admin.results.export', request()->all()) }}"
                   class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <i class="fas fa-file-csv"></i>
                    <span>Download Full CSV</span>
                </a>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Candidate</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Course</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Exam Period</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Semester</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Score</th>
                                <th class="px-6 py-3.5 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)] text-xs">
                            @foreach($results->take(10) as $r)
                                <tr class="hover:bg-[var(--accent-soft)] transition">
                                    <td class="px-6 py-4 font-bold text-[var(--text-primary)]">
                                        {{ $r->student->user->name ?? 'Candidate' }}
                                        <span class="text-[10px] text-[var(--text-muted)] font-mono block">{{ $r->student->reg_number ?? 'REG-N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-[var(--text-secondary)]">
                                        {{ $r->subject->name ?? 'Course' }}
                                        <span class="text-[10px] text-blue-500 font-mono block font-bold">{{ $r->subject->code ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4 uppercase font-mono text-[var(--text-secondary)]">{{ $r->exam_type }}</td>
                                    <td class="px-6 py-4 text-[var(--text-secondary)]">{{ $r->term->term_name ?? '' }}</td>
                                    <td class="px-6 py-4 font-mono font-bold text-[var(--text-primary)]">{{ number_format($r->marks, 1) }}%</td>
                                    <td class="px-6 py-4 text-right font-black font-mono {{ $r->marks >= 50 ? 'text-emerald-500' : 'text-rose-500' }}">{{ $r->grade }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($results->count() > 10)
                    <p class="text-xs text-center text-[var(--text-muted)] mt-4 font-mono">
                        Showing 10 preview rows of {{ $results->count() }} total dataset records. Full dataset will be exported.
                    </p>
                @endif
            </div>
        </div>

    </div>
@endsection
