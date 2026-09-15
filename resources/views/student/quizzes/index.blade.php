@extends('layouts.student')

@section('content')
<div class="space-y-6">

    <!-- Header (Matching Portal Design Standard) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-mono">
                    <i class="fas fa-bolt mr-1"></i> ASSESSMENT STUDIO
                </span>
                <span class="text-xs text-zinc-400 font-mono">{{ $availableQuizzes->count() }} Available Tests</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                Online Quizzes
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="px-4 py-2 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-mono font-black text-xs">
                    {{ $availableQuizzes->count() }}
                </div>
                <div>
                    <span class="text-[9px] uppercase font-bold text-zinc-400 font-mono block">Available</span>
                    <span class="text-xs font-black text-zinc-900 dark:text-white">Assessments</span>
                </div>
            </div>

            <div class="px-4 py-2 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-mono font-black text-xs">
                    {{ $pastAttempts->where('is_passed', true)->count() }}
                </div>
                <div>
                    <span class="text-[9px] uppercase font-bold text-zinc-400 font-mono block">Passed</span>
                    <span class="text-xs font-black text-zinc-900 dark:text-white">{{ $pastAttempts->count() }} Completed</span>
                </div>
            </div>
        </div>
    </div>

        <!-- ========== AVAILABLE ASSESSMENTS ========== -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-[var(--text-primary)] flex items-center gap-2">
                    <i class="fas fa-feather-alt text-blue-500"></i>
                    <span>Available & Assigned Quizzes</span>
                </h3>
                <span class="text-xs text-[var(--text-muted)] font-mono">{{ $availableQuizzes->count() }} active tests</span>
            </div>

            @if($availableQuizzes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($availableQuizzes as $quiz)
                        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl border border-[var(--border-color)] shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col justify-between">
                            <div>
                                <!-- Header Band -->
                                <div class="h-2 bg-gradient-to-r from-blue-500 to-indigo-600"></div>

                                <div class="p-5 space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                                {{ $quiz->subject->code ?? 'General' }}
                                            </span>
                                            <h4 class="text-base font-bold text-[var(--text-primary)] mt-1.5 line-clamp-1">
                                                {{ $quiz->title }}
                                            </h4>
                                        </div>

                                        @if($quiz->active_attempt)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                                In Progress
                                            </span>
                                        @elseif($quiz->attempts_count > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono {{ $quiz->best_score >= $quiz->passing_score ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300' }}">
                                                Best: {{ number_format($quiz->best_score, 0) }}%
                                            </span>
                                        @endif
                                    </div>

                                    @if($quiz->description)
                                        <p class="text-xs text-[var(--text-secondary)] line-clamp-2">{{ $quiz->description }}</p>
                                    @endif

                                    <!-- Specs Grid -->
                                    <div class="grid grid-cols-2 gap-2 text-xs font-mono pt-1">
                                        <div class="p-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                                            <span class="text-[10px] text-[var(--text-muted)] block uppercase">Timer</span>
                                            <span class="font-bold text-[var(--text-primary)]">
                                                {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' mins' : 'Untimed' }}
                                            </span>
                                        </div>

                                        <div class="p-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                                            <span class="text-[10px] text-[var(--text-muted)] block uppercase">Pass Mark</span>
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                                {{ $quiz->passing_score }}%
                                            </span>
                                        </div>

                                        <div class="p-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                                            <span class="text-[10px] text-[var(--text-muted)] block uppercase">Questions</span>
                                            <span class="font-bold text-[var(--text-primary)]">
                                                {{ $quiz->questions->count() }} items ({{ $quiz->total_points }} pts)
                                            </span>
                                        </div>

                                        <div class="p-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                                            <span class="text-[10px] text-[var(--text-muted)] block uppercase">Attempts Left</span>
                                            <span class="font-bold text-[var(--text-primary)]">
                                                {{ $quiz->remaining_attempts > 100 ? 'Unlimited' : $quiz->remaining_attempts . ' of ' . $quiz->max_attempts }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Launch Bar -->
                            <div class="px-5 py-4 bg-[var(--bg-card)] border-t border-[var(--border-color)] flex items-center justify-between">
                                @if($quiz->active_attempt)
                                    <a href="{{ route('student.quizzes.take', $quiz->id) }}"
                                       class="w-full py-2.5 px-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-xs font-bold rounded-xl shadow hover:shadow-lg hover:scale-102 transition text-center flex items-center justify-center gap-1.5">
                                        <i class="fas fa-play text-[10px]"></i>
                                        <span>Resume Active Exam</span>
                                    </a>
                                @elseif($quiz->can_take)
                                    <a href="{{ route('student.quizzes.take', $quiz->id) }}"
                                       class="w-full py-2.5 px-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow hover:shadow-lg hover:scale-102 transition text-center flex items-center justify-center gap-1.5">
                                        <span>Launch Assessment</span>
                                        <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                @else
                                    <span class="w-full py-2 px-3 text-center text-xs font-bold text-[var(--text-muted)] bg-[var(--glass-bg)] rounded-xl border border-[var(--border-color)]">
                                        Attempts Exhausted
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-[var(--glass-bg)] rounded-2xl border border-[var(--border-color)] p-6">
                    <div class="w-16 h-16 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h4 class="text-base font-bold text-[var(--text-primary)]">No Active Quizzes Assigned</h4>
                    <p class="text-xs text-[var(--text-secondary)]">Your course instructors haven't published any quizzes for this semester yet.</p>
                </div>
            @endif
        </div>

        <!-- ========== PAST ATTEMPTS & TRANSCRIPTS ========== -->
        @if($pastAttempts->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[var(--text-primary)] flex items-center gap-2">
                        <i class="fas fa-history text-purple-500"></i>
                        <span>Past Assessment Transcripts</span>
                    </h3>
                    <span class="text-xs text-[var(--text-muted)] font-mono">{{ $pastAttempts->count() }} submissions</span>
                </div>

                <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl border border-[var(--border-color)] overflow-hidden shadow">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[var(--border-color)]">
                            <thead class="bg-gray-50/50 dark:bg-gray-800/40">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Assessment</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Score</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Status</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Points</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Date</th>
                                    <th class="px-6 py-3.5 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border-color)]">
                                @foreach($pastAttempts as $attempt)
                                    <tr class="hover:bg-[var(--accent-soft)] transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <p class="font-bold text-sm text-[var(--text-primary)]">{{ $attempt->quiz->title ?? 'Assessment' }}</p>
                                            <p class="text-xs text-[var(--text-secondary)] font-mono">{{ $attempt->quiz->subject->code ?? 'General' }}</p>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-base font-black font-mono {{ $attempt->is_passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                {{ number_format($attempt->score, 1) }}%
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono {{ $attempt->is_passed ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300' }}">
                                                {{ $attempt->is_passed ? 'PASSED (' . $attempt->grade_letter . ')' : 'FAILED (' . $attempt->grade_letter . ')' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono font-bold text-[var(--text-primary)]">
                                            {{ $attempt->total_points_earned }} / {{ $attempt->quiz->total_points ?? 0 }} Pts
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                            {{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y') : 'N/A' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('student.quizzes.result', $attempt->id) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-lg transition">
                                                <span>View Transcript</span>
                                                <i class="fas fa-chevron-right ml-1 text-[9px]"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
