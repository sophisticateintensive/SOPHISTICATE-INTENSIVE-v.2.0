@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.quizzes.index') }}" class="hover:text-blue-500 transition">Quizzes</a>
                <span>/</span>
                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="hover:text-blue-500 transition">{{ Str::limit($quiz->title, 25) }}</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">Add Question</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Add Assessment Question
            </h2>
        </div>

        <a href="{{ route('admin.quizzes.edit', $quiz) }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-1.5"></i>
            Back to Quiz Studio
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto" x-data="{ questionType: 'multiple_choice', optionCount: 4 }">
        <form action="{{ route('admin.quizzes.store-question', $quiz) }}" method="POST">
            @csrf

            <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 sm:p-8 border border-[var(--border-color)] shadow-lg space-y-6">

                <!-- Header Info -->
                <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-4">
                    <div>
                        <h3 class="text-base font-bold text-[var(--text-primary)]">Configure Question & Answer Key</h3>
                        <p class="text-xs text-[var(--text-secondary)]">Supports Multiple Choice (up to 6 choices) and True / False items</p>
                    </div>

                    <!-- Type Switcher -->
                    <div class="flex p-1 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                        <button type="button" @click="questionType = 'multiple_choice'"
                                :class="questionType === 'multiple_choice' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                            Multiple Choice
                        </button>
                        <button type="button" @click="questionType = 'true_false'"
                                :class="questionType === 'true_false' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                            True / False
                        </button>
                        <button type="button" @click="questionType = 'essay'"
                                :class="questionType === 'essay' ? 'bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                            Written Essay
                        </button>
                    </div>
                </div>

                <input type="hidden" name="type" :value="questionType">

                <!-- Question Text & Points -->
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Question Prompt *</label>
                            <textarea name="question_text" rows="3" required
                                      placeholder="Type your question prompt, problem statement, or essay topic here..."
                                      class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">{{ old('question_text') }}</textarea>
                            @error('question_text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="w-full sm:w-36">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Points Weight *</label>
                            <input type="number" name="points" value="{{ old('points', 5) }}" min="1" max="100" required
                                   class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-mono font-bold">
                        </div>
                    </div>
                </div>

                <!-- WRITTEN ESSAY INFO SECTION -->
                <div x-show="questionType === 'essay'" class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/30 text-purple-800 dark:text-purple-300 space-y-2" style="display: none;">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <i class="fas fa-pen-nib"></i>
                        <span>Free-Text Written Response (Manual Grading)</span>
                    </div>
                    <p class="text-xs leading-relaxed opacity-90">
                        Students will be provided with an essay text area during their assessment to draft their complete written answer. Once submitted, lecturers will review and award points with individual feedback.
                    </p>
                </div>

                <!-- MULTIPLE CHOICE OPTIONS SECTION -->
                <div x-show="questionType === 'multiple_choice'" class="space-y-4 pt-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Answer Choices & Correct Key *
                        </label>
                        <span class="text-[11px] text-blue-500 font-medium">Select the radio button beside the correct answer</span>
                    </div>

                    <div class="space-y-3">
                        @for($i = 0; $i < 4; $i++)
                            <div class="flex items-center space-x-3 p-2.5 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] focus-within:border-blue-500 transition">
                                <label class="flex items-center justify-center p-2 cursor-pointer" title="Mark as correct answer">
                                    <input type="radio" name="correct_option" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }}
                                           class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                </label>
                                <span class="w-6 font-mono font-bold text-xs text-[var(--text-muted)] text-center">{{ chr(65 + $i) }}</span>
                                <input type="text" name="options[]" value="{{ old('options.' . $i) }}" placeholder="Enter choice text..."
                                       class="flex-1 px-3 py-2 text-sm bg-transparent border-0 focus:outline-none text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- TRUE / FALSE OPTIONS SECTION -->
                <div x-show="questionType === 'true_false'" class="space-y-4 pt-2" style="display: none;">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                        Select Correct Statement Value *
                    </label>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center space-x-3 p-4 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] cursor-pointer hover:border-emerald-500 transition">
                            <input type="radio" name="correct_tf" value="true" checked class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-bold text-[var(--text-primary)] block">True</span>
                                <span class="text-xs text-[var(--text-secondary)]">Statement is factually correct</span>
                            </div>
                        </label>

                        <label class="flex items-center space-x-3 p-4 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] cursor-pointer hover:border-red-500 transition">
                            <input type="radio" name="correct_tf" value="false" class="w-5 h-5 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-bold text-[var(--text-primary)] block">False</span>
                                <span class="text-xs text-[var(--text-secondary)]">Statement is incorrect or flawed</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Explanation Box -->
                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">
                        Pedagogical Explanation & Answer Key Rationale (Optional)
                    </label>
                    <textarea name="explanation" rows="2"
                              placeholder="Explain why this answer is correct. This is shown to students during post-test transcript reviews to enhance learning..."
                              class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">{{ old('explanation') }}</textarea>
                </div>

                <!-- Footer Submit -->
                <div class="pt-6 border-t border-[var(--border-color)] flex items-center justify-between">
                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="px-5 py-3 rounded-xl bg-[var(--bg-card)] text-[var(--text-secondary)] text-xs font-bold hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-7 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow hover:shadow-lg hover:scale-105 transition-all flex items-center gap-1.5">
                        <i class="fas fa-check text-[10px]"></i>
                        <span>Save Question to Quiz</span>
                    </button>
                </div>

            </div>
        </form>
    </div>
@endsection
