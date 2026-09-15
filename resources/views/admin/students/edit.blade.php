@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Edit Student
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Update student information and enrollment details</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.students.show', $student) }}"
                class="inline-flex items-center px-3 sm:px-4 py-2 bg-[var(--bg-card)] backdrop-blur-sm text-blue-600 text-sm font-semibold rounded-lg border border-[var(--border-color)] hover:bg-[var(--bg-card)] hover:scale-105 transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Profile
            </a>

            <a href="{{ route('admin.students.index') }}"
                class="inline-flex items-center px-3 sm:px-4 py-2 bg-[var(--bg-card)] backdrop-blur-sm text-[var(--text-primary)] text-sm font-semibold rounded-lg border border-[var(--border-color)] hover:bg-[var(--bg-card)] hover:scale-105 transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>
    </div>
@endsection

@section('content')
    <style>
        /* ----- Animated blobs (page-specific) ----- */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.2;
            animation: blobMorph 15s ease-in-out infinite alternate;
            transition: background 0.6s;
            z-index: -1;
            pointer-events: none;
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
            margin: 0 auto 100px auto;
            background: var(--bg-card);
            backdrop-filter: blur(var(--glass-blur, 16px));
            -webkit-backdrop-filter: blur(var(--glass-blur, 16px));
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
                margin-bottom: 120px;
            }
            .edit-container::before { display: none; }
        }

        /* ----- Left panel – profile & stats (glass) ----- */
        .profile-panel {
            background: linear-gradient(145deg, var(--accent-soft), transparent);
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
            background: var(--brand-gradient, linear-gradient(135deg, #3b82f6, #8b5cf6));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 12px 40px var(--accent-glow);
            margin-bottom: 1.5rem;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        .profile-avatar:hover {
            transform: scale(1.05) rotate(-2deg);
            box-shadow: 0 16px 50px var(--accent-glow);
        }
        .profile-avatar .status-dot {
            position: absolute;
            bottom: 6px;
            right: 6px;
            width: 18px;
            height: 18px;
            background: #10b981;
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
            transition: color 0.3s;
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
            transition: background 0.3s;
        }

        .profile-badge {
            display: inline-block;
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 600;
            font-size: 0.8rem;
            padding: 0.25rem 1.25rem;
            border-radius: 9999px;
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }

        /* Animated stats */
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
            box-shadow: 0 8px 25px var(--shadow-color);
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

        /* Progress ring */
        .progress-ring {
            width: 80px;
            height: 80px;
            margin: 1rem auto;
            position: relative;
        }
        .progress-ring svg {
            transform: rotate(-90deg);
        }
        .progress-ring .bg {
            fill: none;
            stroke: var(--border-color);
            stroke-width: 6;
        }
        .progress-ring .fg {
            fill: none;
            stroke: var(--accent);
            stroke-width: 6;
            stroke-linecap: round;
            transition: stroke-dashoffset 1s ease;
        }
        .progress-ring .center-text {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ----- Right panel – form (glass) ----- */
        .form-panel {
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            position: relative;
            z-index: 1;
            overflow: visible;
        }

        @media (max-width: 1024px) {
            .form-panel {
                padding: 2rem 1.5rem 1.5rem 1.5rem;
            }
        }

        /* Section cards */
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
            color: var(--accent);
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

        /* Floating labels and glass inputs */
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
            color: var(--accent);
        }
        .input-field {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 2px solid var(--border-color);
            border-radius: 1rem;
            background: var(--bg-card);
            color: var(--text-primary);
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .input-field:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-soft);
            outline: none;
            background: var(--bg-card);
            transform: scale(1.01);
        }
        .input-field.valid {
            border-color: #10b981;
            background: rgba(16,185,129,0.08);
        }
        .input-field.invalid {
            border-color: #ef4444;
            background: rgba(239,68,68,0.08);
        }
        select.input-field {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
            padding-right: 2.5rem;
        }

        /* Password reset card */
        .password-reset-box {
            background: var(--accent-soft);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 1.5rem;
            backdrop-filter: blur(4px);
        }
        .password-reset-box .warning-icon {
            color: var(--accent);
        }

        /* Fixed action buttons */
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
            background: var(--accent-gradient);
            color: white;
            box-shadow: 0 4px 12px var(--accent-glow);
        }
        .btn-primary:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 20px var(--accent-glow);
        }
        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            border-color: var(--accent);
            transform: scale(1.03);
        }

        /* Scroll to top */
        .scroll-top {
            position: fixed;
            bottom: 100px;
            right: 1.5rem;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--bg-card);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px var(--shadow-color);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 99;
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }
        .scroll-top.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
        .scroll-top:hover {
            transform: scale(1.1);
            border-color: var(--accent);
        }

        @media (max-width: 640px) {
            .fixed-actions {
                padding: 0.75rem 1rem;
                justify-content: center;
            }
            .fixed-actions .btn {
                flex: 1;
                justify-content: center;
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
            .profile-avatar { width: 90px; height: 90px; font-size: 2.2rem; }
            .profile-name { font-size: 1.4rem; }
            .form-panel { padding: 1.5rem 1rem; }
            .section-card { padding: 1rem; }
            .progress-ring { width: 60px; height: 60px; }
            .edit-container { margin-bottom: 110px; }
            .scroll-top { bottom: 90px; right: 1rem; width: 38px; height: 38px; }
        }
    </style>

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Main container -->
    <div class="edit-container" x-data="livePreview()" x-init="initPreview()">
        <!-- Left: Profile Panel -->
        <div class="profile-panel">
            <div class="profile-avatar" x-text="getInitials()">
                {{ substr($student->user->name, 0, 1) }}
                <span class="status-dot"></span>
            </div>
            <div class="profile-name" x-text="formData.name || '{{ $student->user->name }}'">{{ $student->user->name }}</div>
            <div class="profile-reg" x-text="formData.reg_number || '{{ $student->reg_number }}'">{{ $student->reg_number }}</div>
            <span class="profile-badge" x-text="formData.programme || '{{ $student->programme }}'">{{ $student->programme }}</span>

            @php
                $fields = ['name' => $student->user->name, 'email' => $student->user->email, 'reg_number' => $student->reg_number, 'programme' => $student->programme];
                $filled = count(array_filter($fields));
                $total = count($fields);
                $percent = round(($filled / $total) * 100);
                $circumference = 2 * pi() * 34;
                $offset = $circumference - ($percent / 100) * $circumference;
            @endphp
            <div class="progress-ring" x-data="{ percent: {{ $percent }} }" x-init="
                $watch('formData', () => {
                    let filled = 0;
                    if (formData.name) filled++;
                    if (formData.email) filled++;
                    if (formData.reg_number) filled++;
                    if (formData.programme) filled++;
                    percent = Math.round((filled / 4) * 100);
                    const ring = $el.querySelector('.fg');
                    const circumference = 2 * Math.PI * 34;
                    const offset = circumference - (percent / 100) * circumference;
                    ring.style.strokeDashoffset = offset;
                    $el.querySelector('.center-text').textContent = percent + '%';
                });
            ">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle class="bg" cx="40" cy="40" r="34" />
                    <circle class="fg" cx="40" cy="40" r="34" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" />
                </svg>
                <div class="center-text">{{ $percent }}%</div>
            </div>
            <p class="text-xs text-[var(--text-muted)] -mt-1">Profile complete</p>

            <div class="profile-stats" x-data="{
                subjects: 0,
                results: 0,
                fees: 0,
                balance: 0,
                init() {
                    let targetSubjects = {{ $student->subjects->count() }};
                    let targetResults = {{ $student->results->count() }};
                    let targetFees = {{ $student->fees->sum('amount') }};
                    let targetBalance = {{ $student->fees->sum('balance') }};
                    let duration = 800;
                    let steps = 30;
                    let interval = duration / steps;
                    let stepSubjects = targetSubjects / steps;
                    let stepResults = targetResults / steps;
                    let stepFees = targetFees / steps;
                    let stepBalance = targetBalance / steps;
                    let i = 0;
                    let timer = setInterval(() => {
                        i++;
                        this.subjects = Math.round(stepSubjects * i);
                        this.results = Math.round(stepResults * i);
                        this.fees = Math.round(stepFees * i);
                        this.balance = Math.round(stepBalance * i);
                        if (i >= steps) {
                            this.subjects = targetSubjects;
                            this.results = targetResults;
                            this.fees = targetFees;
                            this.balance = targetBalance;
                            clearInterval(timer);
                        }
                    }, interval);
                }
            }">
                <div class="stat-item">
                    <div class="number" x-text="subjects">0</div>
                    <div class="label">Subjects</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="results">0</div>
                    <div class="label">Results</div>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="fees.toLocaleString()">0</div>
                    <div class="label">Fees (MK)</div>
                    <span class="trend">+{{ $student->fees->where('created_at', '>=', now()->subMonth())->sum('amount') }}</span>
                </div>
                <div class="stat-item">
                    <div class="number" x-text="balance.toLocaleString()">0</div>
                    <div class="label">Balance</div>
                </div>
            </div>

            <div class="mt-4 text-xs text-[var(--text-muted)]">
                Joined {{ $student->created_at->format('M d, Y') }} · Updated {{ $student->updated_at->diffForHumans() }}
            </div>
        </div>

        <!-- Right: Form Panel -->
        <div class="form-panel">
            <form action="{{ route('admin.students.update', $student) }}" method="POST" id="studentForm" @submit.prevent="submitForm">
                @csrf
                @method('PUT')

                <!-- Personal Information Card -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Personal Information
                        <span class="badge-required">Required</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $student->user->name) }}"
                                class="input-field @error('name') border-red-300 @enderror"
                                placeholder="Full Name" required
                                @input="formData.name = $event.target.value; checkValidity($event.target)">
                        </div>
                        @error('name') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Email -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', $student->user->email) }}"
                                class="input-field @error('email') border-red-300 @enderror"
                                placeholder="Email Address" required
                                @input="formData.email = $event.target.value; checkValidity($event.target)">
                        </div>
                        @error('email') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Registration Number -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                </svg>
                            </div>
                            <input type="text" name="reg_number" id="reg_number"
                                value="{{ old('reg_number', $student->reg_number) }}"
                                class="input-field @error('reg_number') border-red-300 @enderror"
                                placeholder="Registration Number" required
                                @input="formData.reg_number = $event.target.value; checkValidity($event.target)">
                        </div>
                        @error('reg_number') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Programme -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <select name="programme" id="programme"
                                class="input-field @error('programme') border-red-300 @enderror" required
                                @change="formData.programme = $event.target.value">
                                <option value="">Select Programme</option>
                                @php
                                    $programmes = ['BRESE','BSPHE','BSCHE','BSDS','BICT','BSPDVC','BSMAT','BBCM','BEDICT','BSBS','BSTRP','BSLS','BSEM','BEDS','BSVCA','BSFAS','BSTCD','BSWREM'];
                                @endphp
                                @foreach($programmes as $prog)
                                    <option value="{{ $prog }}" {{ old('programme', $student->programme) == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('programme') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Source of Funding -->
                        <div class="md:col-span-2" x-data="{ fundingSource: '{{ old('source_of_funding', $student->source_of_funding ?? 'Not Specified') }}' }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-1">Source of Funding <span class="text-red-500">*</span></label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <select name="source_of_funding" id="source_of_funding" x-model="fundingSource"
                                    class="input-field @error('source_of_funding') border-red-300 @enderror" required>
                                    @foreach(\App\Models\Student::FUNDING_SOURCES as $source)
                                        <option value="{{ $source }}" {{ old('source_of_funding', $student->source_of_funding) == $source ? 'selected' : '' }}>{{ $source }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('source_of_funding') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                            <!-- Custom input if Other is chosen -->
                            <div x-show="fundingSource === 'Other'" class="mt-2" style="display: none;">
                                <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-1">Specify Other Funding Source *</label>
                                <input type="text" name="funding_source_other" id="funding_source_other"
                                    value="{{ old('funding_source_other', $student->funding_source_other) }}"
                                    placeholder="e.g. Rotary Foundation Grant, NGO Sponsorship..."
                                    class="w-full px-4 py-2.5 border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm">
                                @error('funding_source_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact & Guardian Information Card -->
                <div class="section-card">
                    <div class="section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Contact & Guardian Information
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Student Phone -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <input type="text" name="phone" id="phone"
                                value="{{ old('phone', $student->phone) }}"
                                class="input-field @error('phone') border-red-300 @enderror"
                                placeholder="Student Phone (e.g. +265 999 123 456)">
                        </div>
                        @error('phone') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Parent / Guardian Name -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <input type="text" name="parent_name" id="parent_name"
                                value="{{ old('parent_name', $student->parent_name) }}"
                                class="input-field @error('parent_name') border-red-300 @enderror"
                                placeholder="Parent / Guardian Full Name">
                        </div>
                        @error('parent_name') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Parent / Guardian Phone -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="text" name="parent_phone" id="parent_phone"
                                value="{{ old('parent_phone', $student->parent_phone) }}"
                                class="input-field @error('parent_phone') border-red-300 @enderror"
                                placeholder="Parent / Guardian Phone">
                        </div>
                        @error('parent_phone') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Emergency Contact -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <input type="text" name="emergency_contact" id="emergency_contact"
                                value="{{ old('emergency_contact', $student->emergency_contact) }}"
                                class="input-field @error('emergency_contact') border-red-300 @enderror"
                                placeholder="Emergency Contact (Name & Number)">
                        </div>
                        @error('emergency_contact') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Physical Address -->
                        <div class="md:col-span-2 input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <input type="text" name="address" id="address"
                                value="{{ old('address', $student->address) }}"
                                class="input-field @error('address') border-red-300 @enderror"
                                placeholder="Physical Address / Residential Location">
                        </div>
                        @error('address') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Enrollment Card -->
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <select name="academic_year_id" id="academic_year_id"
                                class="input-field @error('academic_year_id') border-red-300 @enderror" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}"
                                        {{ old('academic_year_id', $student->activeEnrollment->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>
                                        {{ $year->year_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('academic_year_id') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Term (with data attributes for dates) -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <select name="term_id" id="term_id"
                                class="input-field @error('term_id') border-red-300 @enderror" required>
                                <option value="">Select Term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}"
                                        data-academic-year-id="{{ $term->academic_year_id }}"
                                        data-start-date="{{ $term->start_date ? $term->start_date->format('Y-m-d') : '' }}"
                                        data-end-date="{{ $term->end_date ? $term->end_date->format('Y-m-d') : '' }}"
                                        {{ old('term_id', $student->activeEnrollment->term_id ?? '') == $term->id ? 'selected' : '' }}>
                                        {{ $term->term_name }} ({{ $term->academicYear->year_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('term_id') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Status -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <select name="status" id="status"
                                class="input-field @error('status') border-red-300 @enderror" required>
                                <option value="active" {{ old('status', $student->activeEnrollment->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="graduated" {{ old('status', $student->activeEnrollment->status ?? '') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="suspended" {{ old('status', $student->activeEnrollment->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="withdrawn" {{ old('status', $student->activeEnrollment->status ?? '') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                            </select>
                        </div>
                        @error('status') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Enrollment Date (auto-filled) -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" name="enrollment_date" id="enrollment_date"
                                value="{{ old('enrollment_date', $student->activeEnrollment->enrollment_date ?? date('Y-m-d')) }}"
                                class="input-field @error('enrollment_date') border-red-300 @enderror" required>
                        </div>
                        @error('enrollment_date') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Expected Graduation Date (auto-filled) -->
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" name="expected_graduation_date" id="expected_graduation_date"
                                value="{{ old('expected_graduation_date', $student->activeEnrollment->expected_graduation_date ?? '') }}"
                                class="input-field @error('expected_graduation_date') border-red-300 @enderror">
                        </div>
                        @error('expected_graduation_date') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                        <!-- Notes -->
                        <div class="md:col-span-2 input-group">
                            <div class="input-icon" style="top:1.2rem; transform:none;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <textarea name="notes" id="notes" rows="2"
                                class="input-field pl-10"
                                placeholder="Additional notes about this student...">{{ old('notes', $student->activeEnrollment->notes ?? '') }}</textarea>
                        </div>
                        @error('notes') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Password Reset Card -->
                <div class="section-card password-reset-box">
                    <div class="section-title" style="border-bottom-color: var(--border-color);">
                        <svg class="text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Reset Password (Optional)
                        <span class="text-xs text-[var(--text-muted)] ml-auto">Leave blank to keep current</span>
                    </div>
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Password Reset Information</p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-0.5">
                                Only enter a new password if you need to reset it. Minimum 8 characters.
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-current mb-1">New Password</label>
                            <input type="password" name="manual_password" id="manual_password"
                                class="input-field pl-4"
                                placeholder="Enter new password">
                        </div>
                        <div class="flex items-end">
                            <button type="button" onclick="generateRandomPassword()"
                                class="w-full md:w-auto px-4 py-2.5 bg-[var(--bg-card)] text-[var(--text-primary)] rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-card)] transition flex items-center justify-center gap-2 group">
                                <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Generate
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fixed action buttons -->
    <div class="fixed-actions">
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
        </a>
        <button type="submit" form="studentForm" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Update Student
        </button>
    </div>

    <!-- Scroll to top -->
    <div class="scroll-top" id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </div>

    @push('scripts')
    <script>
        function generateRandomPassword() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
            let password = "";
            for (let i = 0; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }
            document.getElementById('manual_password').value = password;
            showNotification('Random password generated!', 'success');
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-xl z-50 transform transition-all duration-500 animate-slideIn ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>${message}</div>`;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-y-[-20px]');
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }

        function filterTermsByAcademicYear() {
            const academicYearId = document.getElementById('academic_year_id').value;
            const termSelect = document.getElementById('term_id');
            const options = termSelect.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }
                const optionAcademicYearId = option.getAttribute('data-academic-year-id');
                option.style.display = (!academicYearId || optionAcademicYearId === academicYearId) ? 'block' : 'none';
                if (option.style.display === 'none' && option.selected) option.selected = false;
            });
            // After filtering, update dates from the selected term
            updateDatesFromTerm();
        }

        function updateDatesFromTerm() {
            const termSelect = document.getElementById('term_id');
            const selectedOption = termSelect.options[termSelect.selectedIndex];
            const enrollmentDateInput = document.getElementById('enrollment_date');
            const graduationDateInput = document.getElementById('expected_graduation_date');

            if (!selectedOption || !selectedOption.value) {
                // No term selected, leave dates as they are
                return;
            }

            const startDate = selectedOption.getAttribute('data-start-date');
            const endDate = selectedOption.getAttribute('data-end-date');

            if (startDate && enrollmentDateInput) {
                enrollmentDateInput.value = startDate;
            }

            if (endDate && graduationDateInput) {
                graduationDateInput.value = endDate;
            }
        }

        window.addEventListener('scroll', function() {
            const btn = document.getElementById('scrollTopBtn');
            btn.classList.toggle('visible', window.scrollY > 400);
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('livePreview', () => ({
                formData: {
                    name: '{{ $student->user->name }}',
                    email: '{{ $student->user->email }}',
                    reg_number: '{{ $student->reg_number }}',
                    programme: '{{ $student->programme }}'
                },
                getInitials() {
                    return this.formData.name ? this.formData.name.charAt(0).toUpperCase() : '{{ substr($student->user->name, 0, 1) }}';
                },
                checkValidity(el) {
                    if (el.value.trim() === '') {
                        el.classList.remove('valid');
                        el.classList.add('invalid');
                    } else {
                        el.classList.remove('invalid');
                        el.classList.add('valid');
                    }
                },
                initPreview() {
                    document.querySelectorAll('.input-field').forEach(field => {
                        if (field.value.trim() !== '') field.classList.add('valid');
                    });
                },
                submitForm() {
                    document.getElementById('studentForm').submit();
                }
            }));
        });

        document.addEventListener('DOMContentLoaded', function() {
            const academicYearSelect = document.getElementById('academic_year_id');
            const termSelect = document.getElementById('term_id');

            if (academicYearSelect) {
                academicYearSelect.addEventListener('change', filterTermsByAcademicYear);
                filterTermsByAcademicYear(); // initial call
            }

            if (termSelect) {
                termSelect.addEventListener('change', updateDatesFromTerm);
                // Set dates from the initially selected term
                updateDatesFromTerm();
            }

            // Add slide-in animation style
            const style = document.createElement('style');
            style.innerHTML = `@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }.animate-slideIn { animation: slideIn 0.3s ease-out; }`;
            document.head.appendChild(style);

            document.getElementById('manual_password')?.addEventListener('blur', function() {
                if (this.value && this.value.length < 8) {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                } else if (this.value) {
                    this.classList.remove('invalid');
                    this.classList.add('valid');
                } else {
                    this.classList.remove('invalid', 'valid');
                }
            });
        });
    </script>
    @endpush
@endsection
