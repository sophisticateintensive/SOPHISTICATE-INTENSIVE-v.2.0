@extends('layouts.student')

@section('content')
    @php
        $totalQuestions = $questions->count();
    @endphp

    <div x-data="examStudio()" x-init="initExam()" class="space-y-6 max-w-6xl mx-auto pt-2">

        <!-- Title Bar -->
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <div>
                    <h2 class="font-black text-xl text-zinc-900 dark:text-white">
                        {{ $quiz->title }}
                    </h2>
                    <p class="text-xs text-zinc-400 font-mono">{{ $quiz->subject->code ?? 'COURSE ASSESSMENT' }} &bull; {{ $quiz->total_points }} Total Points</p>
                </div>
            </div>

            <div id="autosaveIndicator" class="flex items-center space-x-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-mono">
                <i class="fas fa-check-circle text-[11px]"></i>
                <span class="hidden sm:inline font-bold">Progress Synced</span>
            </div>
        </div>

        <!-- ========== FLOATING DYNAMIC ISLAND HUD ========== -->
        <div class="sticky top-20 z-40 bg-[var(--bg-card)]/90 backdrop-blur-md rounded-2xl p-4 border border-[var(--border-color)] shadow-xl flex items-center justify-between gap-4">
            <!-- Progress Bar Info -->
            <div class="flex-1">
                <div class="flex items-center justify-between text-xs font-mono mb-1.5">
                    <span class="text-[var(--text-secondary)]">
                        Question <strong class="text-[var(--text-primary)]" x-text="currentIndex + 1"></strong> of {{ $totalQuestions }}
                    </span>
                    <span class="text-blue-500 font-bold" x-text="Math.round((totalAnsweredCount() / {{ $totalQuestions }}) * 100) + '% Answered'"></span>
                </div>
                <div class="w-full h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-300"
                         :style="'width: ' + ((totalAnsweredCount() / {{ $totalQuestions }}) * 100) + '%'"></div>
                </div>
            </div>

            <!-- Modern Circular Notched Dial Timer (Matching Reference Design) -->
            @if($quiz->time_limit_minutes)
                <div class="flex flex-col items-center select-none flex-shrink-0">
                    <!-- Circular Chronograph Dial -->
                    <div class="relative w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center">
                        <svg class="w-full h-full" viewBox="0 0 120 120">
                            <defs>
                                <!-- Dynamic mask that reveals active ticks clockwise -->
                                <mask id="hudTimerMask">
                                    <circle cx="60" cy="60" r="48" fill="none" stroke="white" stroke-width="14"
                                            stroke-dasharray="301.59"
                                            :stroke-dashoffset="301.59 * (1 - (timerSeconds / Math.max(totalSeconds, 1)))"
                                            transform="rotate(-90 60 60)"
                                            class="transition-all duration-1000 ease-linear" />
                                </mask>
                            </defs>

                            <!-- Inactive Ticks (Dim background notches: visible in both light & dark mode) -->
                            <circle cx="60" cy="60" r="48" fill="none"
                                    class="text-zinc-200 dark:text-zinc-700/60"
                                    stroke="currentColor"
                                    stroke-width="6"
                                    stroke-dasharray="2.4 2.6265"
                                    stroke-linecap="butt"
                                    transform="rotate(-90 60 60)" />

                            <!-- Active Ticks (Adaptive colors: blue/white in normal, amber/red in warning) -->
                            <circle cx="60" cy="60" r="48" fill="none"
                                    :class="timerSeconds < 60 ? 'text-rose-500' : (timerSeconds < 300 ? 'text-amber-500' : 'text-blue-600 dark:text-blue-400')"
                                    stroke="currentColor"
                                    stroke-width="6"
                                    stroke-dasharray="2.4 2.6265"
                                    stroke-linecap="butt"
                                    mask="url(#hudTimerMask)"
                                    transform="rotate(-90 60 60)" />
                        </svg>

                        <!-- Centered Ultra-Thin Number Display -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-xl sm:text-2xl font-light tracking-tight leading-none font-sans"
                                  style="font-family: 'Space Grotesk', system-ui, -apple-system, sans-serif;"
                                  :class="timerSeconds < 60 ? 'text-rose-600 dark:text-rose-400 animate-pulse' : (timerSeconds < 300 ? 'text-amber-600 dark:text-amber-300' : 'text-zinc-900 dark:text-white')"
                                  x-text="formatDisplayMinutes()">35</span>
                            <span class="text-[9px] font-mono text-zinc-500 dark:text-zinc-400 tracking-tight leading-none mt-0.5"
                                  x-text="formatSeconds() + 's'">00s</span>
                        </div>
                    </div>

                    <!-- Label Below Dial -->
                    <span class="text-[9px] tracking-[0.25em] font-bold text-zinc-500 dark:text-zinc-400 uppercase mt-1"
                          style="font-family: 'Space Grotesk', system-ui, sans-serif;">TIMER</span>
                </div>
            @endif

            <!-- Quick Finish CTA -->
            <button type="button" @click="showConfirmModal = true"
                    class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold rounded-xl shadow hover:shadow-lg transition flex items-center gap-1.5 flex-shrink-0">
                <i class="fas fa-paper-plane text-[10px]"></i>
                <span class="hidden sm:inline">Submit Test</span>
            </button>
        </div>

        <!-- ========== MAIN VIEWPORT & PALETTE GRID ========== -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- LEFT: ACTIVE QUESTION VIEWPORT (3 COLS) -->
            <div class="lg:col-span-3 space-y-6">
                <form id="quizForm" action="{{ route('student.quizzes.submit', $quiz->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="time_expired" id="timeExpiredInput" value="0">

                    @foreach($questions as $index => $q)
                        <div x-show="currentIndex === {{ $index }}"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-3xl p-6 sm:p-8 border border-[var(--border-color)] shadow-xl space-y-6">

                            <!-- Question Header -->
                            <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-4">
                                <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                                    <span class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 font-bold text-xs flex items-center justify-center font-mono border border-blue-500/20">
                                        Q{{ $index + 1 }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono bg-zinc-100 dark:bg-zinc-800 text-[var(--text-secondary)]">
                                        {{ $q->points }} {{ Str::plural('Point', $q->points) }}
                                    </span>
                                    @if($q->type === 'essay' || $q->type === 'short_answer')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                            {{ $q->type === 'essay' ? 'Written Essay' : 'Short Answer' }}
                                        </span>
                                    @elseif($q->type === 'true_false')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                            True / False
                                        </span>
                                    @endif
                                </div>

                                <!-- Flag for Review Toggle -->
                                <button type="button" @click="toggleFlag({{ $q->id }})"
                                        :class="flags[{{ $q->id }}] ? 'bg-amber-500/10 border-amber-500/40 text-amber-600' : 'bg-[var(--bg-card)] border-[var(--border-color)] text-[var(--text-secondary)]'"
                                        class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5">
                                    <i class="fas fa-flag text-[10px]"></i>
                                    <span x-text="flags[{{ $q->id }}] ? 'Flagged' : 'Flag for Review'"></span>
                                </button>
                            </div>

                            <!-- Question Statement -->
                            <div class="py-2">
                                <h3 class="text-base sm:text-lg font-bold text-[var(--text-primary)] leading-relaxed">
                                    {{ $q->question_text }}
                                </h3>
                            </div>

                            @if($q->type === 'essay' || $q->type === 'short_answer')
                                <!-- Written Text / Essay Area -->
                                <div class="space-y-3 pt-2">
                                    <label class="block text-xs font-bold text-[var(--text-secondary)]">
                                        Your Written Response:
                                    </label>
                                    <textarea
                                        name="text_answers[{{ $q->id }}]"
                                        x-model="textAnswers[{{ $q->id }}]"
                                        @input.debounce.500ms="saveTextAnswer({{ $q->id }})"
                                        rows="{{ $q->type === 'essay' ? '7' : '4' }}"
                                        placeholder="Type your complete response or essay here. Changes auto-save in the background..."
                                        class="w-full p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)] text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:outline-none transition leading-relaxed"></textarea>
                                    
                                    <div class="flex items-center justify-between text-[11px] text-[var(--text-muted)] font-mono">
                                        <span>
                                            Words: <strong class="text-[var(--text-primary)]" x-text="(textAnswers[{{ $q->id }}] || '').trim().split(/\s+/).filter(Boolean).length">0</strong>
                                        </span>
                                        <span>
                                            Characters: <strong class="text-[var(--text-primary)]" x-text="(textAnswers[{{ $q->id }}] || '').length">0</strong>
                                        </span>
                                    </div>
                                </div>
                            @else
                                <!-- Choices Options (MCQ / True-False) -->
                                <div class="space-y-3 pt-2">
                                    @foreach($q->options as $optIndex => $opt)
                                        <label class="flex items-center p-4 rounded-2xl border cursor-pointer transition-all duration-200 select-none group"
                                               :class="answers[{{ $q->id }}] == {{ $opt->id }} ? 'bg-blue-500/10 border-blue-500 ring-2 ring-blue-500/20 text-blue-700 dark:text-blue-300 font-bold shadow-md' : 'bg-[var(--bg-card)] border-[var(--border-color)] hover:border-blue-500/40 text-[var(--text-primary)]'">
                                            <input type="radio"
                                                   name="answers[{{ $q->id }}]"
                                                   value="{{ $opt->id }}"
                                                   x-model="answers[{{ $q->id }}]"
                                                   @change="saveAnswer({{ $q->id }}, {{ $opt->id }})"
                                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <span class="w-8 h-8 rounded-xl font-mono text-xs font-bold flex items-center justify-center ml-3 mr-3 bg-[var(--glass-bg)] border border-[var(--border-color)] group-hover:border-blue-500/30 transition">
                                                {{ chr(65 + $optIndex) }}
                                            </span>
                                            <span class="text-sm flex-1">{{ $opt->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Viewport Navigation Bar -->
                            <div class="pt-6 border-t border-[var(--border-color)] flex items-center justify-between">
                                <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0"
                                        class="px-5 py-2.5 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-xs font-bold text-[var(--text-primary)] hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-30 disabled:pointer-events-none transition flex items-center gap-1.5">
                                    <i class="fas fa-chevron-left text-[9px]"></i>
                                    <span>Previous</span>
                                </button>

                                <div class="flex items-center space-x-2">
                                    <template x-if="currentIndex < {{ $totalQuestions - 1 }}">
                                        <button type="button" @click="nextQuestion()"
                                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold shadow hover:scale-105 transition flex items-center gap-1.5">
                                            <span>Next</span>
                                            <i class="fas fa-chevron-right text-[9px]"></i>
                                        </button>
                                    </template>

                                    <template x-if="currentIndex === {{ $totalQuestions - 1 }}">
                                        <button type="button" @click="showConfirmModal = true"
                                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-bold shadow hover:scale-105 transition flex items-center gap-1.5">
                                            <span>Review & Submit</span>
                                            <i class="fas fa-check text-[9px]"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </form>
            </div>

            <!-- RIGHT: QUESTION NAVIGATOR PALETTE (1 COL) -->
            <div class="space-y-6">
                <!-- Dedicated Minimalist Dial Timer Widget (Matching Uploaded Image) -->
                @if($quiz->time_limit_minutes)
                    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-3xl p-6 border border-[var(--border-color)] shadow-xl flex flex-col items-center justify-center text-center select-none relative overflow-hidden">
                        
                        <!-- 60-Notch Chronograph Dial -->
                        <div class="relative w-32 h-32 flex items-center justify-center my-1">
                            <svg class="w-full h-full" viewBox="0 0 120 120">
                                <defs>
                                    <mask id="sidebarTimerMask">
                                        <circle cx="60" cy="60" r="48" fill="none" stroke="white" stroke-width="14"
                                                stroke-dasharray="301.59"
                                                :stroke-dashoffset="301.59 * (1 - (timerSeconds / Math.max(totalSeconds, 1)))"
                                                transform="rotate(-90 60 60)"
                                                class="transition-all duration-1000 ease-linear" />
                                    </mask>
                                </defs>

                                <!-- Inactive Ticks (Dim background notches: visible in both light & dark mode) -->
                                <circle cx="60" cy="60" r="48" fill="none"
                                        class="text-zinc-200 dark:text-zinc-700/60"
                                        stroke="currentColor"
                                        stroke-width="6"
                                        stroke-dasharray="2.4 2.6265"
                                        stroke-linecap="butt"
                                        transform="rotate(-90 60 60)" />

                                <!-- Active Ticks (Adaptive colors: blue/white in normal, amber/red in warning) -->
                                <circle cx="60" cy="60" r="48" fill="none"
                                        :class="timerSeconds < 60 ? 'text-rose-500' : (timerSeconds < 300 ? 'text-amber-500' : 'text-blue-600 dark:text-blue-400')"
                                        stroke="currentColor"
                                        stroke-width="6"
                                        stroke-dasharray="2.4 2.6265"
                                        stroke-linecap="butt"
                                        mask="url(#sidebarTimerMask)"
                                        transform="rotate(-90 60 60)" />
                            </svg>

                            <!-- Centered Thin Minimalist Numbers -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-3xl sm:text-4xl font-extralight tracking-tight leading-none font-sans"
                                      style="font-family: 'Space Grotesk', system-ui, -apple-system, sans-serif;"
                                      :class="timerSeconds < 60 ? 'text-rose-600 dark:text-rose-400 animate-pulse' : (timerSeconds < 300 ? 'text-amber-600 dark:text-amber-300' : 'text-zinc-900 dark:text-white')"
                                      x-text="formatDisplayMinutes()">35</span>
                                <span class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400 tracking-tight leading-none mt-1"
                                      x-text="timerSeconds < 60 ? 'sec left' : ':' + formatSeconds()">:00</span>
                            </div>
                        </div>

                        <!-- Sleek TIMER Label -->
                        <span class="text-[11px] tracking-[0.3em] font-bold text-zinc-500 dark:text-zinc-400 uppercase mt-1"
                              style="font-family: 'Space Grotesk', system-ui, sans-serif;">TIMER</span>
                    </div>
                @endif

                <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-3xl p-6 border border-[var(--border-color)] shadow-xl space-y-5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] flex items-center justify-between">
                        <span>Question Navigator</span>
                        <span class="text-blue-500 font-mono" x-text="totalAnsweredCount() + ' / {{ $totalQuestions }}'"></span>
                    </h4>

                    <!-- Palette Grid -->
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($questions as $index => $q)
                            <button type="button" @click="jumpTo({{ $index }})"
                                    class="relative h-10 rounded-xl font-mono text-xs font-bold flex items-center justify-center transition-all duration-200"
                                    :class="{
                                        'ring-2 ring-blue-500 border-2 border-blue-500 scale-105 z-10': currentIndex === {{ $index }},
                                        'bg-emerald-500 text-white shadow-sm': isAnswered({{ $q->id }}) && currentIndex !== {{ $index }},
                                        'bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)] hover:border-blue-500/50': !isAnswered({{ $q->id }}) && currentIndex !== {{ $index }}
                                    }">
                                <span>{{ $index + 1 }}</span>

                                <!-- Flag Indicator Dot -->
                                <template x-if="flags[{{ $q->id }}]">
                                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-400 border-2 border-[var(--bg-card)]"></span>
                                </template>
                            </button>
                        @endforeach
                    </div>

                    <!-- Legend -->
                    <div class="space-y-1.5 pt-3 border-t border-[var(--border-color)] text-[11px] text-[var(--text-secondary)] font-mono">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded bg-emerald-500"></span>
                            <span>Answered</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded bg-[var(--bg-card)] border border-[var(--border-color)]"></span>
                            <span>Unattempted</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span>Flagged for Review</span>
                        </div>
                    </div>

                    <button type="button" @click="showConfirmModal = true"
                            class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold rounded-xl shadow hover:shadow-lg transition">
                        Finish & Finalize Exam
                    </button>
                </div>
            </div>

        </div>

        <!-- ========== SUBMISSION CONFIRMATION MODAL ========== -->
        <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-2xl mx-auto">
                    <i class="fas fa-shield-alt"></i>
                </div>

                <h3 class="text-xl font-bold text-[var(--text-primary)] text-center">Ready to Submit Assessment?</h3>

                <p class="text-xs text-[var(--text-secondary)] text-center leading-relaxed">
                    You have answered <strong class="text-[var(--text-primary)] font-mono" x-text="totalAnsweredCount()"></strong> of <strong class="text-[var(--text-primary)] font-mono">{{ $totalQuestions }}</strong> questions.
                    <span x-show="totalAnsweredCount() < {{ $totalQuestions }}" class="text-amber-500 font-bold block mt-1">
                        ⚠️ You have <span x-text="{{ $totalQuestions }} - totalAnsweredCount()"></span> unanswered questions!
                    </span>
                </p>

                <div class="flex space-x-3 pt-2">
                    <button type="button" @click="showConfirmModal = false" class="flex-1 py-3 rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs font-bold text-[var(--text-primary)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Keep Working
                    </button>

                    <button type="button" @click="submitFinalForm()" :disabled="isSubmitting || isTimeExpired" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-bold shadow hover:shadow-lg transition flex items-center justify-center gap-1.5">
                        <i class="fas fa-check" x-show="!isSubmitting"></i>
                        <i class="fas fa-spinner fa-spin" x-show="isSubmitting" style="display: none;"></i>
                        <span x-text="isSubmitting ? 'Submitting...' : 'Confirm Submit'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========== TIME EXPIRED AUTO-SUBMISSION MODAL OVERLAY ========== -->
        <div x-show="isTimeExpired" class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4" style="display: none;">
            <div class="bg-zinc-900 border border-red-500/40 rounded-3xl p-8 max-w-md w-full shadow-2xl text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/40 flex items-center justify-center text-3xl mx-auto animate-bounce">
                    <i class="fas fa-hourglass-end"></i>
                </div>
                <h3 class="text-2xl font-black text-white">Time Has Expired!</h3>
                <p class="text-xs text-zinc-300 leading-relaxed">
                    The allotted time for this assessment has ended. All your selected answers have been saved and are now being automatically submitted for grading.
                </p>
                <div class="flex items-center justify-center gap-2 text-sm font-mono text-amber-400 font-bold pt-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Finalizing assessment & scoring...</span>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    function examStudio() {
        const questionIds = @json($questions->pluck('id'));
        return {
            currentIndex: 0,
            showConfirmModal: false,
            isTimeExpired: false,
            isSubmitting: false,
            timerInterval: null,
            totalSeconds: {{ max(1, ($quiz->time_limit_minutes ?? 0) * 60) }},
            timerSeconds: {{ $remainingSeconds ?? (($quiz->time_limit_minutes ?? 0) * 60) }},
            answers: {
                @foreach($savedAnswers as $qId => $ans)
                    @if($ans->selected_option_id)
                        {{ $qId }}: {{ $ans->selected_option_id }},
                    @endif
                @endforeach
            },
            textAnswers: {
                @foreach($savedAnswers as $qId => $ans)
                    @if($ans->text_answer !== null)
                        {{ $qId }}: @json($ans->text_answer),
                    @endif
                @endforeach
            },
            flags: {},

            isAnswered(qId) {
                const hasMcq = this.answers[qId] !== undefined && this.answers[qId] !== null && this.answers[qId] !== '';
                const hasText = this.textAnswers[qId] !== undefined && this.textAnswers[qId] !== null && String(this.textAnswers[qId]).trim() !== '';
                return hasMcq || hasText;
            },

            totalAnsweredCount() {
                let count = 0;
                questionIds.forEach(id => {
                    if (this.isAnswered(id)) count++;
                });
                return count;
            },

            initExam() {
                @if($quiz->time_limit_minutes)
                    // If server reported time is already 0 on page load/re-entry
                    if (this.timerSeconds <= 0) {
                        this.handleTimeExpiration();
                        return;
                    }

                    this.timerInterval = setInterval(() => {
                        if (this.timerSeconds > 0) {
                            this.timerSeconds--;
                        }
                        
                        if (this.timerSeconds <= 0) {
                            clearInterval(this.timerInterval);
                            this.handleTimeExpiration();
                        }
                    }, 1000);
                @endif
            },

            handleTimeExpiration() {
                if (this.isTimeExpired) return;
                this.isTimeExpired = true;
                this.timerSeconds = 0;
                this.showConfirmModal = false;

                // Disable all form inputs to prevent tampering once time is up
                const inputs = document.querySelectorAll('#quizForm input, #quizForm textarea, #quizForm button');
                inputs.forEach(el => el.setAttribute('readonly', 'true'));

                // Set expired flag
                const expiredInput = document.getElementById('timeExpiredInput');
                if (expiredInput) {
                    expiredInput.value = '1';
                }

                // Automatic submission after visual cue
                setTimeout(() => {
                    document.getElementById('quizForm').submit();
                }, 1000);
            },

            formatDisplayMinutes() {
                const mins = Math.floor(this.timerSeconds / 60);
                if (mins === 0 && this.timerSeconds > 0) {
                    return this.formatSeconds();
                }
                return mins < 10 ? `0${mins}` : `${mins}`;
            },

            formatMinutes() {
                const mins = Math.floor(this.timerSeconds / 60);
                return mins < 10 ? `0${mins}` : `${mins}`;
            },

            formatSeconds() {
                const secs = this.timerSeconds % 60;
                return secs < 10 ? `0${secs}` : `${secs}`;
            },

            formatTimer() {
                return `${this.formatMinutes()}:${this.formatSeconds()}`;
            },

            nextQuestion() {
                if (this.currentIndex < {{ $totalQuestions - 1 }}) {
                    this.currentIndex++;
                }
            },

            prevQuestion() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                }
            },

            jumpTo(index) {
                this.currentIndex = index;
            },

            toggleFlag(qId) {
                this.flags[qId] = !this.flags[qId];
            },

            saveAnswer(qId, optId) {
                if (this.isTimeExpired) return;

                const indicator = document.getElementById('autosaveIndicator');
                if (indicator) {
                    indicator.innerHTML = '<i class="fas fa-spinner fa-spin text-[11px] text-amber-500"></i> <span class="hidden sm:inline">Saving...</span>';
                }

                fetch('{{ route("student.quizzes.autosave", $quiz->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        question_id: qId,
                        selected_option_id: optId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.expired) {
                        this.handleTimeExpiration();
                        return;
                    }
                    if (indicator) {
                        indicator.innerHTML = '<i class="fas fa-check-circle text-[11px] text-emerald-500"></i> <span class="hidden sm:inline">Saved ' + (data.saved_at || '') + '</span>';
                    }
                })
                .catch(err => {
                    if (indicator) {
                        indicator.innerHTML = '<i class="fas fa-exclamation-triangle text-[11px] text-red-500"></i> <span class="hidden sm:inline">Sync Pending</span>';
                    }
                });
            },

            saveTextAnswer(qId) {
                if (this.isTimeExpired) return;

                const indicator = document.getElementById('autosaveIndicator');
                if (indicator) {
                    indicator.innerHTML = '<i class="fas fa-spinner fa-spin text-[11px] text-amber-500"></i> <span class="hidden sm:inline">Saving text...</span>';
                }

                fetch('{{ route("student.quizzes.autosave", $quiz->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        question_id: qId,
                        text_answer: this.textAnswers[qId] || ''
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.expired) {
                        this.handleTimeExpiration();
                        return;
                    }
                    if (indicator) {
                        indicator.innerHTML = '<i class="fas fa-check-circle text-[11px] text-emerald-500"></i> <span class="hidden sm:inline">Saved ' + (data.saved_at || '') + '</span>';
                    }
                })
                .catch(err => {
                    if (indicator) {
                        indicator.innerHTML = '<i class="fas fa-exclamation-triangle text-[11px] text-red-500"></i> <span class="hidden sm:inline">Sync Pending</span>';
                    }
                });
            },

            submitFinalForm() {
                if (this.isSubmitting) return;
                this.isSubmitting = true;
                document.getElementById('quizForm').submit();
            }
        };
    }
</script>
@endpush

