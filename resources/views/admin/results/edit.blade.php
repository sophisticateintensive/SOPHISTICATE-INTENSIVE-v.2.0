@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Modify Assessment Score
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Update marks and grade classification for {{ $result->student->user->name ?? 'Student' }}</p>
        </div>

        <a href="{{ route('admin.results.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--glass-bg)] backdrop-blur-sm text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-card)] hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Results
        </a>
    </div>
@endsection

@section('content')
    <style>
        .split-create-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 2.5rem;
            box-shadow: 0 25px 80px var(--shadow-color);
            overflow: hidden;
            transition: background 0.4s, border-color 0.4s, box-shadow 0.4s;
            display: grid;
            grid-template-columns: 1fr 2fr;
            min-height: 620px;
            position: relative;
        }

        .split-create-container::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 2.5rem;
            padding: 2px;
            background: linear-gradient(135deg, rgba(59,130,246,0.3), rgba(139,92,246,0.3));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            z-index: 0;
        }

        @media (max-width: 1024px) {
            .split-create-container {
                grid-template-columns: 1fr;
                border-radius: 2rem;
            }
            .split-create-container::before { display: none; }
        }

        .brand-preview-panel {
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.06), rgba(37, 99, 235, 0.03));
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-right: 1px solid var(--border-color);
            transition: border-color 0.4s;
            position: relative;
            z-index: 1;
            justify-content: space-between;
        }

        @media (max-width: 1024px) {
            .brand-preview-panel {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 2rem 1.5rem;
            }
        }

        .form-panel {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 640px) {
            .form-panel { padding: 1.5rem; }
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .glass-input, .glass-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .glass-input:focus, .glass-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
            outline: none;
        }

        .glass-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
            padding-right: 2.5rem;
        }
    </style>

    <div class="split-create-container">
        <!-- ========== LEFT PANEL: LIVE RESULT CARD SIMULATION ========== -->
        <div class="brand-preview-panel">
            <div class="w-full">
                <!-- Glowing Icon -->
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl shadow-xl mx-auto mb-4">
                    <i class="fas fa-edit"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[var(--text-primary)]">Edit Score</h3>
                <p class="text-xs text-[var(--text-secondary)] mt-1">Live grading badge & status recalculation</p>

                <!-- Mock Grade Card -->
                <div class="mt-6 p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] text-left shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $result->exam_type == 'exam1' ? 'bg-blue-500/10 text-blue-500' : 'bg-purple-500/10 text-purple-500' }}" id="previewExamTypeBadge">
                            {{ $result->exam_type == 'exam1' ? 'Exam 1' : 'Exam 2' }}
                        </span>
                        <span class="text-[10px] text-[var(--text-muted)]">{{ $result->term->term_name ?? 'Term' }}</span>
                    </div>

                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-[var(--text-primary)] truncate">{{ $result->student->user->name ?? 'Student' }}</h4>
                        <p class="text-xs text-[var(--text-secondary)] truncate">{{ $result->subject->name ?? 'Course' }}</p>
                    </div>

                    <!-- Grade Circle & Percentage -->
                    <div class="p-3 bg-[var(--glass-bg)] rounded-xl border border-[var(--border-color)] flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Grade</span>
                            <div class="text-2xl font-black text-blue-500" id="previewGradeLetter">{{ $result->grade }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Score</span>
                            <div class="text-2xl font-black text-[var(--text-primary)]" id="previewMarksDisplay">{{ number_format($result->marks, 1) }}%</div>
                        </div>
                    </div>

                    <div class="text-center pt-1" id="previewStatusBanner">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $result->marks >= 40 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}" id="previewStatusText">
                            {{ $result->marks >= 70 ? 'Distinction' : ($result->marks >= 60 ? 'Merit' : ($result->marks >= 50 ? 'Credit' : ($result->marks >= 40 ? 'Pass' : 'Failed'))) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Grade scale reference -->
            <div class="w-full mt-6 pt-4 border-t border-[var(--border-color)] text-left text-[11px] text-[var(--text-muted)] space-y-1">
                <div class="flex justify-between"><span>A (70-100%): Distinction</span><span>D (40-49%): Pass</span></div>
                <div class="flex justify-between"><span>B (60-69%): Merit</span><span>F (&lt;40%): Fail</span></div>
                <div class="flex justify-between"><span>C (50-59%): Credit</span></div>
            </div>
        </div>

        <!-- ========== RIGHT PANEL: FORM INPUTS ========== -->
        <div class="form-panel">
            <form action="{{ route('admin.results.update', $result) }}" method="POST" id="resultForm" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Student Display (Locked to maintain integrity) -->
                <div class="form-group">
                    <label class="form-label">Recipient Student</label>
                    <input type="hidden" name="student_id" value="{{ $result->student_id }}">
                    <div class="px-4 py-3 bg-gray-100/60 dark:bg-gray-800/60 border border-[var(--border-color)] rounded-xl text-sm font-bold text-[var(--text-primary)] flex items-center justify-between">
                        <span>{{ $result->student->reg_number }} &bull; {{ $result->student->user->name ?? 'Student' }}</span>
                        <span class="text-xs font-mono text-[var(--text-muted)]">{{ $result->student->programme ?? '' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Subject Selection -->
                    <div class="form-group">
                        <label for="subject_id" class="form-label">
                            Subject Course <span class="text-red-500">*</span>
                        </label>
                        <select name="subject_id" id="subject_id" class="glass-select" required>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" {{ (old('subject_id', $result->subject_id) == $s->id) ? 'selected' : '' }}>
                                    {{ $s->code }} - {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Exam Type -->
                    <div class="form-group">
                        <label for="exam_type" class="form-label">
                            Exam Type <span class="text-red-500">*</span>
                        </label>
                        <select name="exam_type" id="exam_type" class="glass-select" required onchange="handleExamTypeChange(this)">
                            <option value="exam1" {{ old('exam_type', $result->exam_type) == 'exam1' ? 'selected' : '' }}>Exam 1 (First Assessment)</option>
                            <option value="exam2" {{ old('exam_type', $result->exam_type) == 'exam2' ? 'selected' : '' }}>Exam 2 (Second Assessment)</option>
                        </select>
                        @error('exam_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Academic Year -->
                    <div class="form-group">
                        <label for="academic_year_id" class="form-label">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <select name="academic_year_id" id="academic_year_id" class="glass-select" required>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $result->academic_year_id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->year_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Term -->
                    <div class="form-group">
                        <label for="term_id" class="form-label">
                            Term / Semester <span class="text-red-500">*</span>
                        </label>
                        <select name="term_id" id="term_id" class="glass-select" required>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ old('term_id', $result->term_id) == $term->id ? 'selected' : '' }}>
                                    {{ $term->term_name }} ({{ $term->academicYear->year_name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('term_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Marks & Grade Auto -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="marks" class="form-label">
                            Score Marks (0 - 100%) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="marks" id="marks" step="0.01" min="0" max="100" required
                            value="{{ old('marks', $result->marks) }}"
                            class="glass-input font-bold text-lg"
                            oninput="calculateGrade(this.value)">
                        @error('marks')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="grade" class="form-label">
                            Letter Grade (Auto-calculated)
                        </label>
                        <input type="text" name="grade" id="grade" readonly
                            value="{{ old('grade', $result->grade) }}"
                            class="glass-input font-black text-lg bg-gray-100/50 dark:bg-gray-800/50">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-[var(--border-color)]">
                    <a href="{{ route('admin.results.index') }}"
                        class="px-5 py-2.5 bg-[var(--bg-card)] text-[var(--text-secondary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function handleExamTypeChange(select) {
            const badge = document.getElementById('previewExamTypeBadge');
            badge.textContent = select.value === 'exam1' ? 'Exam 1' : 'Exam 2';
            badge.className = select.value === 'exam1' 
                ? 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-500'
                : 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-500';
        }

        function calculateGrade(marksVal) {
            const marks = parseFloat(marksVal);
            const gradeInput = document.getElementById('grade');
            const gradePreview = document.getElementById('previewGradeLetter');
            const marksPreview = document.getElementById('previewMarksDisplay');
            const statusText = document.getElementById('previewStatusText');

            if (isNaN(marks)) {
                gradeInput.value = '';
                gradePreview.textContent = '--';
                marksPreview.textContent = '0.0%';
                statusText.textContent = 'Awaiting Score Entry';
                statusText.className = 'px-3 py-1 rounded-full text-xs font-bold bg-gray-500/10 text-gray-500';
                return;
            }

            marksPreview.textContent = `${marks.toFixed(1)}%`;

            let grade = 'F';
            let status = 'Failed';
            let statusClass = 'px-3 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-500';

            if (marks >= 70) {
                grade = 'A';
                status = 'Distinction (Passed)';
                statusClass = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500';
            } else if (marks >= 60) {
                grade = 'B';
                status = 'Merit (Passed)';
                statusClass = 'px-3 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-500';
            } else if (marks >= 50) {
                grade = 'C';
                status = 'Credit (Passed)';
                statusClass = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-500';
            } else if (marks >= 40) {
                grade = 'D';
                status = 'Pass (Passed)';
                statusClass = 'px-3 py-1 rounded-full text-xs font-bold bg-orange-500/10 text-orange-500';
            }

            gradeInput.value = grade;
            gradePreview.textContent = grade;
            statusText.textContent = status;
            statusText.className = statusClass;
        }
    </script>
    @endpush
@endsection
