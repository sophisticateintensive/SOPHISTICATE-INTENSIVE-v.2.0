@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Semesters & Academic Terms
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Manage active academic sessions and historical terms</p>
        </div>

        <a href="{{ route('admin.terms.create') }}"
           class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Semester
        </a>
    </div>
@endsection

@section('content')
    @php
        $totalSemesters = $terms->total();
        $activeTermModel = \App\Services\ActiveSemesterService::getActiveTerm();
        $unlockedSemesters = \App\Models\Term::where('is_locked', false)->count();
        $lockedSemesters = \App\Models\Term::where('is_locked', true)->count();
        $academicYearsCount = \App\Models\AcademicYear::count();
    @endphp

    <!-- ========== CURRENT ACTIVE SEMESTER BANNER ========== -->
    <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-blue-500/10 border border-emerald-500/30 backdrop-blur-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-xl shadow-md">
                <i class="fas fa-bolt animate-pulse"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500 text-white">
                        CURRENT SYSTEM ACTIVE SEMESTER
                    </span>
                </div>
                <h2 class="text-xl font-bold text-[var(--text-primary)] font-mono mt-0.5">
                    {{ $activeTermModel?->academicYear?->year_name ?? 'No Year Set' }} &mdash; {{ $activeTermModel?->term_name ?? 'No Active Semester' }}
                </h2>
                <p class="text-xs text-[var(--text-secondary)] mt-0.5">
                    All dashboards, records, results, enrollments, and tuition fees automatically filter to this semester by default.
                </p>
            </div>
        </div>

        @if($activeTermModel && $activeTermModel->is_locked)
            <span class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 text-xs font-mono font-bold flex items-center gap-1.5">
                <i class="fas fa-lock text-xs"></i>
                <span>Active Semester is Locked (Read-Only)</span>
            </span>
        @endif
    </div>

    <!-- ========== HERO / STATS SECTION ========== -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 mb-8 shadow-2xl">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    {{ $unlockedSemesters }} editable · {{ $lockedSemesters }} locked archive
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Semester Master Directory</h1>
                <p class="text-blue-100/80 mt-1">Switch active semester and lock historical terms to prevent accidental modifications</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                    <div class="text-2xl font-bold text-white">{{ $totalSemesters }}</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Total</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                    <div class="text-2xl font-bold text-emerald-300">1</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Active</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                    <div class="text-2xl font-bold text-amber-300">{{ $lockedSemesters }}</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Locked</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                    <div class="text-2xl font-bold text-white">{{ $academicYearsCount }}</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Years</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== SEARCH & FILTER ========== -->
    <form method="GET" action="{{ route('admin.terms.index') }}" id="filterForm" class="mb-8">
        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-[var(--border-color)] shadow-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                           placeholder="Search by semester name..."
                           class="w-full pl-10 pr-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm hover:shadow-md transition-all duration-200 text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                    @if(request('search'))
                        <a href="{{ route('admin.terms.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>

                <!-- Status Filter -->
                <div class="relative">
                    <select name="status" id="statusFilter"
                            class="w-full px-3 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Unlocked</option>
                        <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Locked</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Academic Year Filter -->
                <div class="relative">
                    <select name="academic_year" id="yearFilter"
                            class="w-full px-3 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears ?? [] as $year)
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

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-3 py-3 text-sm bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Search
                    </button>
                    <a href="{{ route('admin.terms.index') }}" class="px-3 py-3 text-sm bg-[var(--bg-card)] text-[var(--text-primary)] rounded-xl border border-[var(--border-color)] hover:bg-[var(--glass-bg)] hover:scale-105 transition-all duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- ========== SEMESTERS TABLE ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-wide">All Semesters</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Activate semester or lock to archive</p>
                </div>
            </div>
            <span class="text-xs text-white/80 bg-white/10 px-3 py-1 rounded-full">
                {{ $terms->total() }} Semesters
            </span>
        </div>

        <div class="p-6">
            @if($terms->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Active Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Term / Semester</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Academic Year</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Edit Access</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Created</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)]">
                            @foreach($terms as $term)
                                @php
                                    $isThisCurrent = $term->is_current;
                                @endphp
                                <tr class="hover:bg-[var(--accent-soft)] transition-colors duration-200 {{ $isThisCurrent ? 'bg-emerald-500/5' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($isThisCurrent)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-500 text-white shadow-sm font-mono">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                                <span>ACTIVE</span>
                                            </span>
                                        @else
                                            <form action="{{ route('admin.terms.set-active', $term) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 rounded-xl text-xs font-mono font-bold border border-zinc-300 dark:border-zinc-700 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                                    <i class="fas fa-check text-[10px]"></i>
                                                    <span>Set Active</span>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 {{ $isThisCurrent ? 'bg-emerald-500' : 'bg-gradient-to-br from-blue-400 to-indigo-500' }} rounded-lg flex items-center justify-center shadow-sm">
                                                <span class="text-xs font-bold text-white">{{ substr($term->term_name, 0, 1) }}</span>
                                            </div>
                                            <span class="text-sm font-semibold text-[var(--text-primary)]">{{ $term->term_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-[var(--accent-soft)] text-[var(--accent)] font-mono">
                                            {{ $term->academicYear->year_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($term->is_locked)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800">
                                                <i class="fas fa-lock mr-1.5 text-[10px]"></i>
                                                Locked Archive
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800">
                                                <i class="fas fa-unlock mr-1.5 text-[10px]"></i>
                                                Unlocked (Open)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-[var(--text-secondary)] font-mono">{{ $term->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <!-- Toggle Lock/Unlock -->
                                            <form action="{{ route('admin.terms.toggle-lock', $term) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-2 bg-[var(--bg-card)] text-[var(--text-secondary)] rounded-lg hover:bg-[var(--accent-soft)] hover:text-[var(--accent)] hover:scale-110 transition-all duration-200" title="{{ $term->is_locked ? 'Unlock for Edits' : 'Lock for Edits' }}">
                                                    <i class="fas {{ $term->is_locked ? 'fa-lock-open' : 'fa-lock' }}"></i>
                                                </button>
                                            </form>

                                            <!-- Edit -->
                                            <a href="{{ route('admin.terms.edit', $term) }}" class="p-2 bg-[var(--bg-card)] text-[var(--text-secondary)] rounded-lg hover:bg-[var(--accent-soft)] hover:text-[var(--accent)] hover:scale-110 transition-all duration-200" title="Edit Semester">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <!-- Delete -->
                                            @if(!$isThisCurrent)
                                                <form action="{{ route('admin.terms.destroy', $term) }}" method="POST" id="delete-term-{{ $term->id }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="openDeleteModal('Delete Semester?', 'Are you sure you want to delete <strong>{{ addslashes($term->term_name) }}</strong>? This action cannot be undone.', document.getElementById('delete-term-{{ $term->id }}'))"
                                                        class="p-2 bg-[var(--bg-card)] text-[var(--text-secondary)] rounded-lg hover:bg-red-100 hover:text-red-600 hover:scale-110 transition-all duration-200"
                                                        title="Delete Term">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $terms->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Semesters Found</h4>
                    <p class="text-[var(--text-secondary)] mb-4">Get started by adding your first semester</p>
                    <a href="{{ route('admin.terms.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add First Semester
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
