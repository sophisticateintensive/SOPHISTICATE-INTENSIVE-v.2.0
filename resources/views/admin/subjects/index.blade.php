@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Courses & Curriculums
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Manage academic courses, module codes and credit hour weights</p>
        </div>

        <a href="{{ route('admin.subjects.create') }}"
            class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Course
        </a>
    </div>
@endsection

@section('content')
    @php
        $totalCourses = $subjects->count();
        $totalCredits = $subjects->sum('credit_hours');
        $avgCredits = $totalCourses > 0 ? round($totalCredits / $totalCourses, 1) : 0;
        $threeCreditPlus = $subjects->where('credit_hours', '>=', 3)->count();
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
             x-data="{ counters: { total: 0, credits: 0, core: 0, avg: 0 }, init() {
                const targets = { total: {{ $totalCourses }}, credits: {{ $totalCredits }}, core: {{ $threeCreditPlus }}, avg: {{ (int)$avgCredits }} };
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
                    {{ $totalCourses }} Active Courses · {{ $totalCredits }} Cumulative Credits
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Course Catalog Ledger</h1>
                <p class="text-blue-100/80 mt-1">Full curriculum inventory and credit allocations</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.total"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Courses</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-emerald-300" x-text="counters.credits"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Credit Hours</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-purple-300" x-text="counters.core"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Core Modules</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-amber-300"><span x-text="counters.avg"></span> hrs</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Avg Weight</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== SEARCH & FILTER (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-[var(--border-color)] shadow-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Input -->
            <div class="lg:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="courseSearchInput" onkeyup="filterCourses()"
                           placeholder="Search course code, subject name..."
                           class="w-full pl-10 pr-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm hover:shadow-md transition-all duration-200 text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                </div>
            </div>

            <!-- Credits Filter -->
            <div class="relative">
                <select id="creditFilter" onchange="filterCourses()"
                        class="w-full px-3 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200 appearance-none cursor-pointer text-[var(--text-primary)]">
                    <option value="">All Credit Hours</option>
                    <option value="1">1 Credit</option>
                    <option value="2">2 Credits</option>
                    <option value="3">3 Credits</option>
                    <option value="4">4 Credits</option>
                    <option value="5+">5+ Credits</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <button type="button" onclick="filterCourses()" class="flex-1 px-3 py-3 text-sm bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <button type="button" onclick="resetCourseFilters()" class="px-3 py-3 text-sm bg-[var(--bg-card)] text-[var(--text-primary)] rounded-xl border border-[var(--border-color)] hover:bg-[var(--glass-bg)] hover:scale-105 transition-all duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- ========== COURSES TABLE (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-wide">All Curriculum Courses</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Manage codes, credit weights, and course syllabi</p>
                </div>
            </div>
            <span class="text-xs text-white/80 bg-white/10 px-3 py-1 rounded-full font-mono">
                {{ $subjects->count() }} Courses
            </span>
        </div>

        <div class="p-6">
            @if($subjects->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Course Code</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Course Name</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Credit Hours</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Created Date</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="courseTableBody" class="divide-y divide-[var(--border-color)]">
                            @foreach($subjects as $subject)
                                <tr class="course-row hover:bg-[var(--accent-soft)] transition-colors duration-200"
                                    data-code="{{ strtolower($subject->code) }}"
                                    data-name="{{ strtolower($subject->name) }}"
                                    data-credits="{{ $subject->credit_hours }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                            <i class="fas fa-bookmark mr-1.5 text-[10px]"></i>
                                            {{ $subject->code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-bold text-[var(--text-primary)]">{{ $subject->name }}</p>
                                        @if($subject->description)
                                            <p class="text-xs text-[var(--text-secondary)] truncate max-w-xs">{{ $subject->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                            {{ $subject->credit_hours }} Credits
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                        {{ $subject->created_at?->format('M d, Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.subjects.show', $subject) }}" class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all" title="View Course">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all" title="Edit Course">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" id="delete-subject-{{ $subject->id }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="openDeleteModal(
                                                    'Delete Course?',
                                                    'You are about to delete {{ addslashes($subject->name) }} (Code: {{ $subject->code }}). This action cannot be undone.',
                                                    document.getElementById('delete-subject-{{ $subject->id }}')
                                                )" class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-all" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="bg-[var(--glass-bg)] rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center border border-[var(--border-color)]">
                        <svg class="w-12 h-12 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Courses Found</h4>
                    <p class="text-[var(--text-secondary)] mb-4">Get started by creating your first academic course</p>
                    <a href="{{ route('admin.subjects.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add First Course
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function filterCourses() {
        const query = document.getElementById('courseSearchInput').value.toLowerCase().trim();
        const credit = document.getElementById('creditFilter').value;
        const rows = document.querySelectorAll('.course-row');

        rows.forEach(row => {
            const code = row.getAttribute('data-code');
            const name = row.getAttribute('data-name');
            const rowCredits = parseInt(row.getAttribute('data-credits'));

            let matchesSearch = !query || code.includes(query) || name.includes(query);
            let matchesCredit = true;

            if (credit === '5+') {
                matchesCredit = rowCredits >= 5;
            } else if (credit) {
                matchesCredit = rowCredits === parseInt(credit);
            }

            if (matchesSearch && matchesCredit) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function resetCourseFilters() {
        document.getElementById('courseSearchInput').value = '';
        document.getElementById('creditFilter').value = '';
        filterCourses();
    }
</script>
@endpush
