@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Compose Message
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Send a direct message or academic inquiry to a student</p>
        </div>

        <a href="{{ route('admin.messages.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--glass-bg)] backdrop-blur-sm text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-card)] hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Messages
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
            margin-bottom: 1.5rem;
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

        .glass-input, .glass-select, .glass-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .glass-input:focus, .glass-select:focus, .glass-textarea:focus {
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

        .template-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            background: var(--glass-bg);
            border: 1px solid var(--border-color);
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
        }
        .template-chip:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: rgba(59,130,246,0.08);
            transform: translateY(-1px);
        }
    </style>

    <div class="split-create-container">
        <!-- ========== LEFT PANEL: LIVE PREVIEW & RECIPIENT INFO ========== -->
        <div class="brand-preview-panel">
            <div class="w-full">
                <!-- Glowing Icon Header -->
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl shadow-xl mx-auto mb-4">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[var(--text-primary)]">Message Dispatch</h3>
                <p class="text-xs text-[var(--text-secondary)] mt-1">Direct private communication channel</p>

                <!-- Live Student Card Preview -->
                <div class="mt-6 p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] text-left shadow-sm">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm" id="previewAvatar">
                            ?
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-[var(--text-primary)]" id="previewStudentName">No Student Selected</h4>
                            <p class="text-xs text-[var(--text-muted)]" id="previewStudentReg">Select from list</p>
                        </div>
                    </div>

                    <!-- Live Message Balloon -->
                    <div class="p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-xs text-[var(--text-secondary)] space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-bold text-blue-500 uppercase tracking-wider">
                            <span>Message Preview</span>
                            <span>{{ now()->format('h:i A') }}</span>
                        </div>
                        <p class="text-xs text-[var(--text-primary)] leading-relaxed line-clamp-4 italic" id="previewMessageText">
                            "Start typing your message to see how it will appear..."
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats/Tips Footer -->
            <div class="w-full mt-6 pt-4 border-t border-[var(--border-color)] text-left">
                <div class="flex items-center space-x-2 text-xs text-[var(--text-muted)]">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>Messages are delivered instantly to student portals.</span>
                </div>
            </div>
        </div>

        <!-- ========== RIGHT PANEL: FORM INPUTS ========== -->
        <div class="form-panel">
            <form action="{{ route('admin.messages.store') }}" method="POST" id="composeForm">
                @csrf

                <!-- Hidden fields for academic year and term -->
                <input type="hidden" name="academic_year_id" id="academic_year_id" value="">
                <input type="hidden" name="term_id" id="term_id" value="">

                <!-- Student Selection -->
                <div class="form-group">
                    <label for="student_id" class="form-label">
                        Recipient Student <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id" id="student_id" class="glass-select" required onchange="handleStudentSelect(this)">
                        <option value="" disabled selected>-- Select an enrolled student --</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}"
                                data-name="{{ $student->user->name ?? 'Student' }}"
                                data-reg="{{ $student->reg_number ?? '' }}"
                                data-year="{{ $student->activeEnrollment->academic_year_id ?? '' }}"
                                data-term="{{ $student->activeEnrollment->term_id ?? '' }}"
                                data-prog="{{ $student->programme ?? '' }}"
                                {{ (request('student_id') == $student->id || old('student_id') == $student->id) ? 'selected' : '' }}>
                                {{ $student->reg_number }} &bull; {{ $student->user->name ?? 'No Name' }} ({{ $student->programme ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick Templates -->
                <div class="mb-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-2">Quick Message Starters</span>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="template-chip" onclick="insertTemplate('Tuition Fee Reminder', 'Please be reminded that your pending tuition balance for this term is due soon. Kindly settle at the accounts office.')">
                            <i class="fas fa-wallet mr-1 text-xs text-amber-500"></i> Fee Reminder
                        </button>
                        <button type="button" class="template-chip" onclick="insertTemplate('Class Attendance Notice', 'This is to follow up on your recent attendance in scheduled intensive classes. Please contact the administration if you need assistance.')">
                            <i class="fas fa-user-check mr-1 text-xs text-blue-500"></i> Attendance Follow-up
                        </button>
                        <button type="button" class="template-chip" onclick="insertTemplate('Exam Registration Update', 'Your exam registration details for the upcoming assessment have been verified. Please review your subject enrollments.')">
                            <i class="fas fa-file-alt mr-1 text-xs text-emerald-500"></i> Exam Verification
                        </button>
                    </div>
                </div>

                <!-- Message Body -->
                <div class="form-group">
                    <div class="flex items-center justify-between mb-1">
                        <label for="message" class="form-label mb-0">
                            Message Content <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[11px] text-[var(--text-muted)]" id="charCounter">0 characters</span>
                    </div>
                    <textarea name="message" id="message" rows="6" class="glass-textarea" required
                        placeholder="Type your private message to the student here..."
                        oninput="updateMessagePreview(this)">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-[var(--border-color)]">
                    <a href="{{ route('admin.messages.index') }}"
                        class="px-5 py-2.5 bg-[var(--bg-card)] text-[var(--text-secondary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function handleStudentSelect(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt) return;

            const name = opt.getAttribute('data-name') || 'Selected Student';
            const reg = opt.getAttribute('data-reg') || '';
            const year = opt.getAttribute('data-year') || '';
            const term = opt.getAttribute('data-term') || '';

            document.getElementById('previewStudentName').textContent = name;
            document.getElementById('previewStudentReg').textContent = reg ? `Reg: ${reg}` : '';
            document.getElementById('previewAvatar').textContent = name.charAt(0).toUpperCase();

            document.getElementById('academic_year_id').value = year;
            document.getElementById('term_id').value = term;
        }

        function updateMessagePreview(textarea) {
            const text = textarea.value.trim();
            const preview = document.getElementById('previewMessageText');
            const counter = document.getElementById('charCounter');

            preview.textContent = text ? `"${text}"` : '"Start typing your message to see how it will appear..."';
            counter.textContent = `${textarea.value.length} characters`;
        }

        function insertTemplate(subject, body) {
            const textarea = document.getElementById('message');
            textarea.value = body;
            updateMessagePreview(textarea);
            textarea.focus();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('student_id');
            if (sel && sel.value) handleStudentSelect(sel);
            const msg = document.getElementById('message');
            if (msg && msg.value) updateMessagePreview(msg);
        });
    </script>
    @endpush
@endsection
