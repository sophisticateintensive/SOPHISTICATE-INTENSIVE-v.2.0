@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.results.index') }}" class="hover:text-blue-500 transition">Academic Results</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">Record Official Score</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Record Assessment Score
            </h2>
        </div>

        <a href="{{ route('admin.results.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-1.5"></i>
            Back to Results
        </a>
    </div>
@endsection

@section('content')
    <div x-data="resultCalculator()" class="max-w-5xl mx-auto space-y-8">

        <!-- ========== HERO BANNER (STUDENT MANAGEMENT DESIGN) ========== -->
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
                        Official Examination Ledger
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Score Entry & Automated Grading</h1>
                    <p class="text-blue-100/80 mt-1">Automatic GPA point evaluation, letter grading, and transcript synchronization</p>
                </div>

                <!-- Live Evaluation Scorecard -->
                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-center min-w-[200px] shadow-lg">
                    <span class="text-[10px] text-blue-200 uppercase font-mono tracking-wider block">Computed Grade</span>
                    <p class="text-4xl font-black text-white font-mono mt-1" x-text="computedGrade()"></p>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full inline-block mt-1 font-mono"
                          :class="isPassing() ? 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30' : 'bg-rose-400/20 text-rose-300 border border-rose-400/30'"
                          x-text="isPassing() ? 'PASS (' + computedGPA() + ' GPA)' : 'FAIL (0.0 GPA)'"></span>
                </div>
            </div>
        </div>

        <!-- ========== SCORE ENTRY FORM & LIVE CALCULATOR (GLASS) ========== -->
        <form action="{{ route('admin.results.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT: PARAMETERS & SELECTION (2 COLS) -->
                <div class="lg:col-span-2 bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 sm:p-8 border border-[var(--border-color)] shadow-lg space-y-5">
                    <h3 class="text-base font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-2">
                        <i class="fas fa-user-graduate text-blue-500"></i>
                        <span>Candidate & Course Selection</span>
                    </h3>

                    <!-- Student Select -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Enrolled Student *</label>
                        <select name="student_id" required
                                class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] cursor-pointer">
                            <option value="">Select Candidate...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->user->name ?? 'Student' }} &mdash; {{ $student->reg_number ?? 'REG-N/A' }} ({{ $student->programme ?? 'Regular' }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Subject Select -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Subject Course *</label>
                        <select name="subject_id" required
                                class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] cursor-pointer">
                            <option value="">Select Academic Subject...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->code }} &mdash; {{ $subject->name }} ({{ $subject->credit_hours ?? 3 }} Credits)
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Academic Year & Term -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Academic Session *</label>
                            <select name="academic_year_id" required
                                    class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-bold cursor-pointer">
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $ay->is_current ? 'selected' : '' }}>{{ $ay->year_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Semester Term *</label>
                            <select name="term_id" required
                                    class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-bold cursor-pointer">
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ $term->is_current ? 'selected' : '' }}>
                                        {{ $term->term_name }} ({{ $term->academicYear->year_name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Exam Type Selection -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Examination Period *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 p-3.5 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="exam_type" value="exam1" checked class="w-4 h-4 text-blue-600">
                                <div>
                                    <span class="text-xs font-bold text-[var(--text-primary)] block">Exam 1 (Midterm)</span>
                                    <span class="text-[10px] text-[var(--text-secondary)]">First semester evaluation</span>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 p-3.5 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="exam_type" value="exam2" class="w-4 h-4 text-blue-600">
                                <div>
                                    <span class="text-xs font-bold text-[var(--text-primary)] block">Exam 2 (Final Exam)</span>
                                    <span class="text-[10px] text-[var(--text-secondary)]">End of semester final</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Remarks & Notes -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Examiner Remarks (Optional)</label>
                        <textarea name="remarks" rows="2" placeholder="e.g. Excellent conceptual grasp, high performance in practical module..."
                                  class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <!-- RIGHT: MARKS INPUT & LIVE BREAKDOWN (1 COL) -->
                <div class="space-y-6">
                    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-lg space-y-5">
                        <h3 class="text-sm font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-1.5">
                            <i class="fas fa-calculator text-indigo-500"></i>
                            <span>Mark Entry & Live Matrix</span>
                        </h3>

                        <!-- Score Input -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Examination Score (0 &mdash; 100) *</label>
                            <div class="relative">
                                <input type="number" step="0.5" min="0" max="100" name="marks" required
                                       x-model="rawScore"
                                       placeholder="Enter mark (e.g. 85.5)"
                                       class="w-full px-4 py-3 text-lg font-black font-mono bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)]">
                                <span class="absolute right-4 top-3 text-sm text-[var(--text-muted)] font-mono font-bold">/ 100</span>
                            </div>
                            @error('marks') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Live Telemetry Matrix -->
                        <div class="space-y-2.5 pt-2 text-xs font-mono">
                            <div class="p-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] flex items-center justify-between">
                                <span class="text-[var(--text-muted)] uppercase text-[10px]">Percentage</span>
                                <span class="font-bold text-sm text-[var(--text-primary)]" x-text="(rawScore || 0) + '%'"></span>
                            </div>

                            <div class="p-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] flex items-center justify-between">
                                <span class="text-[var(--text-muted)] uppercase text-[10px]">Grade Standard</span>
                                <span class="font-bold text-sm text-blue-500" x-text="computedGrade() + ' (' + gradeDescription() + ')'"></span>
                            </div>

                            <div class="p-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] flex items-center justify-between">
                                <span class="text-[var(--text-muted)] uppercase text-[10px]">GPA Point Weight</span>
                                <span class="font-bold text-sm text-emerald-500" x-text="computedGPA() + ' / 4.0'"></span>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            <span>Save & Publish Official Result</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
@endsection

@push('scripts')
<script>
    function resultCalculator() {
        return {
            rawScore: {{ old('marks', 75) }},

            computedGrade() {
                const s = parseFloat(this.rawScore) || 0;
                if (s >= 70) return 'A';
                if (s >= 60) return 'B';
                if (s >= 50) return 'C';
                if (s >= 40) return 'D';
                return 'F';
            },

            computedGPA() {
                const s = parseFloat(this.rawScore) || 0;
                if (s >= 70) return '4.0';
                if (s >= 60) return '3.0';
                if (s >= 50) return '2.0';
                if (s >= 40) return '1.0';
                return '0.0';
            },

            isPassing() {
                return (parseFloat(this.rawScore) || 0) >= 50;
            },

            gradeDescription() {
                const s = parseFloat(this.rawScore) || 0;
                if (s >= 70) return 'Distinction';
                if (s >= 60) return 'Credit';
                if (s >= 50) return 'Pass';
                if (s >= 40) return 'Poor Pass';
                return 'Fail';
            }
        };
    }
</script>
@endpush