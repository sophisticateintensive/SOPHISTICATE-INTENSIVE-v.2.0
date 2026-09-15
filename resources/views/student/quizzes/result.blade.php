@extends('layouts.student')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pt-2">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center space-x-2 text-xs font-mono text-zinc-400 mb-1">
                    <a href="{{ route('student.quizzes.index') }}" class="hover:text-blue-500 transition">Assessments</a>
                    <span>/</span>
                    <span class="text-zinc-700 dark:text-zinc-300 font-bold">Performance Transcript</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ $quiz->title }}
                </h1>
            </div>

            <div class="flex items-center space-x-2.5">
                @if($quiz->canStudentAttempt(Auth::user()->student->id))
                    <a href="{{ route('student.quizzes.take', $quiz->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-bold rounded-full shadow hover:scale-105 transition">
                        <i class="fas fa-redo-alt mr-1.5"></i>
                        Retake Assessment ({{ $remainingAttempts }} left)
                    </a>
                @endif
                <a href="{{ route('student.quizzes.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-full border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                    <i class="fas fa-arrow-left mr-1.5"></i>
                    All Quizzes
                </a>
            </div>
        </div>

        <!-- Top Time-Expired Alert (if applicable) -->
        @if($attempt->isTimeExpired())
            <div class="flex items-center gap-3 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-300">
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 flex items-center justify-center flex-shrink-0 text-base">
                    <i class="fas fa-hourglass-end"></i>
                </div>
                <div>
                    <div class="font-bold text-sm">Automatic Submission &bull; Time Expired</div>
                    <div class="text-xs opacity-90">This assessment was automatically submitted when your timer reached 00:00. All selected answers were safely captured and graded.</div>
                </div>
            </div>
        @endif

        <!-- ========== CYBER BENTO SCORECARD ========== -->
        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-3xl p-6 sm:p-8 border border-[var(--border-color)] shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-br {{ $attempt->is_passed ? 'from-emerald-500/15 to-teal-500/10' : 'from-rose-500/15 to-orange-500/10' }} rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 border-b border-[var(--border-color)] pb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-2xl {{ $attempt->is_passed ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' }} flex items-center justify-center text-3xl shadow-lg">
                            <i class="fas {{ $attempt->is_passed ? 'fa-award' : 'fa-times-circle' }}"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold font-mono {{ $attempt->is_passed ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300' }}">
                                    {{ $attempt->is_passed ? 'ASSESSMENT PASSED' : 'BENCHMARK NOT MET' }}
                                </span>
                                @if($attempt->isTimeExpired())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                        <i class="fas fa-stopwatch text-[10px]"></i> Time Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                        <i class="fas fa-check text-[10px]"></i> On Time
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-2xl font-black text-[var(--text-primary)] mt-1.5">
                                {{ $attempt->is_passed ? 'Congratulations, Great Job!' : 'Keep Practicing & Review Topics' }}
                            </h3>
                            <p class="text-xs text-[var(--text-secondary)] mt-0.5">
                                Submitted on {{ $attempt->submitted_at ? $attempt->submitted_at->format('F d, Y · h:i A') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="text-center sm:text-right">
                        <span class="text-xs text-[var(--text-muted)] uppercase tracking-wider font-mono block">Final Grade</span>
                        <span class="text-5xl font-black font-mono {{ $attempt->is_passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ number_format($attempt->score, 1) }}%
                        </span>
                        <span class="text-xs font-bold text-[var(--text-secondary)] block mt-0.5 font-mono">Grade Letter: {{ $attempt->grade_letter }}</span>
                    </div>
                </div>

                <!-- Bento Metrics Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6">
                    <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                        <span class="text-[10px] uppercase font-bold text-[var(--text-muted)] block">Points Tally</span>
                        <span class="text-2xl font-black font-mono text-[var(--text-primary)] mt-1 block">
                            {{ $attempt->total_points_earned }} <span class="text-xs text-[var(--text-muted)] font-normal">/ {{ $questions->sum('points') }}</span>
                        </span>
                        <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">Passing: {{ $quiz->passing_score }}%</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                        <span class="text-[10px] uppercase font-bold text-[var(--text-muted)] block">Time Spent</span>
                        <span class="text-2xl font-black font-mono text-[var(--text-primary)] mt-1 block">
                            {{ $attempt->formatted_time_taken }}
                        </span>
                        <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">Limit: {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . 'm' : 'Untimed' }}</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                        <span class="text-[10px] uppercase font-bold text-[var(--text-muted)] block">Grading Status</span>
                        @if($attempt->needs_manual_grading)
                            <span class="text-sm font-bold font-mono text-amber-600 dark:text-amber-400 mt-1 block flex items-center gap-1">
                                <i class="fas fa-edit text-xs"></i> Essay Pending
                            </span>
                            <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">Lecturer review needed</span>
                        @else
                            <span class="text-2xl font-black font-mono text-emerald-600 dark:text-emerald-400 mt-1 block">
                                {{ $attempt->answers->where('is_correct', true)->count() }} <span class="text-xs text-[var(--text-muted)] font-normal">/ {{ $questions->count() }}</span>
                            </span>
                            <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">Questions Correct</span>
                        @endif
                    </div>

                    <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                        <span class="text-[10px] uppercase font-bold text-[var(--text-muted)] block">Attempts Left</span>
                        <span class="text-2xl font-black font-mono text-indigo-500 mt-1 block">
                            {{ $remainingAttempts > 100 ? 'Unlimited' : $remainingAttempts }}
                        </span>
                        <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">Of {{ $quiz->max_attempts }} allowed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== QUESTION REVIEW & EXPLANATIONS ========== -->
        @if($quiz->show_correct_answers)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[var(--text-primary)] flex items-center gap-2">
                        <i class="fas fa-book-open text-blue-500"></i>
                        <span>Conceptual Question Review & Explanations</span>
                    </h3>
                    <span class="text-xs text-[var(--text-secondary)] font-mono">{{ $questions->count() }} questions</span>
                </div>

                <div class="space-y-4">
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
                                            {{ $isGraded ? 'GRADED (+ ' . ($ans->points_earned ?? 0) . ' / ' . $question->points . ' Pts)' : 'AWAITING LECTURER REVIEW' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                            {{ $question->type === 'essay' ? 'Written Essay' : 'Short Answer' }}
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

                                <span class="text-xs font-mono text-[var(--text-muted)]">{{ $question->points }} {{ Str::plural('Pt', $question->points) }}</span>
                            </div>

                            <p class="text-base font-bold text-[var(--text-primary)]">
                                {{ $question->question_text }}
                            </p>

                            @if($isEssay)
                                <!-- Student Essay Response & Lecturer Feedback -->
                                <div class="space-y-3">
                                    <div class="p-4 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1">
                                            Your Submitted Answer:
                                        </p>
                                        @if(!empty($ans?->text_answer))
                                            <div class="text-sm text-[var(--text-primary)] whitespace-pre-wrap font-sans bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-[var(--border-color)]">
                                                {{ $ans->text_answer }}
                                            </div>
                                        @else
                                            <p class="text-xs italic text-[var(--text-muted)]">No response provided.</p>
                                        @endif
                                    </div>

                                    @if($isGraded && !empty($ans?->grader_feedback))
                                        <div class="p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/30 text-xs text-[var(--text-primary)] space-y-1">
                                            <div class="font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1.5">
                                                <i class="fas fa-comment-dots"></i> Lecturer Feedback:
                                            </div>
                                            <div class="text-sm font-sans italic">
                                                "{{ $ans->grader_feedback }}"
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <!-- Options List -->
                                <div class="space-y-2">
                                    @foreach($question->options as $opt)
                                        @php
                                            $isMyChoice = ($selectedOpt && $selectedOpt->id === $opt->id);
                                            $isCorrectKey = $opt->is_correct;
                                        @endphp
                                        <div class="p-3 rounded-xl text-xs flex items-center justify-between border {{ $isCorrectKey ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-700 dark:text-emerald-300 font-bold' : ($isMyChoice ? 'bg-rose-500/10 border-rose-500/30 text-rose-700 dark:text-rose-300 font-bold' : 'bg-[var(--bg-card)] border-[var(--border-color)] text-[var(--text-secondary)]') }}">
                                            <div class="flex items-center space-x-2">
                                                @if($isMyChoice)
                                                    <i class="fas {{ $isCorrect ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500' }}"></i>
                                                @else
                                                    <i class="far fa-circle text-[var(--text-muted)]"></i>
                                                @endif
                                                <span>{{ $opt->option_text }}</span>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                @if($isMyChoice)
                                                    <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded font-bold {{ $isCorrect ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                                        Your Choice
                                                    </span>
                                                @endif
                                                @if($isCorrectKey && !$isCorrect)
                                                    <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded font-bold bg-emerald-600 text-white">
                                                        Correct Key
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
                                        <span class="font-bold text-[var(--text-primary)]">Explanation Rationale:</span>
                                        {{ $question->explanation }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection

