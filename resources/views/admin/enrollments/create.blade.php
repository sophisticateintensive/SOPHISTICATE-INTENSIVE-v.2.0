@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                New Student Enrollment
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Enroll a student for an academic year and term</p>
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
        .enroll-container {
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
        .enroll-container::before {
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
            .enroll-container {
                grid-template-columns: 1fr;
                border-radius: 2rem;
            }
            .enroll-container::before { display: none; }
        }

        /* ----- Left panel – branding & stats ----- */
        .brand-panel {
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
            justify-content: center;
        }

        @media (max-width: 1024px) {
            .brand-panel {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 2rem 1.5rem;
            }
        }

        .brand-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            filter: drop-shadow(0 8px 30px rgba(59,130,246,0.2));
            animation: floatLogo 6s ease-in-out infinite;
            margin-bottom: 1.5rem;
        }
        @keyframes floatLogo {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-8px) scale(1.02); }
            100% { transform: translateY(0px) scale(1); }
        }

        .brand-title {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-sub {
            font-size: 0.7rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }
        .brand-tagline {
            font-size: 0.9rem;
            color: var(--text-secondary);
            max-width: 240px;
            line-height: 1.6;
            margin: 1rem auto;
        }

        .brand-stats {
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
        }
        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(59,130,246,0.1);
        }
        .stat-item .number {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .stat-item .label {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            letter-spacing: 0.05em;
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
        .input-field:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Student search dropdown (glass) */
        .search-results {
            position: absolute;
            z-index: 20;
            width: 100%;
            margin-top: 0.25rem;
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 20px 60px var(--shadow-color);
            max-height: 220px;
            overflow-y: auto;
            display: none;
        }
        .search-results .result-item {
            padding: 0.6rem 1rem;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid var(--border-color);
        }
        .search-results .result-item:last-child { border-bottom: none; }
        .search-results .result-item:hover {
            background: rgba(59,130,246,0.08);
        }
        .search-results .result-item .highlight {
            background: #fbbf24;
            font-weight: 600;
        }
        .search-results .empty-state {
            padding: 1.5rem;
            text-align: center;
            color: var(--text-secondary);
        }

        /* Selected student card */
        .selected-student {
            margin-top: 0.5rem;
            padding: 0.75rem 1rem;
            background: rgba(59,130,246,0.06);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: 1rem;
            display: none;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s;
        }
        .selected-student.visible {
            display: flex;
        }
        .selected-student .info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .selected-student .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Info box */
        .info-box {
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid rgba(59, 130, 246, 0.15);
            border-radius: 1.5rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .info-box .icon { color: #3b82f6; flex-shrink: 0; margin-top: 0.1rem; }

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
            .brand-logo { width: 70px; height: 70px; }
            .brand-title { font-size: 1.4rem; }
            .form-panel { padding: 1.5rem 1rem; }
            .section-card { padding: 1rem; }
            .enroll-container { margin-bottom: 90px; }
            .fixed-actions { padding: 0.75rem 1rem; justify-content: center; }
            .fixed-actions .btn { flex: 1; justify-content: center; padding: 0.6rem 1rem; font-size: 0.9rem; }
        }
    </style>

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="enroll-container">
        <!-- Left Panel: Branding & Stats -->
        <div class="brand-panel">
            <img src="{{ asset('images/logo.png') }}" alt="Sophisticate Logo" class="brand-logo">
            <div class="brand-title">Sophisticate Intensive</div>
            <div class="brand-sub">CLASSES</div>
            <p class="brand-tagline">Empowering academic excellence through specialised instruction.</p>

            @php
                $totalStudents = \App\Models\Student::count();
                $totalEnrollments = \App\Models\StudentEnrollment::count();
                $activeEnrollments = \App\Models\StudentEnrollment::where('status', 'active')->count();
                $totalProgrammes = \App\Models\Student::distinct('programme')->count('programme') ?? 0;
            @endphp

            <div class="brand-stats" x-data="{
                students: 0, enrollments: 0, active: 0, programmes: 0,
                init() {
                    let targets = {
                        students: {{ $totalStudents }},
                        enrollments: {{ $totalEnrollments }},
                        active: {{ $activeEnrollments }},
                        programmes: {{ $totalProgrammes }}
                    };
                    let steps = 30;
                    let duration = 700;
                    let interval = duration / steps;
                    let i = 0;
                    let timer = setInterval(() => {
                        i++;
                        this.students = Math.round((targets.students / steps) * i);
                        this.enrollments = Math.round((targets.enrollments / steps) * i);
                        this.active = Math.round((targets.active / steps) * i);
                        this.programmes = Math.round((targets.programmes / steps) * i);
                        if (i >= steps) {
                            this.students = targets.students;
                            this.enrollments = targets.enrollments;
                            this.active = targets.active;
                            this.programmes = targets.programmes;
                            clearInterval(timer);
                        }
                    }, interval);
                }
            }">
                <div class="stat-item">
                    <div class="number" x-text="students">0</div>
                    <div class="label">Students</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="enrollments">0</div>
                    <div class="label">Enrollments</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="active">0</div>
                    <div class="label">Active</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="programmes">0</div>
                    <div class="label">Programmes</div>
                </div>
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

            <form action="{{ route('admin.enrollments.store') }}" method="POST" id="enrollmentForm">
                @csrf

                <!-- Student Search -->
                <div class="section-card" id="student-search-container">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Select Student
                        <span class="badge-required">Required</span>
                    </div>
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="student_search"
                            placeholder="Type to search for a student by name or registration number..."
                            class="input-field" autocomplete="off">
                        <button type="button" onclick="clearSearch()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Hidden select -->
                    <select name="student_id" id="student_id" class="hidden" required>
                        <option value="" selected>No student selected</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" data-reg="{{ $student->reg_number }}"
                                data-name="{{ $student->user->name }}" data-programme="{{ $student->programme }}">
                                {{ $student->reg_number }} - {{ $student->user->name }} ({{ $student->programme }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Search results -->
                    <div id="search-results" class="search-results"></div>

                    <!-- Selected student display -->
                    <div id="selected-student" class="selected-student">
                        <div class="info">
                            <div class="avatar" id="selected-avatar">S</div>
                            <div>
                                <div class="text-sm font-medium text-[var(--text-primary)]" id="selected-name"></div>
                                <div class="text-xs text-[var(--text-secondary)]" id="selected-details"></div>
                                <div class="text-xs text-blue-600" id="selected-programme"></div>
                            </div>
                        </div>
                        <button type="button" onclick="clearSelectedStudent()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @error('student_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Programme (auto-populated) -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Programme
                        <span class="badge-required" style="background: #10b981;">Auto-filled</span>
                    </div>
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <input type="text" id="programme_display" class="input-field"
                            placeholder="Select a student first" readonly disabled>
                        <input type="hidden" name="programme" id="programme" value="">
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
                        Academic Period
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
                            <select name="academic_year_id" id="academic_year_id" class="input-field" required>
                                <option value="" disabled {{ old('academic_year_id') ? '' : 'selected' }}>Select academic year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->year_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Term -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <select name="term_id" id="term_id" class="input-field" required>
                                <option value="" disabled {{ old('term_id') ? '' : 'selected' }}>Select a term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" data-academic-year-id="{{ $term->academic_year_id }}"
                                        {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->term_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('academic_year_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('term_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                            <select name="status" id="status" class="input-field" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="withdrawn" {{ old('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                            </select>
                        </div>

                        <!-- Enrollment Date -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" name="enrollment_date" id="enrollment_date"
                                value="{{ old('enrollment_date', now()->format('Y-m-d')) }}"
                                class="input-field @error('enrollment_date') border-red-300 dark:border-red-700 @enderror" required>
                        </div>

                        <!-- Expected Graduation -->
                        <div class="input-group md:col-span-2">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" name="expected_graduation_date" id="expected_graduation_date"
                                value="{{ old('expected_graduation_date') }}"
                                class="input-field @error('expected_graduation_date') border-red-300 dark:border-red-700 @enderror">
                        </div>
                    </div>
                    @error('enrollment_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('expected_graduation_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                        <textarea name="notes" id="notes" rows="2"
                            class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white/60 dark:bg-gray-800/60 text-current"
                            placeholder="Add any additional notes about this enrollment...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <svg class="w-5 h-5 icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h5 class="text-sm font-semibold text-blue-800 dark:text-blue-300">Enrollment Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1 mt-1 text-xs text-blue-600 dark:text-blue-400">
                            <div>✓ Each student can only be enrolled once per academic year and term</div>
                            <div>✓ Active status means the student is currently enrolled and studying</div>
                            <div>✓ Programme is automatically populated from student record</div>
                            <div>✓ Enrollment date defaults to today's date</div>
                        </div>
                    </div>
                </div>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            Enroll Student
        </button>
    </div>

    @push('scripts')
    <script>
        // ---- Student Search Logic ----
        const studentSelect = document.getElementById('student_id');
        const students = Array.from(studentSelect.options).slice(1).map(opt => ({
            id: opt.value,
            reg: opt.getAttribute('data-reg'),
            name: opt.getAttribute('data-name'),
            programme: opt.getAttribute('data-programme'),
            displayText: opt.text,
            searchText: `${opt.getAttribute('data-reg')} ${opt.getAttribute('data-name')} ${opt.getAttribute('data-programme')}`.toLowerCase()
        }));

        const searchInput = document.getElementById('student_search');
        const resultsContainer = document.getElementById('search-results');
        const selectedDiv = document.getElementById('selected-student');
        const selectedName = document.getElementById('selected-name');
        const selectedDetails = document.getElementById('selected-details');
        const selectedProgramme = document.getElementById('selected-programme');
        const selectedAvatar = document.getElementById('selected-avatar');
        const programmeDisplay = document.getElementById('programme_display');
        const programmeHidden = document.getElementById('programme');

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('student-search-container');
            if (!container.contains(e.target)) {
                resultsContainer.style.display = 'none';
            }
        });

        // Show results on focus if there's a query
        searchInput.addEventListener('focus', function() {
            if (this.value.length >= 1) {
                performSearch(this.value);
            }
        });

        function performSearch(query) {
            const term = query.toLowerCase().trim();
            if (term.length === 0) {
                resultsContainer.style.display = 'none';
                return;
            }

            const filtered = students.filter(s => s.searchText.includes(term)).slice(0, 10);
            displayResults(filtered, term);
        }

        function displayResults(results, term) {
            if (results.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="empty-state">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-sm">No students found</p>
                        <p class="text-xs text-gray-400 mt-1">Try a different search term</p>
                    </div>
                `;
                resultsContainer.style.display = 'block';
                return;
            }

            let html = '';
            results.forEach(s => {
                const highlighted = s.displayText.replace(
                    new RegExp(term, 'gi'),
                    match => `<span class="highlight">${match}</span>`
                );
                html += `
                    <div class="result-item" onclick="selectStudent('${s.id}', '${s.name}', '${s.reg}', '${s.programme}')">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                                ${s.name.charAt(0)}
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-[var(--text-primary)]">${highlighted}</div>
                                <div class="text-xs text-[var(--text-secondary)]">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">${s.reg}</span>
                                    <span class="ml-2">${s.programme}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            resultsContainer.innerHTML = html;
            resultsContainer.style.display = 'block';
        }

        window.selectStudent = function(id, name, reg, programme) {
            studentSelect.value = id;
            selectedName.textContent = name;
            selectedDetails.textContent = reg;
            selectedProgramme.textContent = 'Programme: ' + programme;
            selectedAvatar.textContent = name.charAt(0);
            selectedDiv.classList.add('visible');

            programmeDisplay.value = programme;
            programmeHidden.value = programme;

            searchInput.value = '';
            resultsContainer.style.display = 'none';
            studentSelect.dispatchEvent(new Event('change'));
        };

        window.clearSelectedStudent = function() {
            studentSelect.value = '';
            selectedDiv.classList.remove('visible');
            programmeDisplay.value = '';
            programmeHidden.value = '';
            searchInput.focus();
        };

        window.clearSearch = function() {
            searchInput.value = '';
            resultsContainer.style.display = 'none';
            searchInput.focus();
        };

        // Debounced search
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        searchInput.addEventListener('input', debounce(function(e) {
            const q = e.target.value;
            if (q.length >= 1) {
                performSearch(q);
            } else {
                resultsContainer.style.display = 'none';
            }
        }, 300));

        // ---- Term filtering ----
        function filterTermsByAcademicYear() {
            const yearId = document.getElementById('academic_year_id').value;
            const termSelect = document.getElementById('term_id');
            const options = termSelect.querySelectorAll('option');

            let hasVisible = false;
            let firstVisible = null;

            options.forEach(opt => {
                if (opt.value === '') {
                    opt.style.display = 'block';
                    return;
                }
                const optYear = opt.getAttribute('data-academic-year-id');
                if (!yearId || optYear === yearId) {
                    opt.style.display = 'block';
                    hasVisible = true;
                    if (!firstVisible) firstVisible = opt.value;
                } else {
                    opt.style.display = 'none';
                    if (opt.selected) opt.selected = false;
                }
            });

            if (hasVisible && (!termSelect.value || termSelect.value === '')) {
                if (firstVisible) termSelect.value = firstVisible;
            }
            if (!hasVisible && yearId) {
                termSelect.value = '';
            }
        }

        document.getElementById('academic_year_id').addEventListener('change', filterTermsByAcademicYear);

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            filterTermsByAcademicYear();

            // Restore old selection if any
            @if(old('student_id'))
                const oldStudent = students.find(s => s.id == {{ old('student_id') }});
                if (oldStudent) {
                    selectStudent(oldStudent.id, oldStudent.name, oldStudent.reg, oldStudent.programme);
                }
            @endif
        });
    </script>
    @endpush
@endsection
