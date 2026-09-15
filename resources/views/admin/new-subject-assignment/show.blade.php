@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.new-subject-assignment.index') }}" class="hover:text-blue-500 transition">Course Matrix</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">{{ $student->user->name ?? 'Candidate' }}</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Candidate Course Matrix & Enrollments
            </h2>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.students.show', $student) }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-user mr-1.5 text-blue-500"></i>
                Student Profile
            </a>
            <a href="{{ route('admin.new-subject-assignment.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-arrow-left mr-1.5"></i>
                Back to Matrix
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $assignedSubjects = $student->subjects;
        $totalCredits = $assignedSubjects->sum('credit_hours');
        $enrolledCount = $assignedSubjects->where('pivot.status', 'enrolled')->count();
        $completedCount = $assignedSubjects->where('pivot.status', 'completed')->count();
        $droppedCount = $assignedSubjects->where('pivot.status', 'dropped')->count();
    @endphp

    <div class="space-y-8">

        <!-- ========== HERO / CANDIDATE PROFILE BANNER (STUDENT MANAGEMENT DESIGN) ========== -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 shadow-2xl">
            <!-- Animated blobs -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-2xl font-bold shadow-lg border border-white/20">
                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-1 rounded-full border border-white/20 text-xs font-medium text-white mb-2 font-mono">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            {{ $student->reg_number ?? 'REG-N/A' }} &bull; {{ $student->programme ?? 'Regular Programme' }}
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $student->user->name ?? 'Student' }}</h1>
                        <p class="text-blue-100/80 text-xs mt-0.5">{{ $student->user->email ?? '' }}</p>
                    </div>
                </div>

                <!-- Floating Bento Counters -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 font-mono">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-white">{{ $assignedSubjects->count() }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Courses</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-emerald-300">{{ $enrolledCount }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Enrolled</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-purple-300">{{ $completedCount }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Passed</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10">
                        <div class="text-2xl font-bold text-amber-300">{{ $totalCredits }}</div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Credit Hrs</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== QUICK ENROLL DRAWER & COURSE MATRIX ========== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT: ASSIGNED COURSES TABLE (2 COLS) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white tracking-wide">Assigned Course Matrix</h3>
                                <p class="text-xs text-blue-200 mt-0.5">{{ $assignedSubjects->count() }} modules allocated &bull; {{ $totalCredits }} total credit units</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        @if($assignedSubjects->count() > 0)
                            <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                                <table class="min-w-full divide-y divide-[var(--border-color)]">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Course</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Credits</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase">Status</th>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[var(--border-color)]">
                                        @foreach($assignedSubjects as $sub)
                                            <tr class="hover:bg-[var(--accent-soft)] transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 font-bold text-xs flex items-center justify-center font-mono">
                                                            {{ substr($sub->code, 0, 3) }}
                                                        </div>
                                                        <div>
                                                            <p class="font-bold text-sm text-[var(--text-primary)]">{{ $sub->name }}</p>
                                                            <p class="text-xs text-blue-500 font-mono font-bold">{{ $sub->code }}</p>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono font-bold text-[var(--text-primary)]">
                                                    {{ $sub->credit_hours ?? 3 }} Cr
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <form action="{{ route('admin.new-subject-assignment.updateStatus', [$student, $sub]) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status" onchange="this.form.submit()"
                                                                class="px-2.5 py-1 text-xs font-bold font-mono rounded-lg border border-[var(--border-color)] bg-[var(--bg-card)] cursor-pointer
                                                                {{ $sub->pivot->status === 'completed' ? 'text-purple-600' : ($sub->pivot->status === 'dropped' ? 'text-rose-600' : 'text-emerald-600') }}">
                                                            <option value="enrolled" {{ $sub->pivot->status === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                                            <option value="completed" {{ $sub->pivot->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="dropped" {{ $sub->pivot->status === 'dropped' ? 'selected' : '' }}>Dropped</option>
                                                        </select>
                                                    </form>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <form action="{{ route('admin.new-subject-assignment.remove', [$student, $sub]) }}" method="POST" id="remove-course-{{ $sub->id }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="openDeleteModal(
                                                            'Remove Course Assignment?',
                                                            'Are you sure you want to detach {{ addslashes($sub->name) }} ({{ $sub->code }}) from this candidate?',
                                                            document.getElementById('remove-course-{{ $sub->id }}')
                                                        )" class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 transition" title="Detach Course">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <p class="text-xs text-[var(--text-secondary)]">No courses currently assigned to this student. Use the provisioner on the right to attach courses.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- RIGHT: PROVISION NEW COURSES (1 COL) -->
            <div class="space-y-6">
                <form action="{{ route('admin.new-subject-assignment.assign', $student) }}" method="POST">
                    @csrf

                    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-lg space-y-5">
                        <h3 class="text-sm font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-1.5">
                            <i class="fas fa-plus-circle text-blue-500"></i>
                            <span>Provision New Courses</span>
                        </h3>

                        <!-- Academic Year & Term -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-[var(--text-secondary)] mb-1">Academic Session *</label>
                            <select name="academic_year_id" required class="w-full px-3.5 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-[var(--text-primary)] font-bold">
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $ay->is_current ? 'selected' : '' }}>{{ $ay->year_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-[var(--text-secondary)] mb-1">Semester Term *</label>
                            <select name="term_id" required class="w-full px-3.5 py-2.5 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-[var(--text-primary)] font-bold">
                                @foreach($terms as $t)
                                    <option value="{{ $t->id }}" {{ $t->is_current ? 'selected' : '' }}>{{ $t->term_name }} ({{ $t->academicYear->year_name ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Available Courses Checklist -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-[var(--text-secondary)] mb-2">Available Curriculum Modules</label>
                            <div class="max-h-60 overflow-y-auto space-y-2 p-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)]">
                                @forelse($availableSubjects as $avail)
                                    <label class="flex items-center space-x-2.5 p-2 rounded-lg hover:bg-[var(--accent-soft)] cursor-pointer select-none text-xs">
                                        <input type="checkbox" name="subject_ids[]" value="{{ $avail->id }}" class="w-4 h-4 text-blue-600 rounded">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-[var(--text-primary)] truncate">{{ $avail->name }}</p>
                                            <p class="text-[10px] text-blue-500 font-mono">{{ $avail->code }} &bull; {{ $avail->credit_hours ?? 3 }} Cr</p>
                                        </div>
                                    </label>
                                @empty
                                    <p class="text-xs text-[var(--text-muted)] text-center py-4">All available curriculum courses are already assigned.</p>
                                @endforelse
                            </div>
                        </div>

                        <button type="submit"
                                :disabled="{{ $availableSubjects->count() === 0 ? 'true' : 'false' }}"
                                class="w-full py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow hover:scale-102 transition">
                            Assign Selected Modules
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection
