@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Edit Course
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Update course information and credit hours</p>
        </div>

        <a href="{{ route('admin.subjects.index') }}"
            class="inline-flex items-center px-3 sm:px-4 py-2 bg-[var(--glass-bg)] backdrop-blur-sm text-[var(--text-primary)] text-sm font-semibold rounded-lg border border-[var(--border-color)] hover:bg-[var(--bg-card)] hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Courses
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

        /* Current course info box */
        .current-info {
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid rgba(59, 130, 246, 0.15);
            border-radius: 1.5rem;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .current-info .badge {
            background: rgba(59,130,246,0.15);
            color: #3b82f6;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
        }

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
        <!-- Left Panel: Branding & Stats -->
        <div class="brand-panel">
            <img src="{{ asset('images/logo.png') }}" alt="Sophisticate Logo" class="brand-logo">
            <div class="brand-title">Sophisticate Intensive</div>
            <div class="brand-sub">CLASSES</div>
            <p class="brand-tagline">Empowering academic excellence through specialised instruction.</p>

            @php
                $totalCourses = \App\Models\Subject::count();
                $totalCredits = \App\Models\Subject::sum('credit_hours');
                $avgCredits = $totalCourses > 0 ? number_format($totalCredits / $totalCourses, 1) : 0;
            @endphp

            <div class="brand-stats" x-data="{
                courses: 0, credits: 0, avgCredits: 0,
                init() {
                    let targets = { courses: {{ $totalCourses }}, credits: {{ $totalCredits }}, avgCredits: {{ $avgCredits }} };
                    let steps = 30, duration = 700, interval = duration / steps;
                    let i = 0;
                    let timer = setInterval(() => {
                        i++;
                        this.courses = Math.round((targets.courses / steps) * i);
                        this.credits = Math.round((targets.credits / steps) * i);
                        this.avgCredits = parseFloat(((targets.avgCredits / steps) * i).toFixed(1));
                        if (i >= steps) {
                            this.courses = targets.courses;
                            this.credits = targets.credits;
                            this.avgCredits = targets.avgCredits;
                            clearInterval(timer);
                        }
                    }, interval);
                }
            }">
                <div class="stat-item">
                    <div class="number" x-text="courses">0</div>
                    <div class="label">Courses</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="credits">0</div>
                    <div class="label">Total Credits</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="avgCredits">0</div>
                    <div class="label">Avg Credits</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ \App\Models\Subject::where('created_at', '>=', now()->subMonth())->count() }}</div>
                    <div class="label">New (30d)</div>
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

            <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" id="courseForm">
                @csrf
                @method('PUT')

                <!-- Current Course Info -->
                <div class="current-info">
                    <span class="text-sm font-medium text-[var(--text-primary)]">Currently Editing:</span>
                    <span class="badge">{{ $subject->code }}</span>
                    <span class="text-sm text-[var(--text-secondary)]">{{ $subject->name }}</span>
                    <span class="text-sm text-[var(--text-secondary)]">•</span>
                    <span class="text-sm text-[var(--text-secondary)]">{{ $subject->credit_hours }} credit(s)</span>
                </div>

                <!-- Course Code & Name -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                        </svg>
                        Course Details
                        <span class="badge-required">Required</span>
                    </div>

                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                            </svg>
                        </div>
                        <input type="text" name="code" id="code" value="{{ old('code', $subject->code) }}"
                            class="input-field @error('code') border-red-300 dark:border-red-700 @enderror"
                            placeholder="Course Code (e.g., CS101)" required>
                    </div>
                    @error('code')
                        <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p>
                    @enderror

                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}"
                            class="input-field @error('name') border-red-300 dark:border-red-700 @enderror"
                            placeholder="Course Name (e.g., Introduction to Computer Science)" required>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Credit Hours -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Credit Hours
                        <span class="badge-required">Required</span>
                    </div>

                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <select name="credit_hours" id="credit_hours" class="input-field @error('credit_hours') border-red-300 dark:border-red-700 @enderror" required>
                            <option value="" disabled>Select credit hours</option>
                            @foreach([1, 2, 3, 4, 5, 6] as $hours)
                                <option value="{{ $hours }}" {{ old('credit_hours', $subject->credit_hours) == $hours ? 'selected' : '' }}>
                                    {{ $hours }} {{ Str::plural('credit', $hours) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('credit_hours')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-3 gap-2 mt-4 text-center text-xs text-[var(--text-secondary)]">
                        <div><span class="font-medium">1-2</span> – Light</div>
                        <div><span class="font-medium">3-4</span> – Standard</div>
                        <div><span class="font-medium">5-6</span> – Heavy</div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fixed Action Buttons -->
    <div class="fixed-actions">
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
        </a>
        <button type="submit" form="courseForm" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Update Course
        </button>
    </div>
@endsection
