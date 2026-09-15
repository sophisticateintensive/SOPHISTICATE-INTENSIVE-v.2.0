@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.quizzes.index') }}" class="hover:text-blue-500 transition">Quizzes</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">New Assessment</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Create New Assessment
            </h2>
        </div>

        <a href="{{ route('admin.quizzes.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-1.5"></i>
            Back to Quizzes
        </a>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.quizzes.store') }}" method="POST">
        @csrf

        <div class="max-w-5xl mx-auto space-y-6">

            <!-- ========== HERO / STATS SECTION (STUDENT MANAGEMENT DESIGN) ========== -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 shadow-2xl">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
                </div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        Active Session: {{ $activeYear?->year_name ?? 'Current Year' }} &bull; {{ $activeTerm?->term_name ?? 'Active Term' }}
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Assessment Studio & Parameters</h1>
                    <p class="text-blue-100/80 mt-1">Configure evaluation thresholds, time limits, question behaviors and security</p>
                </div>
            </div>

            <!-- ========== MAIN FORM CARD (GLASS) ========== -->
            <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 sm:p-8 border border-[var(--border-color)] shadow-lg space-y-6">
                <h3 class="text-base font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-2">
                    <i class="fas fa-sliders-h text-blue-500"></i>
                    <span>General Quiz Information</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Quiz Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               placeholder="e.g. Midterm Practical Assessment: Advanced Data Structures"
                               class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                        @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Subject / Course -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Academic Course *</label>
                        <select name="subject_id" required
                                class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] cursor-pointer">
                            <option value="">Select Academic Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->code }} &mdash; {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Semester / Term -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Assigned Semester *</label>
                        <select name="term_id" required
                                class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] cursor-pointer">
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ (old('term_id', $activeTerm?->id) == $term->id) ? 'selected' : '' }}>
                                    {{ $term->academicYear->year_name ?? 'Session' }} &mdash; {{ $term->term_name }} {{ $term->is_current ? '(Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="academic_year_id" value="{{ $activeYear?->id }}">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Description</label>
                        <textarea name="description" rows="2"
                                  placeholder="Brief summary of test syllabus and topics tested..."
                                  class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">{{ old('description') }}</textarea>
                    </div>

                    <!-- Instructions -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Candidate Instructions</label>
                        <textarea name="instructions" rows="2"
                                  placeholder="e.g. Ensure stable internet connection. No calculators permitted. Auto-submitted when timer reaches zero."
                                  class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">{{ old('instructions') }}</textarea>
                    </div>
                </div>

                <!-- Parameters & Exam Rules -->
                <h3 class="text-base font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pt-4 pb-3 flex items-center gap-2">
                    <i class="fas fa-stopwatch text-indigo-500"></i>
                    <span>Execution Rules & Security</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <!-- Passing Score -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Passing Benchmark (%) *</label>
                        <div class="relative">
                            <input type="number" name="passing_score" value="{{ old('passing_score', 50) }}" min="0" max="100" required
                                   class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-mono font-bold">
                            <span class="absolute right-4 top-3 text-sm text-[var(--text-muted)] font-mono">%</span>
                        </div>
                    </div>

                    <!-- Time Limit (Minutes) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Time Limit (Minutes)</label>
                        <div class="relative">
                            <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', 30) }}" min="1" max="360"
                                   placeholder="Leave blank for untimed"
                                   class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-mono font-bold">
                            <span class="absolute right-4 top-3 text-xs text-[var(--text-muted)] font-mono">mins</span>
                        </div>
                    </div>

                    <!-- Max Attempts Allowed -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Max Allowed Attempts *</label>
                        <input type="number" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" max="20" required
                               class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-mono font-bold">
                    </div>
                </div>

                <!-- Question Bank / Pooling Option -->
                <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-200 dark:border-indigo-800/40">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-[var(--text-primary)] flex items-center gap-1.5">
                                <i class="fas fa-layer-group text-indigo-500"></i>
                                <span>Question Bank / Dynamic Pooling (Optional)</span>
                            </span>
                            <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">
                                If you create a pool of 50 questions and set this to 20, each candidate will receive a unique random set of 20 questions.
                            </span>
                        </div>
                        <div class="w-full sm:w-56 flex-shrink-0">
                            <input type="number" name="questions_per_attempt" value="{{ old('questions_per_attempt') }}" min="1"
                                   placeholder="All questions (Default)"
                                   class="w-full px-3 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-[var(--text-primary)]">
                        </div>
                    </div>
                </div>

                <!-- Schedule Window -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Available From (Optional)</label>
                        <input type="datetime-local" name="available_from" value="{{ old('available_from') }}"
                               class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Due / Closes At (Optional)</label>
                        <input type="datetime-local" name="available_until" value="{{ old('available_until') }}"
                               class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-mono">
                    </div>
                </div>

                <!-- Feature Toggles -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-[var(--border-color)]">
                    <label class="flex items-center space-x-3 p-3.5 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] cursor-pointer hover:border-blue-500/50 transition">
                        <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-[var(--border-color)]">
                        <div>
                            <span class="text-xs font-bold text-[var(--text-primary)] block">Shuffle Question Order</span>
                            <span class="text-[10px] text-[var(--text-secondary)]">Randomizes question delivery order for each candidate</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 p-3.5 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] cursor-pointer hover:border-blue-500/50 transition">
                        <input type="checkbox" name="shuffle_options" value="1" {{ old('shuffle_options') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-[var(--border-color)]">
                        <div>
                            <span class="text-xs font-bold text-[var(--text-primary)] block">Randomize Answer Choices</span>
                            <span class="text-[10px] text-[var(--text-secondary)]">Shuffles MCQ option letters (A, B, C, D) per student</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 p-3.5 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] cursor-pointer hover:border-blue-500/50 transition">
                        <input type="checkbox" name="show_correct_answers" value="1" {{ old('show_correct_answers', true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-[var(--border-color)]">
                        <div>
                            <span class="text-xs font-bold text-[var(--text-primary)] block">Instant Answer Explanations</span>
                            <span class="text-[10px] text-[var(--text-secondary)]">Shows correct answers and rationales on transcript after submission</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 p-3.5 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] cursor-pointer hover:border-emerald-500/50 transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-[var(--border-color)]">
                        <div>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block">Publish Assessment (Active)</span>
                            <span class="text-[10px] text-[var(--text-secondary)]">Enable student access immediately once questions are added</span>
                        </div>
                    </label>
                </div>

                <!-- Submit Bar -->
                <div class="pt-6 border-t border-[var(--border-color)] flex items-center justify-between">
                    <a href="{{ route('admin.quizzes.index') }}" class="px-5 py-3 rounded-xl bg-[var(--bg-card)] text-[var(--text-secondary)] text-xs font-bold hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-8 py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                        <span>Save & Proceed to Questions Studio</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
