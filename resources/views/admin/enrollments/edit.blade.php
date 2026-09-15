@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Edit Enrollment
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Update student enrollment information</p>
        </div>

        <a href="{{ route('admin.enrollments.index') }}"
            class="inline-flex items-center px-3 sm:px-4 py-2 bg-[var(--glass-bg)] backdrop-blur-sm text-[var(--text-primary)] text-sm font-semibold rounded-lg border border-[var(--border-color)] hover:bg-[var(--bg-card)] hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Enrollments
        </a>
    </div>
@endsection

@section('content')
    <style>
        /* ----- CSS Variables for theming (inherited from admin) ----- */
        :root {
            --bg-primary: #f0f7ff;
            --bg-card: rgba(255, 255, 255, 0.7);
            --border-color: rgba(255, 255, 255, 0.3);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --shadow-color: rgba(59, 130, 246, 0.15);
            --blob-opacity: 0.3;
            --glass-blur: 16px;
        }

        body.dark-mode {
            --bg-primary: #0a0e1a;
            --bg-card: rgba(10, 14, 26, 0.7);
            --border-color: rgba(255, 255, 255, 0.06);
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --shadow-color: rgba(59, 130, 246, 0.25);
            --blob-opacity: 0.2;
            --glass-blur: 20px;
        }

        /* ----- Animated blobs ----- */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: var(--blob-opacity);
            animation: blobMorph 15s ease-in-out infinite alternate;
            transition: background 0.6s;
            z-index: -1;
        }
        .blob-1 { width: 40vw; height: 40vw; background: #3b82f6; top: -10%; right: -10%; }
        .blob-2 { width: 35vw; height: 35vw; background: #2563eb; bottom: -10%; left: -10%; animation-delay: 5s; }
        .blob-3 { width: 25vw; height: 25vw; background: #60a5fa; top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: 10s; }

        @keyframes blobMorph {
            0% { border-radius: 50% 50% 50% 50%; transform: translate(0,0) scale(1); }
            25% { border-radius: 60% 40% 50% 50%; transform: translate(30px,-20px) scale(1.1); }
            50% { border-radius: 40% 60% 60% 40%; transform: translate(-20px,30px) scale(0.9); }
            75% { border-radius: 50% 30% 70% 50%; transform: translate(40px,10px) scale(1.05); }
            100% { border-radius: 50% 50% 50% 50%; transform: translate(0,0) scale(1); }
        }

        /* ----- Main container (glass) ----- */
        .edit-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            background: var(--bg-card);
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border: 1px solid var(--border-color);
            border-radius: 2.5rem;
            box-shadow: 0 25px 80px var(--shadow-color);
            overflow: hidden;
            transition: background 0.4s, border-color 0.4s, box-shadow 0.4s;
            display: grid;
            grid-template-columns: 1fr 2.2fr;
            min-height: 600px;
            position: relative;
        }
        .edit-container::before {
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
            .edit-container {
                grid-template-columns: 1fr;
                border-radius: 2rem;
            }
            .edit-container::before { display: none; }
        }

        /* ----- Left panel – profile & stats (glass) ----- */
        .profile-panel {
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
        }

        @media (max-width: 1024px) {
            .profile-panel {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 2rem 1.5rem;
            }
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 12px 40px rgba(59, 130, 246, 0.3);
            margin-bottom: 1.5rem;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        .profile-avatar:hover {
            transform: scale(1.05) rotate(-2deg);
            box-shadow: 0 16px 50px rgba(59, 130, 246, 0.4);
        }
        .profile-avatar .status-dot {
            position: absolute;
            bottom: 6px;
            right: 6px;
            width: 18px;
            height: 18px;
            background: {{ $enrollment->status === 'active' ? '#10b981' : ($enrollment->status === 'graduated' ? '#3b82f6' : ($enrollment->status === 'suspended' ? '#f59e0b' : '#ef4444')) }};
            border-radius: 50%;
            border: 3px solid var(--bg-card);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        .profile-name {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .profile-reg {
            font-size: 0.9rem;
            font-family: monospace;
            color: var(--text-secondary);
            background: var(--bg-card);
            padding: 0.15rem 1rem;
            border-radius: 9999px;
            border: 1px solid var(--border-color);
            display: inline-block;
            margin-bottom: 0.75rem;
        }

        .profile-badge {
            display: inline-block;
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 0.25rem 1.25rem;
            border-radius: 9999px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            margin-bottom: 1.5rem;
        }

        .profile-stats {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .stat-item {
            background: var(--bg-card);
            border-radius: 1rem;
            padding: 0.75rem 0.5rem;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(4px);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }
        .stat-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(59,130,246,0.1);
        }
        .stat-item .number {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .stat-item .label {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            letter-spacing: 0.05em;
        }
        .stat-item .trend {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            font-size: 0.7rem;
            background: rgba(16,185,129,0.15);
            color: #10b981;
            padding: 0.1rem 0.4rem;
            border-radius: 9999px;
        }

        /* ----- Right panel – form (glass) ----- */
        .form-panel {
            padding: 2.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            overflow-y: auto;
            max-height: 80vh;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 1024px) {
            .form-panel {
                padding: 2rem 1.5rem;
                max-height: none;
            }
        }

        .section-card {
            background: var(--bg-card);
            backdrop-filter: blur(4px);
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }
        .section-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px var(--shadow-color);
        }
        .section-card .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        .section-card .section-title svg {
            color: #3b82f6;
            width: 1.4rem;
            height: 1.4rem;
        }
        .section-card .section-title .badge-required {
            font-size: 0.65rem;
            background: #ef4444;
            color: white;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            margin-left: 0.5rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.25rem;
        }
        .input-group .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            pointer-events: none;
            transition: color 0.3s;
            z-index: 2;
        }
        .input-group:focus-within .input-icon {
            color: #3b82f6;
        }
        .input-field {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 2px solid var(--border-color);
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.3);
            color: var(--text-primary);
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.15);
            outline: none;
            background: rgba(255,255,255,0.6);
            transform: scale(1.01);
        }
        .dark-mode .input-field {
            background: rgba(255,255,255,0.05);
        }
        .dark-mode .input-field:focus {
            background: rgba(255,255,255,0.08);
        }
        select.input-field {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
            padding-right: 2.5rem;
        }

        /* Info box */
        .info-box {
            background: rgba(251, 191, 36, 0.08);
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 1.5rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .info-box .icon { color: #f59e0b; flex-shrink: 0; margin-top: 0.1rem; }

        /* ----- Fixed action buttons ----- */
        .fixed-actions {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
            z-index: 100;
            box-shadow: 0 -4px 20px var(--shadow-color);
            transition: background 0.4s, border-color 0.4s;
        }
        .fixed-actions .btn {
            padding: 0.6rem 1.8rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            box-shadow: 0 4px 12px rgba(59,130,246,0.25);
        }
        .btn-primary:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 20px rgba(59,130,246,0.35);
        }
        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            border-color: #3b82f6;
            transform: scale(1.03);
        }

        @media (max-width: 640px) {
            .profile-avatar { width: 90px; height: 90px; font-size: 2.2rem; }
            .profile-name { font-size: 1.4rem; }
            .form-panel { padding: 1.5rem 1rem; }
            .section-card { padding: 1rem; }
            .edit-container { margin-bottom: 90px; }
            .fixed-actions { padding: 0.75rem 1rem; justify-content: center; }
            .fixed-actions .btn { flex: 1; justify-content: center; padding: 0.6rem 1rem; font-size: 0.9rem; }
        }
    </style>

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="edit-container">
        <!-- Left Panel: Profile -->
        <div class="profile-panel">
            <div class="profile-avatar">
                {{ substr($enrollment->student->user->name, 0, 1) }}
                <span class="status-dot"></span>
            </div>
            <div class="profile-name">{{ $enrollment->student->user->name }}</div>
            <div class="profile-reg">{{ $enrollment->student->reg_number }}</div>
            <span class="profile-badge">{{ $enrollment->programme }}</span>

            <!-- Stats -->
            @php
                $totalResults = $enrollment->student->results->count();
                $totalFees = $enrollment->student->fees->sum('amount');
                $totalPaid = $enrollment->student->fees->sum('paid');
                $balance = $totalFees - $totalPaid;
            @endphp
            <div class="profile-stats" x-data="{
                results: 0,
                fees: 0,
                balance: 0,
                init() {
                    let targetResults = {{ $totalResults }};
                    let targetFees = {{ $totalFees }};
                    let targetBalance = {{ $balance }};
                    let duration = 700;
                    let steps = 30;
                    let interval = duration / steps;
                    let i = 0;
                    let timer = setInterval(() => {
                        i++;
                        this.results = Math.round((targetResults / steps) * i);
                        this.fees = Math.round((targetFees / steps) * i);
                        this.balance = Math.round((targetBalance / steps) * i);
                        if (i >= steps) {
                            this.results = targetResults;
                            this.fees = targetFees;
                            this.balance = targetBalance;
                            clearInterval(timer);
                        }
                    }, interval);
                }
            }">
                <div class="stat-item">
                    <div class="number" x-text="results">0</div>
                    <div class="label">Results</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="fees.toLocaleString()">0</div>
                    <div class="label">Fees (MK)</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="balance.toLocaleString()">0</div>
                    <div class="label">Balance</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ $enrollment->student->subjects->count() }}</div>
                    <div class="label">Subjects</div>
                </div>
            </div>

            <div class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                Enrolled {{ $enrollment->enrollment_date->format('M d, Y') }}
                @if($enrollment->expected_graduation_date)
                    · Expected {{ \Carbon\Carbon::parse($enrollment->expected_graduation_date)->format('M d, Y') }}
                @endif
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div class="form-panel">
            @if($errors->any())
                <div class="section-card" style="border-color: rgba(239,68,68,0.3); background: rgba(239,68,68,0.05);">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h5 class="text-sm font-semibold text-red-700">Please fix the following errors:</h5>
                            <ul class="text-xs text-red-600 list-disc list-inside mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST" id="enrollmentForm">
                @csrf
                @method('PUT')

                <!-- Student Info (read-only) -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Student
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($enrollment->student->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-semibold text-[var(--text-primary)]">{{ $enrollment->student->user->name }}</h4>
                            <p class="text-sm text-[var(--text-secondary)]">{{ $enrollment->student->reg_number }}</p>
                        </div>
                    </div>
                    <input type="hidden" name="student_id" value="{{ $enrollment->student_id }}">
                </div>

                <!-- Programme -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Programme
                        <span class="badge-required">Required</span>
                    </div>
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <input type="text" name="programme" id="programme" value="{{ old('programme', $enrollment->programme) }}"
                            class="input-field @error('programme') border-red-300 dark:border-red-700 @enderror"
                            placeholder="e.g., Computer Science, Information Technology">
                    </div>
                    @error('programme')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Year & Term -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Enrollment Details
                        <span class="badge-required">Required</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Academic Year -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <select name="academic_year_id" id="academic_year_id" required
                                class="input-field @error('academic_year_id') border-red-300 dark:border-red-700 @enderror">
                                <option value="">-- Select Academic Year --</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $enrollment->academic_year_id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->year_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('academic_year_id')
                            <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p>
                        @enderror

                        <!-- Term -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <select name="term_id" id="term_id" required
                                class="input-field @error('term_id') border-red-300 dark:border-red-700 @enderror">
                                <option value="">-- Select Term --</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}"
                                        data-academic-year-id="{{ $term->academic_year_id }}"
                                        {{ old('term_id', $enrollment->term_id) == $term->id ? 'selected' : '' }}>
                                        {{ $term->term_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('term_id')
                            <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status & Dates -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Status & Dates
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Status -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <select name="status" id="status" required
                                class="input-field @error('status') border-red-300 dark:border-red-700 @enderror">
                                <option value="active" {{ old('status', $enrollment->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="graduated" {{ old('status', $enrollment->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="suspended" {{ old('status', $enrollment->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="withdrawn" {{ old('status', $enrollment->status) == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                            </select>
                        </div>
                        @error('status')
                            <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p>
                        @enderror

                        <!-- Enrollment Date -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" name="enrollment_date" id="enrollment_date"
                                value="{{ old('enrollment_date', $enrollment->enrollment_date->format('Y-m-d')) }}"
                                class="input-field @error('enrollment_date') border-red-300 dark:border-red-700 @enderror">
                        </div>
                        @error('enrollment_date')
                            <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p>
                        @enderror

                        <!-- Expected Graduation -->
                        <div class="input-group md:col-span-2">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" name="expected_graduation_date" id="expected_graduation_date"
                                value="{{ old('expected_graduation_date', $enrollment->expected_graduation_date?->format('Y-m-d')) }}"
                                class="input-field @error('expected_graduation_date') border-red-300 dark:border-red-700 @enderror">
                        </div>
                        @error('expected_graduation_date')
                            <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Notes (Optional)
                    </div>
                    <div class="input-group">
                        <div class="input-icon" style="top:1.2rem; transform:none;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <textarea name="notes" id="notes" rows="3"
                            class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white/60 dark:bg-gray-800/60 text-current"
                            placeholder="Add any additional notes about this enrollment...">{{ old('notes', $enrollment->notes) }}</textarea>
                    </div>
                </div>

                <!-- Info Box (status change warning) -->
                @if(old('status', $enrollment->status) != 'active' && old('status', $enrollment->status) == 'active')
                    <div class="info-box">
                        <svg class="w-5 h-5 icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h5 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">Reactivating Student</h5>
                            <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-0.5">
                                Changing status to "Active" will reactivate this student's enrollment. Make sure the student is eligible to resume studies.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Hidden: form actions are in fixed bar -->
            </form>
        </div>
    </div>

    <!-- Fixed Action Buttons -->
    <div class="fixed-actions">
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
        </a>
        <button type="submit" form="enrollmentForm" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Update Enrollment
        </button>
    </div>

    @push('scripts')
    <script>
        // Filter terms based on selected academic year
        function filterTermsByAcademicYear() {
            const academicYearId = document.getElementById('academic_year_id').value;
            const termSelect = document.getElementById('term_id');
            const options = termSelect.querySelectorAll('option');

            let hasVisibleOption = false;
            let selectedOptionVisible = false;

            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }

                const optionAcademicYearId = option.getAttribute('data-academic-year-id');

                if (!academicYearId || optionAcademicYearId === academicYearId) {
                    option.style.display = 'block';
                    hasVisibleOption = true;
                    if (option.selected) {
                        selectedOptionVisible = true;
                    }
                } else {
                    option.style.display = 'none';
                    if (option.selected) {
                        option.selected = false;
                        selectedOptionVisible = false;
                    }
                }
            });

            // Auto-select first available term if current selection is hidden
            if (!selectedOptionVisible && hasVisibleOption && academicYearId) {
                const firstVisibleOption = Array.from(options).find(opt => opt.style.display !== 'none' && opt.value !== '');
                if (firstVisibleOption) {
                    firstVisibleOption.selected = true;
                }
            }

            // Reset term select if no valid options
            if (!hasVisibleOption && academicYearId) {
                termSelect.value = '';
            }
        }

        // Event listeners
        document.getElementById('academic_year_id').addEventListener('change', filterTermsByAcademicYear);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            filterTermsByAcademicYear();
        });
    </script>
    @endpush
@endsection
