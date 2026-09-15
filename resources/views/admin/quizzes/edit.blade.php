@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.quizzes.index') }}" class="hover:text-blue-500 transition">Quizzes</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">Assessment Studio</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                {{ $quiz->title }}
            </h2>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.quizzes.attempts', $quiz) }}"
                class="inline-flex items-center px-3.5 py-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 border border-purple-500/20 text-xs font-bold rounded-xl transition">
                <i class="fas fa-chart-line mr-1.5"></i>
                Submissions ({{ $quiz->attempts()->count() }})
            </a>
            <a href="{{ route('admin.quizzes.add-question', $quiz) }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all">
                <i class="fas fa-plus mr-1.5"></i>
                Add Question
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-8">

        <!-- ========== HERO / STATS SECTION (STUDENT MANAGEMENT DESIGN) ========== -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 shadow-2xl">
            <!-- Animated blobs -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                        <span class="w-2 h-2 {{ $quiz->is_active ? 'bg-emerald-400' : 'bg-amber-400' }} rounded-full animate-pulse"></span>
                        Status: {{ $quiz->is_active ? 'Live & Published' : 'Draft Mode' }} &bull; {{ $quiz->subject->code ?? 'General' }}
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $quiz->title }}</h1>
                    <p class="text-blue-100/80 mt-1">
                        {{ $quiz->term->term_name ?? 'Semester' }} &bull;
                        {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' Mins Duration' : 'Untimed' }} &bull;
                        {{ $quiz->max_attempts }} Attempt Limit
                    </p>
                </div>

                <!-- Floating Stats Bento -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                        <div class="text-2xl font-bold text-white">{{ $questions->count() }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Questions</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                        <div class="text-2xl font-bold text-emerald-300">{{ $quiz->total_points }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Total Pts</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                        <div class="text-2xl font-bold text-amber-300">{{ $quiz->passing_score }}%</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Pass Mark</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                        <div class="text-2xl font-bold text-purple-300">{{ $quiz->attempts()->count() }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Submissions</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT COLUMN: QUESTION STUDIO (2 COLS) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white tracking-wide">Question Inventory</h3>
                                <p class="text-xs text-blue-200 mt-0.5">{{ $questions->count() }} items configured &bull; {{ $quiz->total_points }} Points available</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.quizzes.add-question', $quiz) }}"
                               class="px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center gap-1">
                                <i class="fas fa-plus text-[10px]"></i>
                                <span>Add Question</span>
                            </a>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @forelse($questions as $index => $question)
                            <div class="p-5 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)] shadow-sm hover:border-blue-500/50 transition duration-200 space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center space-x-2.5">
                                        <span class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-500 font-bold text-xs flex items-center justify-center font-mono">
                                            Q{{ $index + 1 }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono {{ $question->type === 'essay' || $question->type === 'short_answer' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : ($question->type === 'true_false' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300') }}">
                                            {{ $question->type === 'essay' || $question->type === 'short_answer' ? 'Written / Essay' : ($question->type === 'true_false' ? 'True / False' : 'Multiple Choice') }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-zinc-100 dark:bg-zinc-800 text-[var(--text-secondary)]">
                                            {{ $question->points }} {{ Str::plural('Pt', $question->points) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center space-x-1.5">
                                        <a href="{{ route('admin.quizzes.edit-question', [$quiz, $question]) }}"
                                           class="p-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 transition" title="Edit Question">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('admin.quizzes.destroy-question', [$quiz, $question]) }}" method="POST" id="delete-q-{{ $question->id }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="openDeleteModal(
                                                'Delete Question?',
                                                'Are you sure you want to remove Question #{{ $index + 1 }}? This will recalculate the quiz total points.',
                                                document.getElementById('delete-q-{{ $question->id }}')
                                            )" class="p-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 transition" title="Delete Question">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <p class="text-sm font-bold text-[var(--text-primary)]">
                                    {{ $question->question_text }}
                                </p>

                                @if($question->type === 'essay' || $question->type === 'short_answer')
                                    <div class="p-3 rounded-xl bg-purple-500/5 border border-purple-500/20 text-xs text-purple-700 dark:text-purple-300 flex items-center gap-2">
                                        <i class="fas fa-pen-nib text-xs"></i>
                                        <span>Candidate answers via free-text essay box (requires lecturer manual grading on submission).</span>
                                    </div>
                                @else
                                    <!-- Options Preview -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                        @foreach($question->options as $opt)
                                            <div class="px-3 py-2 rounded-xl text-xs flex items-center justify-between border {{ $opt->is_correct ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-700 dark:text-emerald-300 font-bold' : 'bg-[var(--glass-bg)] border-[var(--border-color)] text-[var(--text-secondary)]' }}">
                                                <span>{{ $opt->option_text }}</span>
                                                @if($opt->is_correct)
                                                    <span class="text-[10px] font-mono uppercase bg-emerald-500 text-white px-1.5 py-0.2 rounded font-bold">Correct</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($question->explanation)
                                    <div class="p-2.5 rounded-xl bg-blue-500/5 border border-blue-500/20 text-xs text-[var(--text-secondary)] flex items-start gap-2">
                                        <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                                        <div>
                                            <span class="font-bold text-[var(--text-primary)]">Explanation:</span>
                                            {{ $question->explanation }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center border-2 border-dashed border-[var(--border-color)] rounded-2xl">
                                <i class="fas fa-question-circle text-4xl text-[var(--text-muted)] mb-3"></i>
                                <h4 class="font-bold text-[var(--text-primary)]">No questions added yet</h4>
                                <p class="text-xs text-[var(--text-secondary)] mt-1">Start by adding multiple choice, true/false, or written essay questions to this assessment.</p>
                                <a href="{{ route('admin.quizzes.add-question', $quiz) }}" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition">
                                    <i class="fas fa-plus mr-1.5"></i> Add First Question
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- RIGHT: QUIZ SETTINGS SIDEBAR (1 COL) -->
            <div>
                <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-3xl p-6 border border-[var(--border-color)] shadow-xl space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <h3 class="font-bold text-sm text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-sliders-h text-blue-500"></i>
                            <span>Quiz Configuration</span>
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $quiz->is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">
                            {{ $quiz->is_active ? 'Active' : 'Draft' }}
                        </span>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-[var(--text-secondary)] mb-1">Title *</label>
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                               class="w-full px-3.5 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)]">
                    </div>

                    <!-- Course -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-[var(--text-secondary)] mb-1">Subject Course *</label>
                        <select name="subject_id" required
                                class="w-full px-3.5 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)]">
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" {{ $quiz->subject_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->code }} &mdash; {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Term -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-[var(--text-secondary)] mb-1">Academic Term *</label>
                        <select name="term_id" required
                                class="w-full px-3.5 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)]">
                            @foreach($terms as $t)
                                <option value="{{ $t->id }}" {{ $quiz->term_id == $t->id ? 'selected' : '' }}>
                                    {{ $t->academicYear->year_name ?? '' }} &bull; {{ $t->term_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Numerical Rules -->
                    <div class="grid grid-cols-3 gap-2 text-xs font-mono">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-[var(--text-muted)] mb-1">Pass %</label>
                            <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" min="0" max="100" required
                                   class="w-full px-2.5 py-2 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-[var(--text-primary)] font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-[var(--text-muted)] mb-1">Timer (Min)</label>
                            <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" min="1" max="360"
                                   class="w-full px-2.5 py-2 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-[var(--text-primary)] font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-[var(--text-muted)] mb-1">Max Attempts</label>
                            <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" max="20" required
                                   class="w-full px-2.5 py-2 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-[var(--text-primary)] font-bold">
                        </div>
                    </div>

                    <!-- Question Bank / Pooling -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-[var(--text-secondary)] mb-1 flex items-center gap-1">
                            <i class="fas fa-layer-group text-indigo-500"></i>
                            <span>Question Pool (Per Attempt)</span>
                        </label>
                        <input type="number" name="questions_per_attempt" value="{{ old('questions_per_attempt', $quiz->questions_per_attempt) }}" min="1"
                               placeholder="All questions (Default)"
                               class="w-full px-3 py-2 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] font-mono">
                        <p class="text-[9px] text-[var(--text-muted)] mt-0.5">e.g. Set 20 to randomly pick 20 from the pool.</p>
                    </div>

                    <!-- Toggles -->
                    <div class="space-y-2 pt-2 border-t border-[var(--border-color)]">
                        <label class="flex items-center space-x-2 text-xs cursor-pointer">
                            <input type="checkbox" name="shuffle_questions" value="1" {{ $quiz->shuffle_questions ? 'checked' : '' }} class="rounded text-blue-600">
                            <span class="text-[var(--text-secondary)]">Shuffle Question Order</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs cursor-pointer">
                            <input type="checkbox" name="shuffle_options" value="1" {{ $quiz->shuffle_options ? 'checked' : '' }} class="rounded text-blue-600">
                            <span class="text-[var(--text-secondary)]">Shuffle MCQ Choices</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs cursor-pointer">
                            <input type="checkbox" name="show_correct_answers" value="1" {{ $quiz->show_correct_answers ? 'checked' : '' }} class="rounded text-blue-600">
                            <span class="text-[var(--text-secondary)]">Show Explanations on Results</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $quiz->is_active ? 'checked' : '' }} class="rounded text-emerald-600">
                            <span class="font-bold text-emerald-600">Published / Active</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow hover:scale-102 transition">
                        Save Settings
                    </button>
                </form>

                <!-- Quick Actions Panel -->
                <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-5 border border-[var(--border-color)] shadow space-y-2.5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Tools & Duplication</h4>

                    <form action="{{ route('admin.quizzes.duplicate', $quiz) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-xs font-bold text-[var(--text-primary)] hover:bg-gray-100 dark:hover:bg-gray-800 transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-copy text-indigo-500"></i>
                            <span>Duplicate Quiz as Draft</span>
                        </button>
                    </form>

                    <a href="{{ route('admin.quizzes.export-attempts', $quiz) }}" class="w-full py-2.5 px-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-xs font-bold text-[var(--text-primary)] hover:bg-gray-100 dark:hover:bg-gray-800 transition flex items-center justify-center gap-1.5">
                        <i class="fas fa-file-csv text-emerald-500"></i>
                        <span>Export CSV Gradebook</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
@endsection
