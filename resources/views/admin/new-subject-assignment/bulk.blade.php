@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.new-subject-assignment.index') }}" class="hover:text-blue-500 transition">Course Matrix</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">Bulk Assignment Studio</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Bulk Course Assignment Studio
            </h2>
        </div>

        <a href="{{ route('admin.new-subject-assignment.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-1.5"></i>
            Back to Matrix
        </a>
    </div>
@endsection

@section('content')
    @php
        $totalStudentsCount = $students->count();
        $totalSubjectsCount = $subjects->count();
    @endphp

    <div x-data="bulkAssignStudio()" class="space-y-8">

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
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        Active Session: {{ $activeTerm->term_name ?? 'Active Semester' }}
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Cohort Course Provisioning Studio</h1>
                    <p class="text-blue-100/80 mt-1">Simultaneously assign core modules and electives to entire student cohorts</p>
                </div>

                <!-- Floating Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 font-mono">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-white">{{ $totalStudentsCount }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Candidates</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-white">{{ $totalSubjectsCount }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Courses</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-emerald-300" x-text="selectedStudents.length"></div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Selected</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-amber-300" x-text="selectedStudents.length * selectedSubjects.length"></div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Assignments</div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.new-subject-assignment.bulkAssign') }}" method="POST" id="bulkAssignForm">
            @csrf

            <!-- ========== DUAL-PANE SELECTION STUDIO ========== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- LEFT PANE: STUDENT COHORT SELECTOR -->
                <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl border border-[var(--border-color)] shadow-lg overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between text-white">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-sm font-bold">1</div>
                                <div>
                                    <h3 class="font-bold text-base">Select Student Candidates</h3>
                                    <p class="text-xs text-blue-200"><span x-text="selectedStudents.length"></span> of {{ $totalStudentsCount }} selected</p>
                                </div>
                            </div>

                            <button type="button" @click="toggleSelectAllStudents()"
                                    class="px-3 py-1 rounded-lg bg-white/20 hover:bg-white/30 text-xs font-bold transition">
                                <span x-text="selectedStudents.length === filteredStudentsCount ? 'Deselect All' : 'Select All'"></span>
                            </button>
                        </div>

                        <!-- Filter Controls -->
                        <div class="p-4 border-b border-[var(--border-color)] space-y-3 bg-[var(--bg-card)]/50">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                                    <i class="fas fa-search text-xs"></i>
                                </div>
                                <input type="text" x-model="studentSearch" placeholder="Search by candidate name or reg number..."
                                       class="w-full pl-9 pr-4 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                            </div>
                        </div>

                        <!-- Student List Scroll Container -->
                        <div class="p-4 max-h-[480px] overflow-y-auto space-y-2 divide-y divide-[var(--border-color)]/40">
                            @foreach($students as $s)
                                <label class="flex items-center space-x-3 p-3 rounded-xl cursor-pointer transition select-none pt-3"
                                       x-show="matchesStudent('{{ strtolower(addslashes($s->user->name ?? '')) }}', '{{ strtolower(addslashes($s->reg_number ?? '')) }}')"
                                       :class="selectedStudents.includes('{{ $s->id }}') ? 'bg-blue-500/10 border border-blue-500/30' : 'hover:bg-[var(--accent-soft)]'">
                                    <input type="checkbox" name="student_ids[]" value="{{ $s->id }}"
                                           x-model="selectedStudents"
                                           class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                        {{ substr($s->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-[var(--text-primary)] truncate">{{ $s->user->name ?? 'Unknown' }}</p>
                                        <p class="text-[11px] text-[var(--text-secondary)] font-mono">{{ $s->reg_number ?? 'N/A' }} &bull; {{ $s->programme ?? 'Regular' }}</p>
                                    </div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-[var(--bg-card)] text-[var(--text-muted)] border border-[var(--border-color)]">
                                        {{ $s->subjects->count() }} courses
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- RIGHT PANE: COURSE CATALOG SELECTOR -->
                <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl border border-[var(--border-color)] shadow-lg overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between text-white">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-sm font-bold">2</div>
                                <div>
                                    <h3 class="font-bold text-base">Select Target Courses</h3>
                                    <p class="text-xs text-blue-200"><span x-text="selectedSubjects.length"></span> of {{ $totalSubjectsCount }} selected</p>
                                </div>
                            </div>

                            <button type="button" @click="toggleSelectAllSubjects()"
                                    class="px-3 py-1 rounded-lg bg-white/20 hover:bg-white/30 text-xs font-bold transition">
                                <span x-text="selectedSubjects.length === filteredSubjectsCount ? 'Deselect All' : 'Select All'"></span>
                            </button>
                        </div>

                        <!-- Filter Controls -->
                        <div class="p-4 border-b border-[var(--border-color)] bg-[var(--bg-card)]/50">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                                    <i class="fas fa-search text-xs"></i>
                                </div>
                                <input type="text" x-model="subjectSearch" placeholder="Search by course code or title..."
                                       class="w-full pl-9 pr-4 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                            </div>
                        </div>

                        <!-- Subject List Scroll Container -->
                        <div class="p-4 max-h-[480px] overflow-y-auto space-y-2 divide-y divide-[var(--border-color)]/40">
                            @foreach($subjects as $sub)
                                <label class="flex items-center space-x-3 p-3 rounded-xl cursor-pointer transition select-none pt-3"
                                       x-show="matchesSubject('{{ strtolower(addslashes($sub->code)) }}', '{{ strtolower(addslashes($sub->name)) }}')"
                                       :class="selectedSubjects.includes('{{ $sub->id }}') ? 'bg-indigo-500/10 border border-indigo-500/30' : 'hover:bg-[var(--accent-soft)]'">
                                    <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}"
                                           x-model="selectedSubjects"
                                           class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 font-bold text-xs flex items-center justify-center flex-shrink-0 font-mono">
                                        {{ substr($sub->code, 0, 3) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-[var(--text-primary)] truncate">{{ $sub->name }}</p>
                                        <p class="text-[11px] text-blue-500 font-mono font-bold">{{ $sub->code }}</p>
                                    </div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)]">
                                        {{ $sub->credit_hours ?? 3 }} Cr
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========== EXECUTION STICKY BAR (GLASS) ========== -->
            <div class="mt-8 bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Semester & Academic Year Configuration -->
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-[var(--text-muted)] mb-1">Academic Year</label>
                        <select name="academic_year_id" required class="px-3 py-2 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-[var(--text-primary)] font-bold cursor-pointer">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ $ay->is_current ? 'selected' : '' }}>{{ $ay->year_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-[var(--text-muted)] mb-1">Semester Term</label>
                        <select name="term_id" required class="px-3 py-2 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-[var(--text-primary)] font-bold cursor-pointer">
                            @foreach($terms as $t)
                                <option value="{{ $t->id }}" {{ $t->is_current ? 'selected' : '' }}>{{ $t->term_name }} ({{ $t->academicYear->year_name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="border-l border-[var(--border-color)] pl-4">
                        <span class="text-[10px] text-[var(--text-muted)] uppercase block font-bold">Projected Workload</span>
                        <p class="text-sm font-bold text-[var(--text-primary)]">
                            <span class="text-blue-500 font-mono" x-text="selectedStudents.length"></span> Candidates &times;
                            <span class="text-indigo-500 font-mono" x-text="selectedSubjects.length"></span> Courses =
                            <span class="text-emerald-500 font-black font-mono" x-text="selectedStudents.length * selectedSubjects.length"></span> Total Allocations
                        </p>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.new-subject-assignment.index') }}" class="px-4 py-2.5 rounded-xl bg-[var(--bg-card)] text-[var(--text-secondary)] text-xs font-bold hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            :disabled="selectedStudents.length === 0 || selectedSubjects.length === 0"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 disabled:opacity-30 disabled:pointer-events-none text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                        <i class="fas fa-layer-group"></i>
                        <span>Execute Bulk Provisioning</span>
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
<script>
    function bulkAssignStudio() {
        return {
            studentSearch: '',
            subjectSearch: '',
            selectedStudents: [],
            selectedSubjects: [],
            allStudentIds: [@foreach($students as $s) '{{ $s->id }}', @endforeach],
            allSubjectIds: [@foreach($subjects as $sub) '{{ $sub->id }}', @endforeach],
            filteredStudentsCount: {{ $totalStudentsCount }},
            filteredSubjectsCount: {{ $totalSubjectsCount }},

            matchesStudent(name, reg) {
                if (!this.studentSearch) return true;
                const q = this.studentSearch.toLowerCase().trim();
                return name.includes(q) || reg.includes(q);
            },

            matchesSubject(code, name) {
                if (!this.subjectSearch) return true;
                const q = this.subjectSearch.toLowerCase().trim();
                return code.includes(q) || name.includes(q);
            },

            toggleSelectAllStudents() {
                if (this.selectedStudents.length > 0) {
                    this.selectedStudents = [];
                } else {
                    this.selectedStudents = [...this.allStudentIds];
                }
            },

            toggleSelectAllSubjects() {
                if (this.selectedSubjects.length > 0) {
                    this.selectedSubjects = [];
                } else {
                    this.selectedSubjects = [...this.allSubjectIds];
                }
            }
        };
    }
</script>
@endpush
