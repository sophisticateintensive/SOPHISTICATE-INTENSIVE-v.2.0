@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Quizzes & Online Assessments
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Manage tests, multiple-choice questions, and monitor student attempt performance</p>
        </div>

        <a href="{{ route('admin.quizzes.create') }}"
            class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Create New Quiz
        </a>
    </div>
@endsection

@section('content')
    @php
        $totalQuizzes = $quizzes->total();
        $totalQuestions = \App\Models\Question::count();
        $totalAttempts = \App\Models\QuizAttempt::count();
        $activeQuizzes = \App\Models\Quiz::where('is_active', true)->count();
    @endphp

    <!-- ========== HERO / STATS SECTION (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 mb-8 shadow-2xl">
        <!-- Animated blobs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4"
             x-data="{ counters: { total: 0, active: 0, questions: 0, attempts: 0 }, init() {
                const targets = { total: {{ $totalQuizzes }}, active: {{ $activeQuizzes }}, questions: {{ $totalQuestions }}, attempts: {{ $totalAttempts }} };
                Object.keys(targets).forEach(key => {
                    const interval = setInterval(() => {
                        if (this.counters[key] < targets[key]) {
                            this.counters[key] += Math.ceil(targets[key] / 30);
                            if (this.counters[key] > targets[key]) this.counters[key] = targets[key];
                        } else {
                            clearInterval(interval);
                        }
                    }, 40);
                });
            }}">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    {{ $activeQuizzes }} Live Quizzes · {{ $totalAttempts }} Student Submissions
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Assessment Engine & Quizzes</h1>
                <p class="text-blue-100/80 mt-1">Self-graded online quizzes, timed examinations and evaluation rubrics</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.total"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Quizzes</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-emerald-300" x-text="counters.active"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Active</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-purple-300" x-text="counters.questions"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Questions</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-amber-300" x-text="counters.attempts"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Attempts</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== QUIZZES CARDS GRID (STUDENT MANAGEMENT DESIGN) ========== -->
    @if($quizzes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($quizzes as $quiz)
                <div class="group bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl border border-[var(--border-color)] shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Card Header -->
                        <div class="relative h-20 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 flex items-center justify-between px-4 pt-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg shadow-md">
                                    <i class="fas fa-feather-alt"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-[var(--text-primary)] group-hover:text-blue-600 transition truncate max-w-[170px]">
                                        {{ $quiz->title }}
                                    </h4>
                                    <p class="text-xs text-[var(--text-secondary)] font-mono">{{ $quiz->subject->code ?? 'General' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-full {{ $quiz->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                                {{ $quiz->is_active ? 'Active' : 'Draft' }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-3">
                            @if($quiz->description)
                                <p class="text-xs text-[var(--text-secondary)] line-clamp-2">{{ $quiz->description }}</p>
                            @endif

                            <div class="grid grid-cols-2 gap-2 text-sm font-mono">
                                <div class="bg-[var(--bg-card)] rounded-xl px-3 py-2 border border-[var(--border-color)]">
                                    <p class="text-[10px] text-[var(--text-muted)] uppercase">Questions</p>
                                    <p class="font-bold text-[var(--text-primary)] mt-0.5">{{ $quiz->questions->count() }} items</p>
                                </div>
                                <div class="bg-[var(--bg-card)] rounded-xl px-3 py-2 border border-[var(--border-color)]">
                                    <p class="text-[10px] text-[var(--text-muted)] uppercase">Pass Mark</p>
                                    <p class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $quiz->passing_score }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="px-5 py-3.5 bg-[var(--bg-card)] border-t border-[var(--border-color)] flex items-center justify-between">
                        <a href="{{ route('admin.quizzes.add-question', $quiz) }}" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                            <span>Manage Questions</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.quizzes.attempts', $quiz) }}" class="p-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-all" title="View Submissions">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </a>
                            <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all" title="Edit Quiz">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" id="delete-quiz-{{ $quiz->id }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="openDeleteModal(
                                    'Delete Quiz?',
                                    'You are about to delete {{ addslashes($quiz->title) }} and all associated attempts. This action cannot be undone.',
                                    document.getElementById('delete-quiz-{{ $quiz->id }}')
                                )" class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-all" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $quizzes->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="bg-[var(--glass-bg)] rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center border border-[var(--border-color)]">
                <svg class="w-12 h-12 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Quizzes Found</h4>
            <p class="text-[var(--text-secondary)] mb-4">Get started by creating your first interactive assessment</p>
            <a href="{{ route('admin.quizzes.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create First Quiz
            </a>
        </div>
    @endif
@endsection
