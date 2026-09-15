@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Students Management
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Manage all student records and information</p>
        </div>

        <a href="{{ route('admin.students.create') }}"
           class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Student
        </a>
    </div>
@endsection

@section('content')
    @php
        // Accurate stats from database (not current page)
        $totalStudents = \App\Models\Student::count();
        $activeEnrollments = \App\Models\StudentEnrollment::where('status', 'active')->count();
        $totalProgrammes = \App\Models\Subject::count(); // Adjust to your programme model if different
        $thisMonth = \App\Models\Student::where('created_at', '>=', now()->startOfMonth())->count();
    @endphp

    <!-- ========== HERO / STATS SECTION ========== -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 mb-8 shadow-2xl">
        <!-- Animated blobs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4" x-data="{ counters: { total: 0, active: 0, programmes: 0, thisMonth: 0 }, init() {
                const targets = { total: {{ $totalStudents }}, active: {{ $activeEnrollments }}, programmes: {{ $totalProgrammes }}, thisMonth: {{ $thisMonth }} };
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
                    {{ $totalStudents }} students · {{ $activeEnrollments }} active
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Student Records</h1>
                <p class="text-blue-100/80 mt-1">Comprehensive overview of all enrolled students</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.total"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Total</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.active"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Active</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.programmes"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Programmes</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.thisMonth"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">This month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== SEARCH & FILTER ========== -->
    <form method="GET" action="{{ route('admin.students.index') }}" id="filterForm" class="mb-8">
        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-[var(--border-color)] shadow-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                               placeholder="Search by name, reg number, programme, email..."
                               class="w-full pl-10 pr-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm hover:shadow-md transition-all duration-200 text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                        @if(request('search'))
                            <a href="{{ route('admin.students.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Academic Year Filter -->
                <div class="relative">
                    <select name="academic_year" id="academicYearFilter"
                            class="w-full px-3 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
                                {{ $year->year_name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Term Filter -->
                <div class="relative">
                    <select name="term" id="termFilter"
                            class="w-full px-3 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" data-academic-year-id="{{ $term->academic_year_id }}" {{ request('term') == $term->id ? 'selected' : '' }}>
                                {{ $term->term_name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Programme Filter -->
                <div class="relative">
                    <select name="programme" id="programmeFilter"
                            class="w-full px-3 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Programmes</option>
                        @foreach($programmes as $programme)
                            <option value="{{ $programme }}" {{ request('programme') == $programme ? 'selected' : '' }}>
                                {{ $programme }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-3 py-3 text-sm bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Search
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="px-3 py-3 text-sm bg-[var(--bg-card)] text-[var(--text-primary)] rounded-xl border border-[var(--border-color)] hover:bg-[var(--glass-bg)] hover:scale-105 transition-all duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                    <button type="button" id="exportBtn" class="px-3 py-3 text-sm bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- ========== STUDENT CARDS GRID ========== -->
    @if($students->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($students as $student)
                <div class="group bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl border border-[var(--border-color)] shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                    <!-- Card header -->
                    <div class="relative h-24 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 flex items-center justify-between px-4 pt-4">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                @if($student->profile_picture)
                                    <img src="{{ Storage::url($student->profile_picture) }}" alt="{{ $student->user->name }}"
                                         class="w-14 h-14 rounded-full object-cover shadow-lg ring-4 ring-white/50 dark:ring-zinc-700/50">
                                @else
                                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-xl shadow-lg ring-4 ring-white/50">
                                        {{ substr($student->user->name, 0, 1) }}
                                    </div>
                                @endif
                                @if(optional($student->activeEnrollment)->status === 'active')
                                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-[var(--text-primary)] group-hover:text-blue-600 transition-colors">
                                    {{ $student->user->name }}
                                </h4>
                                <p class="text-xs text-[var(--text-secondary)]">{{ $student->reg_number }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                            {{ $student->programme }}
                        </span>
                    </div>

                    <!-- Card body -->
                    <div class="p-4 space-y-3">
                        <div class="flex items-center text-sm text-[var(--text-secondary)]">
                            <svg class="w-4 h-4 mr-2 text-[var(--text-muted)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate">{{ $student->user->email }}</span>
                        </div>

                        <div class="flex items-center text-xs text-[var(--text-secondary)]">
                            <svg class="w-3.5 h-3.5 mr-2 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            @if($student->phone)
                                <a href="tel:{{ $student->phone }}" class="text-blue-600 hover:underline font-mono">{{ $student->phone }}</a>
                            @elseif($student->parent_phone)
                                <span class="text-gray-400">Parent: {{ $student->parent_phone }}</span>
                            @else
                                <span class="text-gray-400">No contact phone</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="bg-[var(--bg-card)] rounded-lg px-3 py-2 border border-[var(--border-color)]">
                                <p class="text-xs text-[var(--text-muted)]">Academic Year</p>
                                <p class="font-medium text-[var(--text-primary)]">
                                    {{ optional($student->activeEnrollment)->academicYear->year_name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="bg-[var(--bg-card)] rounded-lg px-3 py-2 border border-[var(--border-color)]">
                                <p class="text-xs text-[var(--text-muted)]">Term</p>
                                <p class="font-medium text-[var(--text-primary)]">
                                    {{ optional($student->activeEnrollment)->term->term_name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-2 border-t border-[var(--border-color)]">
                            {{-- Account status badge --}}
                            @php $isActive = $student->user->is_active ?? true; @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $isActive ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-red-500/10 text-red-700 dark:text-red-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.students.show', $student) }}" class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                {{-- Toggle active/inactive --}}
                                <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST" class="inline"
                                      onsubmit="return confirm('{{ $isActive ? 'Deactivate' : 'Activate' }} {{ addslashes($student->user->name) }}\'s account?')">
                                    @csrf
                                    <button type="submit"
                                            class="p-2 rounded-lg transition-all {{ $isActive ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/30' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30' }}"
                                            title="{{ $isActive ? 'Deactivate Account' : 'Activate Account' }}">
                                        <i class="fas fa-{{ $isActive ? 'ban' : 'check-circle' }} w-4 h-4 text-sm"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" id="delete-student-{{ $student->id }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="openDeleteModal(
                                        'Delete Student?',
                                        'You are about to delete {{ addslashes($student->user->name) }} (Reg: {{ $student->reg_number }}). This action cannot be undone.',
                                        document.getElementById('delete-student-{{ $student->id }}')
                                    )" class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-all" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $students->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="bg-[var(--glass-bg)] rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center border border-[var(--border-color)]">
                <svg class="w-12 h-12 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Students Found</h4>
            <p class="text-[var(--text-secondary)] mb-4">Get started by adding your first student</p>
            <a href="{{ route('admin.students.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add First Student
            </a>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // Note: openDeleteModal, closeDeleteModal, confirmDelete are already defined in the layout.
        // We only add filter logic and export.

        function filterTermsByAcademicYear() {
            const academicYearId = document.getElementById('academicYearFilter').value;
            const termSelect = document.getElementById('termFilter');
            const options = termSelect.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }
                const optionAcademicYearId = option.getAttribute('data-academic-year-id');
                option.style.display = (!academicYearId || optionAcademicYearId === academicYearId) ? 'block' : 'none';
                if (option.style.display === 'none' && option.selected) option.selected = false;
            });
        }

        document.getElementById('exportBtn')?.addEventListener('click', function() {
            const search = document.querySelector('input[name="search"]').value;
            const academicYear = document.querySelector('select[name="academic_year"]').value;
            const term = document.querySelector('select[name="term"]').value;
            const programme = document.querySelector('select[name="programme"]').value;
            let url = '{{ route("admin.students.export") }}?';
            if (search) url += `search=${encodeURIComponent(search)}&`;
            if (academicYear) url += `academic_year=${academicYear}&`;
            if (term) url += `term=${term}&`;
            if (programme) url += `programme=${encodeURIComponent(programme)}&`;
            window.location.href = url;
        });

        // Auto-submit on filter change
        ['academicYearFilter', 'termFilter', 'programmeFilter'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function() {
                    if (id === 'academicYearFilter') filterTermsByAcademicYear();
                    document.getElementById('filterForm').submit();
                });
            }
        });

        // Initial filter
        filterTermsByAcademicYear();
    </script>
@endpush
