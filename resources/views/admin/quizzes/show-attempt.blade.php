@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.quizzes.index') }}" class="hover:text-blue-500 transition">Quizzes</a>
                <span>/</span>
                <a href="{{ route('admin.quizzes.attempts', $quiz) }}" class="hover:text-blue-500 transition">Submissions</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">Attempt Audit</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Candidate Assessment Audit: {{ $attempt->student->user->name ?? 'Candidate' }}
            </h2>
        </div>

        <a href="{{ route('admin.quizzes.attempts', $quiz) }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-1.5"></i>
            Back to Submissions
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- ========== CANDIDATE RESULT SCORECARD (BENTO GLASS) ========== -->
        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-3xl p-6 sm:p-8 border border-[var(--border-color)] shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
                <!-- Score & Grade -->
                <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Final Score</p>
                    <p class="text-3xl font-black font-mono mt-1 {{ $attempt->is_passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ number_format($attempt->score, 1) }}%
                    </p>
                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs font-bold {{ $attempt->is_passed ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $attempt->is_passed ? 'PASSED · GRADE ' . $attempt->grade_letter : 'FAILED · GRADE ' . $attempt->grade_letter }}
                        </span>
                        @if($attempt->needs_manual_grading)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                <i class="fas fa-edit"></i> Needs Grading
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Points Earned -->
                <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Points Tally</p>
                    <p class="text-3xl font-black font-mono text-[var(--text-primary)] mt-1">
                        {{ $attempt->total_points_earned }} <span class="text-base text-[var(--text-muted)] font-normal">/ {{ $questions->sum('points') }}</span>
                    </p>
                    <span class="text-xs text-[var(--text-secondary)] mt-1 block">Passing Score: {{ $quiz->passing_score }}%</span>
                </div>

                <!-- Time Spent & Submission Mode -->
                <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Time Spent</p>
                    <p class="text-2xl font-black font-mono text-[var(--text-primary)] mt-1">
                        {{ $attempt->formatted_time_taken }}
                    </p>
                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs text-[var(--text-secondary)]">Limit: {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . 'm' : 'Untimed' }}</span>
                        @if($attempt->isTimeExpired())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                <i class="fas fa-hourglass-end text-[9px]"></i> Auto-Expired
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                <i class="fas fa-check text-[9px]"></i> On Time
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Candidate Details -->
                <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Candidate</p>
                    <p class="text-sm font-bold text-[var(--text-primary)] mt-1 truncate">
                        {{ $attempt->student->user->name ?? 'Candidate' }}
                    </p>
                    <span class="text-xs text-[var(--text-secondary)] font-mono block">{{ $attempt->student->reg_number ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- ========== QUESTION BY QUESTION AUDIT ========== -->
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-[var(--text-primary)] flex items-center justify-between">
                <span>Detailed Question-by-Question Audit</span>
                <span class="text-xs text-[var(--text-secondary)] font-mono">{{ $questions->count() }} items audited</span>
            </h3>

            @foreach($questions as $index => $question)
                @php
                    $ans = $answers->get($question->id);
                    $selectedOpt = $ans?->selectedOption;
                    $isEssay = in_array($question->type, ['essay', 'short_answer']);
                    $isCorrect = $ans?->is_correct ?? false;
                    $isGraded = $ans?->is_graded ?? true;
                @endphp
                <div class="p-6 rounded-2xl bg-[var(--glass-bg)] border border-[var(--border-color)] shadow space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                            <span class="w-8 h-8 rounded-xl font-mono font-bold text-xs flex items-center justify-center {{ $isEssay ? ($isGraded ? 'bg-blue-500/10 text-blue-600 border border-blue-500/20' : 'bg-amber-500/10 text-amber-600 border border-amber-500/20') : ($isCorrect ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20') }}">
                                Q{{ $index + 1 }}
                            </span>
                            @if($isEssay)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono {{ $isGraded ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                    {{ $isGraded ? 'GRADED (+ ' . ($ans->points_earned ?? 0) . ' / ' . $question->points . ' Pts)' : 'NEEDS MANUAL GRADING (Pending)' }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                    {{ $question->type === 'essay' ? 'Essay' : 'Short Answer' }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono {{ $isCorrect ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300' }}">
                                    {{ $isCorrect ? 'CORRECT (+ ' . $question->points . ' Pts)' : 'INCORRECT (0 Pts)' }}
                                </span>
                                @if($question->type === 'true_false')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                        True / False
                                    </span>
                                @endif
                            @endif
                        </div>

                        <span class="text-xs font-mono text-[var(--text-muted)]">Max Points: {{ $question->points }} {{ Str::plural('Pt', $question->points) }}</span>
                    </div>

                    <p class="text-base font-bold text-[var(--text-primary)]">
                        {{ $question->question_text }}
                    </p>

                    @if($isEssay)
                        <!-- Student Essay Response Display & Manual Grading Form -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1 flex items-center gap-1.5">
                                    <i class="fas fa-user-edit text-blue-500"></i> Candidate's Submitted Response:
                                </p>
                                @if(!empty($ans?->text_answer))
                                    <div class="text-sm text-[var(--text-primary)] whitespace-pre-wrap font-sans bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-[var(--border-color)]">
                                        {{ $ans->text_answer }}
                                    </div>
                                @else
                                    <p class="text-xs italic text-[var(--text-muted)]">No response provided by candidate.</p>
                                @endif
                            </div>

                            <!-- Lecturer Manual Grading Form -->
                            <form action="{{ route('admin.quizzes.attempts.grade-question', [$attempt, $question]) }}" method="POST" class="p-4 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40 space-y-3">
                                @csrf
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-blue-900 dark:text-blue-300 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-marker text-blue-600"></i> Lecturer Evaluation & Scoring
                                    </h4>
                                    @if($isGraded)
                                        <span class="text-[11px] font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            Currently: {{ $ans->points_earned }} / {{ $question->points }} pts
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-[var(--text-primary)] mb-1">
                                            Points (0 - {{ $question->points }}) <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="number" name="points_earned" min="0" max="{{ $question->points }}"
                                            value="{{ old('points_earned', $ans?->points_earned ?? 0) }}" required
                                            class="w-full px-3 py-2 rounded-xl text-sm font-bold bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-xs font-bold text-[var(--text-primary)] mb-1">
                                            Feedback / Comments (Optional)
                                        </label>
                                        <input type="text" name="grader_feedback"
                                            value="{{ old('grader_feedback', $ans?->grader_feedback ?? '') }}"
                                            placeholder="Add constructive feedback for the candidate..."
                                            class="w-full px-3 py-2 rounded-xl text-sm bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5">
                                        <i class="fas fa-check-circle"></i>
                                        {{ $isGraded ? 'Update Grade' : 'Submit Grade' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <!-- Options Audit -->
                        <div class="space-y-2">
                            @foreach($question->options as $opt)
                                @php
                                    $isCandidateChoice = ($selectedOpt && $selectedOpt->id === $opt->id);
                                    $isKey = $opt->is_correct;
                                @endphp
                                <div class="p-3 rounded-xl text-xs flex items-center justify-between border {{ $isKey ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-700 dark:text-emerald-300 font-bold' : ($isCandidateChoice ? 'bg-rose-500/10 border-rose-500/30 text-rose-700 dark:text-rose-300 font-bold' : 'bg-[var(--bg-card)] border-[var(--border-color)] text-[var(--text-secondary)]') }}">
                                    <div class="flex items-center space-x-2">
                                        @if($isCandidateChoice)
                                            <i class="fas {{ $isCorrect ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500' }}"></i>
                                        @else
                                            <i class="far fa-circle text-[var(--text-muted)]"></i>
                                        @endif
                                        <span>{{ $opt->option_text }}</span>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        @if($isCandidateChoice)
                                            <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded font-bold {{ $isCorrect ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                                Candidate Choice
                                            </span>
                                        @endif
                                        @if($isKey)
                                            <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded font-bold bg-emerald-600 text-white">
                                                Correct Answer Key
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($question->explanation)
                        <div class="p-3 rounded-xl bg-blue-500/5 border border-blue-500/20 text-xs text-[var(--text-secondary)] flex items-start gap-2">
                            <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                            <div>
                                <span class="font-bold text-[var(--text-primary)]">Pedagogical Explanation:</span>
                                {{ $question->explanation }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </div>
@endsection

