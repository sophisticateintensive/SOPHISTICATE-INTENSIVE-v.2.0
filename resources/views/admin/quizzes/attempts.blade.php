@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.quizzes.index') }}" class="hover:text-blue-500 transition">Quizzes</a>
                <span>/</span>
                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="hover:text-blue-500 transition">{{ Str::limit($quiz->title, 25) }}</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">Submissions Ledger</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Candidate Submissions & Attempts
            </h2>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.quizzes.export-attempts', $quiz) }}"
                class="inline-flex items-center px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-xs font-bold rounded-xl transition">
                <i class="fas fa-file-csv mr-1.5"></i>
                Export CSV Gradebook
            </a>
            <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-sliders-h mr-1.5"></i>
                Quiz Settings
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- ========== HERO / STATS SECTION (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 mb-8 shadow-2xl">
        <!-- Animated blobs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    {{ $quiz->title }} &bull; {{ $quiz->subject->code ?? 'General' }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Assessment Telemetry & Gradebook</h1>
                <p class="text-blue-100/80 mt-1">Passing Benchmark: {{ $quiz->passing_score }}% &bull; Total Points: {{ $quiz->total_points }} Pts</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white">{{ $totalSubmissions }}</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Submissions</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-emerald-300">{{ $passRate }}%</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Pass Rate</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-purple-300">{{ $avgScore }}%</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Class Avg</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-amber-300">{{ $highestScore }}%</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Top Score</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== ATTEMPTS TABLE (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-wide">Candidate Performance Log</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Individual candidate score breakdown and submission audits</p>
                </div>
            </div>
            <span class="text-xs text-white/80 bg-white/10 px-3 py-1 rounded-full font-mono">
                {{ $attempts->total() }} Candidates
            </span>
        </div>

        <div class="p-6">
            @if($attempts->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Candidate</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Score & Grade</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Points Earned</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Time Taken</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Submitted At</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)]">
                            @foreach($attempts as $attempt)
                                <tr class="hover:bg-[var(--accent-soft)] transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                {{ substr($attempt->student->user->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.students.show', $attempt->student) }}" class="text-sm font-bold text-[var(--text-primary)] hover:text-blue-600 transition">
                                                    {{ $attempt->student->user->name ?? 'Unknown Student' }}
                                                </a>
                                                <p class="text-xs text-[var(--text-secondary)] font-mono">{{ $attempt->student->reg_number ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-base font-black font-mono {{ $attempt->is_passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                {{ number_format($attempt->score, 1) }}%
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold font-mono {{ $attempt->is_passed ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300' }}">
                                                {{ $attempt->is_passed ? 'PASSED (' . $attempt->grade_letter . ')' : 'FAILED (' . $attempt->grade_letter . ')' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-mono font-bold text-[var(--text-primary)]">
                                        {{ $attempt->total_points_earned }} / {{ $quiz->total_points }} Pts
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                        {{ $attempt->formatted_time_taken }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                        @if($attempt->submitted_at)
                                            <div>{{ $attempt->submitted_at->format('M d, Y · h:i A') }}</div>
                                            @if($attempt->isTimeExpired())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                                    <i class="fas fa-hourglass-end text-[9px]"></i> Time Expired
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                                    <i class="fas fa-check text-[9px]"></i> On Time
                                                </span>
                                            @endif
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/15 text-blue-600">In Progress</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- View Full Transcript -->
                                            <a href="{{ route('admin.quizzes.attempts.show', $attempt) }}"
                                               class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all"
                                               title="View Student Transcript & Question Audit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <!-- 1-Click Reset Attempt for Retake -->
                                            <form action="{{ route('admin.quizzes.attempts.reset', $attempt) }}" method="POST" id="reset-attempt-{{ $attempt->id }}" class="inline">
                                                @csrf
                                                <button type="button" onclick="openDeleteModal(
                                                    'Reset Candidate Attempt?',
                                                    'You are about to clear the assessment submission for {{ addslashes($attempt->student->user->name ?? 'Candidate') }}. This will allow the student to retake the test afresh.',
                                                    document.getElementById('reset-attempt-{{ $attempt->id }}')
                                                )" class="p-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-all" title="Reset Attempt (Allow Retake)">
                                                    <i class="fas fa-redo-alt text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $attempts->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="bg-[var(--glass-bg)] rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center border border-[var(--border-color)]">
                        <svg class="w-12 h-12 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Submissions Recorded Yet</h4>
                    <p class="text-[var(--text-secondary)] mb-4">When enrolled students take and complete this assessment, their scores will appear here in real-time.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
