@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Course Assignment
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Manage which courses each student is taking</p>
        </div>

        <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2 px-3 sm:px-4 py-2 bg-[var(--bg-card)] backdrop-blur-sm rounded-xl border border-[var(--border-color)] shadow-sm">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-xs sm:text-sm font-medium text-[var(--text-primary)]">{{ now()->format('l, F j, Y') }}</span>
            </div>

            <a href="{{ route('admin.new-subject-assignment.bulk') }}"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Bulk Provisioning Studio
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $totalStudents = $totalStudents ?? ($students ? $students->count() : 0);
        $totalCourses = $totalCourses ?? ($subjects ? $subjects->count() : 0);
        $totalEnrollments = $totalEnrollments ?? ($students ? $students->sum(function($s) { return $s->subjects->count(); }) : 0);
        $noCourses = $noCourses ?? ($students ? $students->filter(function($s) { return $s->subjects->isEmpty(); })->count() : 0);
    @endphp

    <style>
        .student-card {
            display: flex;
            flex-direction: column;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .student-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px var(--shadow-color);
            border-color: rgba(59, 130, 246, 0.4);
        }
        .course-badge {
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 700;
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .progress-bar-wrap {
            width: 100%;
            height: 6px;
            background: var(--border-color);
            border-radius: 9999px;
            overflow: hidden;
        }
        .progress-bar-wrap .bar {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.6s ease;
        }
        .bar-green { background: linear-gradient(90deg, #10b981, #34d399); }
        .bar-yellow { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .bar-red { background: linear-gradient(90deg, #ef4444, #f87171); }
        .bar-blue { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    </style>

    <!-- ========== HERO / STATS SECTION ========== -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 mb-6 shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-xs font-semibold text-white mb-2">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    {{ $activeTerm ? $activeTerm->term_name . ' (' . ($activeTerm->academicYear->year_name ?? 'Active') . ')' : 'Academic Session' }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Course Assignment Matrix</h1>
                <p class="text-blue-100/80 text-sm mt-1">Assign, track, and manage enrolled modules for every registered student</p>
            </div>

            <!-- Stats grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3 text-center border border-white/10">
                    <div class="text-2xl font-black text-white font-mono">{{ $totalStudents }}</div>
                    <div class="text-[10px] text-blue-200 uppercase font-bold tracking-wider">Students</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3 text-center border border-white/10">
                    <div class="text-2xl font-black text-emerald-300 font-mono">{{ $totalCourses }}</div>
                    <div class="text-[10px] text-blue-200 uppercase font-bold tracking-wider">Courses</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3 text-center border border-white/10">
                    <div class="text-2xl font-black text-purple-200 font-mono">{{ $totalEnrollments }}</div>
                    <div class="text-[10px] text-blue-200 uppercase font-bold tracking-wider">Assigned</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3 text-center border border-white/10">
                    <div class="text-2xl font-black text-amber-300 font-mono">{{ $noCourses }}</div>
                    <div class="text-[10px] text-blue-200 uppercase font-bold tracking-wider">Unassigned</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MAIN INTERACTIVE MATRIX & VIEW SWITCHER ========== -->
    <div x-data="{ viewMode: 'card' }" class="space-y-6">

        <!-- Search & Filter Controls -->
        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-4 sm:p-5 border border-[var(--border-color)] shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search by name, reg number..."
                           class="w-full pl-10 pr-4 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                </div>

                <!-- Programme Filter -->
                <div class="relative">
                    <select id="programmeFilter"
                            class="w-full px-3 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Programmes</option>
                        @foreach($programmes ?? [] as $prog)
                            <option value="{{ $prog }}">{{ $prog }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>

                <!-- Course Filter -->
                <div class="relative">
                    <select id="subjectFilter"
                            class="w-full px-3 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Courses</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>

                <!-- Course Count Range Filter -->
                <div class="relative">
                    <select id="courseCountFilter"
                            class="w-full px-3 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Allocations</option>
                        <option value="0">Unassigned (0)</option>
                        <option value="1-3">1-3 Courses</option>
                        <option value="4+">4+ Courses</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>

                <!-- View Toggle Buttons & Reset -->
                <div class="flex items-center gap-2">
                    <div class="flex rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] p-1 flex-1">
                        <button type="button" @click="viewMode = 'card'"
                                :class="viewMode === 'card' ? 'bg-blue-600 text-white shadow-sm' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                                class="flex-1 py-1.5 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1">
                            <i class="fas fa-th-large text-[10px]"></i>
                            <span>Cards</span>
                        </button>
                        <button type="button" @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-blue-600 text-white shadow-sm' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                                class="flex-1 py-1.5 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1">
                            <i class="fas fa-list text-[10px]"></i>
                            <span>Table</span>
                        </button>
                    </div>

                    <button type="button" onclick="resetAllFilters()"
                            class="px-3 py-2.5 bg-[var(--bg-card)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] border border-[var(--border-color)] rounded-xl text-xs font-bold transition flex items-center gap-1"
                            title="Reset all filters">
                        <i class="fas fa-redo-alt text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ==================== 1. CARDS VIEW ==================== -->
        <div x-show="viewMode === 'card'" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="studentsGrid">
                @forelse($students as $student)
                    @php
                        $courseCount = $student->subjects->count();
                        $completed = $student->subjects->where('pivot.status', 'completed')->count();
                        $progress = $courseCount > 0 ? round(($completed / $courseCount) * 100) : 0;
                        $barColor = $progress >= 75 ? 'bar-green' : ($progress >= 40 ? 'bar-yellow' : ($courseCount > 0 ? 'bar-blue' : 'bar-red'));
                    @endphp

                    <div class="student-card"
                         data-name="{{ strtolower($student->user->name ?? '') }}"
                         data-reg="{{ strtolower($student->reg_number ?? '') }}"
                         data-programme="{{ strtolower($student->programme ?? '') }}"
                         data-subjects="{{ $student->subjects->pluck('id')->join(',') }}"
                         data-count="{{ $courseCount }}">

                        <!-- Card Header -->
                        <div class="p-5 border-b border-[var(--border-color)] bg-gradient-to-r from-blue-500/5 to-transparent flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($student->profile_picture)
                                    <img src="{{ Storage::url($student->profile_picture) }}" alt="{{ $student->user->name }}"
                                         class="w-11 h-11 rounded-2xl object-cover ring-2 ring-blue-500/30 flex-shrink-0">
                                @else
                                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white font-bold text-sm flex items-center justify-center flex-shrink-0 font-mono shadow-sm">
                                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-[var(--text-primary)] truncate">{{ $student->user->name ?? 'N/A' }}</h3>
                                    <p class="text-xs text-[var(--text-muted)] font-mono">{{ $student->reg_number }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex-shrink-0">
                                {{ $student->programme ?: 'Science' }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 space-y-4">
                            @if($courseCount > 0)
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Assigned Courses ({{ $courseCount }})</span>
                                        <span class="font-mono font-bold text-xs text-[var(--text-secondary)]">{{ $student->subjects->sum('credit_hours') }} Credits</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($student->subjects->take(5) as $sub)
                                            <span class="course-badge font-mono">
                                                {{ $sub->code }}
                                            </span>
                                        @endforeach
                                        @if($courseCount > 5)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-500">
                                                +{{ $courseCount - 5 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Progress -->
                                <div class="space-y-1.5 pt-2">
                                    <div class="flex items-center justify-between text-xs text-[var(--text-secondary)]">
                                        <span>Completion Progress</span>
                                        <span class="font-mono font-bold">{{ $progress }}%</span>
                                    </div>
                                    <div class="progress-bar-wrap">
                                        <div class="bar {{ $barColor }}" style="width: {{ $progress }}%;"></div>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 rounded-xl border border-dashed border-[var(--border-color)] text-center text-zinc-400 space-y-1">
                                    <i class="fas fa-book-open text-xl opacity-40"></i>
                                    <p class="text-xs font-bold">No courses assigned</p>
                                    <p class="text-[10px]">Click Manage to allocate subjects</p>
                                </div>
                            @endif
                        </div>

                        <!-- Card Footer -->
                        <div class="p-4 border-t border-[var(--border-color)] bg-[var(--bg-card)] flex items-center justify-between gap-3">
                            <div class="quick-assign flex-1">
                                <select onchange="quickAssign({{ $student->id }}, this)"
                                        class="w-full px-2.5 py-1.5 text-xs bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-lg text-[var(--text-primary)] focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">+ Quick Assign Course</option>
                                    @foreach($subjects as $sub)
                                        @if(!$student->subjects->contains($sub->id))
                                            <option value="{{ $sub->id }}">{{ $sub->code }} - {{ $sub->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <a href="{{ route('admin.new-subject-assignment.show', $student) }}"
                               class="px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                                <span>Manage</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-16 text-center bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl text-zinc-400 space-y-3">
                        <i class="fas fa-user-graduate text-3xl opacity-40"></i>
                        <h4 class="text-base font-bold text-[var(--text-primary)]">No Registered Students Found</h4>
                        <p class="text-xs text-[var(--text-secondary)]">Add students first before managing course assignments.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ==================== 2. TABLE VIEW ==================== -->
        <div x-show="viewMode === 'table'" x-transition style="display: none;">
            <div class="bg-[var(--glass-bg)] backdrop-blur-sm border border-[var(--border-color)] rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[var(--border-color)] text-xs">
                        <thead>
                            <tr class="bg-gradient-to-r from-blue-500/10 to-transparent text-[var(--text-secondary)] font-mono font-bold uppercase tracking-wider text-left">
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Programme</th>
                                <th class="px-6 py-4">Assigned Courses</th>
                                <th class="px-6 py-4 text-center">Total Credits</th>
                                <th class="px-6 py-4">Progress</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)]" id="studentsTableBody">
                            @foreach($students as $student)
                                @php
                                    $courseCount = $student->subjects->count();
                                    $completed = $student->subjects->where('pivot.status', 'completed')->count();
                                    $progress = $courseCount > 0 ? round(($completed / $courseCount) * 100) : 0;
                                @endphp
                                <tr class="student-table-row hover:bg-blue-500/5 transition"
                                    data-name="{{ strtolower($student->user->name ?? '') }}"
                                    data-reg="{{ strtolower($student->reg_number ?? '') }}"
                                    data-programme="{{ strtolower($student->programme ?? '') }}"
                                    data-subjects="{{ $student->subjects->pluck('id')->join(',') }}"
                                    data-count="{{ $courseCount }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if($student->profile_picture)
                                                <img src="{{ Storage::url($student->profile_picture) }}" alt="{{ $student->user->name }}"
                                                     class="w-9 h-9 rounded-xl object-cover ring-2 ring-blue-500/30 flex-shrink-0">
                                            @else
                                                <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 font-bold text-xs flex items-center justify-center flex-shrink-0 font-mono">
                                                    {{ substr($student->user->name ?? 'S', 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-sm text-[var(--text-primary)]">{{ $student->user->name ?? 'N/A' }}</div>
                                                <div class="text-[11px] text-[var(--text-muted)] font-mono">{{ $student->reg_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full font-mono font-bold text-[11px] bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                            {{ $student->programme ?: 'General' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @forelse($student->subjects as $sub)
                                                <span class="course-badge font-mono">{{ $sub->code }}</span>
                                            @empty
                                                <span class="text-zinc-400 italic text-[11px]">None assigned</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-[var(--text-primary)]">
                                        {{ $student->subjects->sum('credit_hours') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="progress-bar-wrap w-20">
                                                <div class="bar bar-blue" style="width: {{ $progress }}%;"></div>
                                            </div>
                                            <span class="font-mono text-[11px] text-[var(--text-secondary)]">{{ $progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.new-subject-assignment.show', $student) }}"
                                           class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition inline-flex items-center gap-1 shadow-sm">
                                            <span>Manage</span>
                                            <i class="fas fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        // Quick Assign single course
        function quickAssign(studentId, selectElement) {
            const subjectId = selectElement.value;
            if (!subjectId) return;
            if (!confirm('Assign this course to the student?')) {
                selectElement.value = '';
                return;
            }
            fetch('{{ route("admin.new-subject-assignment.quick-assign") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_id: studentId, subject_id: subjectId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error assigning course.');
                    selectElement.value = '';
                }
            })
            .catch(() => { alert('Error assigning course.'); selectElement.value = ''; });
        }

        // Live Filtering for cards and table rows
        function filterStudents() {
            const search = (document.getElementById('searchInput')?.value || '').toLowerCase();
            const programme = (document.getElementById('programmeFilter')?.value || '').toLowerCase();
            const subject = document.getElementById('subjectFilter')?.value || '';
            const countRange = document.getElementById('courseCountFilter')?.value || '';

            // Filter Cards
            document.querySelectorAll('.student-card').forEach(card => {
                const name = card.dataset.name || '';
                const reg = card.dataset.reg || '';
                const cardProgramme = card.dataset.programme || '';
                const subjects = card.dataset.subjects ? card.dataset.subjects.split(',') : [];
                const count = parseInt(card.dataset.count || '0');

                const matchSearch = !search || name.includes(search) || reg.includes(search);
                const matchProgramme = !programme || cardProgramme === programme;
                const matchSubject = !subject || subjects.includes(subject);
                let matchCount = true;
                if (countRange === '0') matchCount = count === 0;
                else if (countRange === '1-3') matchCount = count >= 1 && count <= 3;
                else if (countRange === '4+') matchCount = count >= 4;

                const show = matchSearch && matchProgramme && matchSubject && matchCount;
                card.style.display = show ? '' : 'none';
            });

            // Filter Table Rows
            document.querySelectorAll('.student-table-row').forEach(row => {
                const name = row.dataset.name || '';
                const reg = row.dataset.reg || '';
                const rowProgramme = row.dataset.programme || '';
                const subjects = row.dataset.subjects ? row.dataset.subjects.split(',') : [];
                const count = parseInt(row.dataset.count || '0');

                const matchSearch = !search || name.includes(search) || reg.includes(search);
                const matchProgramme = !programme || rowProgramme === programme;
                const matchSubject = !subject || subjects.includes(subject);
                let matchCount = true;
                if (countRange === '0') matchCount = count === 0;
                else if (countRange === '1-3') matchCount = count >= 1 && count <= 3;
                else if (countRange === '4+') matchCount = count >= 4;

                const show = matchSearch && matchProgramme && matchSubject && matchCount;
                row.style.display = show ? '' : 'none';
            });
        }

        function resetAllFilters() {
            const search = document.getElementById('searchInput');
            const prog = document.getElementById('programmeFilter');
            const subj = document.getElementById('subjectFilter');
            const count = document.getElementById('courseCountFilter');

            if (search) search.value = '';
            if (prog) prog.value = '';
            if (subj) subj.value = '';
            if (count) count.value = '';

            filterStudents();
        }

        document.getElementById('searchInput')?.addEventListener('keyup', filterStudents);
        document.getElementById('programmeFilter')?.addEventListener('change', filterStudents);
        document.getElementById('subjectFilter')?.addEventListener('change', filterStudents);
        document.getElementById('courseCountFilter')?.addEventListener('change', filterStudents);
    </script>
    @endpush
@endsection
