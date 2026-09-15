@extends('layouts.admin')

@section('content')
    <style>
        /* ----- Animated blobs (inherits variables from layout) ----- */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: var(--blob-opacity, 0.2);
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

        /* ----- Main wizard card ----- */
        .wizard-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            background: var(--bg-card);
            backdrop-filter: blur(var(--glass-blur, 16px));
            -webkit-backdrop-filter: blur(var(--glass-blur, 16px));
            border: 1px solid var(--border-color);
            border-radius: 2.5rem;
            box-shadow: 0 25px 80px var(--shadow-color);
            overflow: hidden;
            transition: background 0.4s, border-color 0.4s, box-shadow 0.4s;
            padding: 2rem 2.5rem;
        }
        @media (max-width: 640px) {
            .wizard-container { padding: 1.5rem; border-radius: 1.5rem; }
        }

        /* ----- Wizard header ----- */
        .wizard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .wizard-header .left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .wizard-header .icon-box {
            background: var(--accent-gradient, linear-gradient(135deg, #3b82f6, #8b5cf6));
            border-radius: 1rem;
            padding: 0.75rem;
            color: white;
            box-shadow: 0 8px 20px var(--accent-glow, rgba(59,130,246,0.3));
        }
        .wizard-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .wizard-header p {
            font-size: 0.95rem;
            color: var(--text-secondary);
        }

        /* ----- Progress steps ----- */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            position: relative;
        }
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--border-color);
            transform: translateY(-50%);
            z-index: 0;
        }
        .progress-steps .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 1;
            cursor: pointer;
        }
        .progress-steps .step .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text-secondary);
            transition: all 0.3s;
        }
        .progress-steps .step.active .circle {
            background: var(--accent-gradient, linear-gradient(135deg, #3b82f6, #8b5cf6));
            border-color: transparent;
            color: white;
            box-shadow: 0 0 0 4px var(--accent-soft, rgba(59,130,246,0.2));
        }
        .progress-steps .step.completed .circle {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }
        .progress-steps .step .label {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .progress-steps .step.active .label {
            color: var(--accent, #3b82f6);
        }

        /* ----- Form inputs (theme-aware) ----- */
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
            z-index: 1;
        }
        .input-group:focus-within .input-icon {
            color: var(--accent, #3b82f6);
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
            border-color: var(--accent, #3b82f6);
            box-shadow: 0 0 0 4px var(--accent-soft, rgba(59,130,246,0.15));
            outline: none;
            background: var(--bg-card);
        }
        select.input-field {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
            padding-right: 2.5rem;
        }
        textarea.input-field {
            padding-left: 2.75rem;
        }

        /* ----- Navigation buttons ----- */
        .wizard-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        .wizard-nav .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: var(--accent-gradient, linear-gradient(135deg, #3b82f6, #8b5cf6));
            color: white;
            box-shadow: 0 4px 12px var(--accent-glow, rgba(59,130,246,0.3));
        }
        .btn-primary:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px var(--accent-glow, rgba(59,130,246,0.4));
        }
        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            background: var(--bg-card);
            border-color: var(--accent, #3b82f6);
        }

        /* ----- Review step ----- */
        .review-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .review-item .label { font-weight: 600; color: var(--text-secondary); }
        .review-item .value { color: var(--text-primary); }
        .review-item .edit-btn {
            color: var(--accent, #3b82f6);
            cursor: pointer;
            font-size: 0.8rem;
            background: none;
            border: none;
            text-decoration: underline;
        }

        /* ----- Responsive ----- */
        @media (max-width: 640px) {
            .wizard-header h1 { font-size: 1.4rem; }
            .progress-steps .step .label { display: none; }
            .progress-steps .step .circle { width: 32px; height: 32px; font-size: 0.75rem; }
        }
    </style>

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="wizard-container" x-data="wizard()" x-init="init()">
        <!-- Header -->
        <div class="wizard-header">
            <div class="left">
                <div class="icon-box">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <h1>Add New Student</h1>
                    <p>Fill in the details below – we'll guide you step by step.</p>
                </div>
            </div>
            <a href="{{ route('admin.students.index') }}" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps">
            <template x-for="(step, index) in steps" :key="index">
                <div class="step" :class="{ active: currentStep === index, completed: currentStep > index }" @click="goToStep(index)">
                    <div class="circle" x-text="index + 1"></div>
                    <span class="label" x-text="step.label"></span>
                </div>
            </template>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.students.store') }}" method="POST" id="studentForm" @submit.prevent="submitForm">
            @csrf

            <!-- Step 1: Account Information -->
            <div x-show="currentStep === 0" x-transition:enter.duration.300ms>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Reg Number (read-only display) -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between p-3 bg-[var(--accent-soft)] rounded-xl border border-[var(--border-color)]">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                </svg>
                                <span class="font-medium text-[var(--text-primary)]">Registration Number:</span>
                                <span class="font-mono text-lg font-bold text-[var(--text-primary)]" id="regNumberDisplay">Loading...</span>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Auto</span>
                            </div>
                            <button type="button" onclick="generateNewRegNumber()" class="text-sm text-blue-600 hover:underline">Refresh</button>
                            <input type="hidden" name="reg_number" id="reg_number">
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="input-field @error('name') border-red-300 @enderror"
                            placeholder="Full Name" required>
                    </div>
                    @error('name') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                    <!-- Email -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="input-field @error('email') border-red-300 @enderror"
                            placeholder="Email Address" required>
                    </div>
                    @error('email') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                    <!-- Password -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password"
                            class="input-field @error('password') border-red-300 @enderror"
                            placeholder="Password" required>
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                            <svg id="passwordEyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                    <!-- Confirm Password -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="input-field" placeholder="Confirm Password" required>
                        <button type="button" onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                            <svg id="confirmPasswordEyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div class="md:col-span-2 text-xs text-gray-500 -mt-2">Password must be at least 8 characters.</div>

                    <!-- Contact Information Section Divider -->
                    <div class="md:col-span-2 pt-3 border-t border-[var(--border-color)]">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-3 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Contact & Guardian Details (Optional)
                        </h4>
                    </div>

                    <!-- Student Phone -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="input-field @error('phone') border-red-300 @enderror"
                            placeholder="Student Phone Number (e.g. +265 999 123 456)">
                    </div>
                    @error('phone') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                    <!-- Parent / Guardian Name -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name') }}"
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
                        <input type="text" name="parent_phone" id="parent_phone" value="{{ old('parent_phone') }}"
                            class="input-field @error('parent_phone') border-red-300 @enderror"
                            placeholder="Parent / Guardian Phone Number">
                    </div>
                    @error('parent_phone') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror

                    <!-- Emergency Contact Info -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}"
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
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                            class="input-field @error('address') border-red-300 @enderror"
                            placeholder="Physical Address / Residential Location">
                    </div>
                    @error('address') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Step 2: Programme & Enrollment -->
            <div x-show="currentStep === 1" x-transition:enter.duration.300ms>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Programme -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-current mb-1">Programme <span class="text-red-500">*</span></label>
                        <input type="hidden" name="programme" id="programmeHidden" value="{{ old('programme') }}">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <select id="programmeSelect"
                                class="input-field @error('programme') border-red-300 @enderror">
                                <option value="">Select a programme</option>
                                @php
                                    $programmes = ['BRESE','BSPHE','BSCHE','BSDS','BICT','BSPDVC','BSMAT','BBCM','BEDICT','BSBS','BSTRP','BSLS','BSEM','BEDS','BSVCA','BSFAS','BSTCD','BSWREM'];
                                @endphp
                                @foreach($programmes as $prog)
                                    <option value="{{ $prog }}" {{ old('programme') == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                                @endforeach
                                <option value="__new__" class="text-blue-600 font-semibold">+ Add New Programme</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <div id="newProgrammeContainer" class="hidden mt-3">
                            <div class="flex items-center space-x-2">
                                <input type="text" id="newProgrammeInput"
                                    class="flex-1 px-4 py-2 border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-[var(--bg-card)] text-[var(--text-primary)]"
                                    placeholder="Enter new programme name">
                                <button type="button" id="addProgrammeBtn" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">Add</button>
                                <button type="button" id="cancelNewProgramme" class="px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-card)] transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-[var(--text-secondary)] mt-2">Press Add or Enter to confirm.</p>
                        </div>
                        @error('programme') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Source of Funding -->
                    <div class="md:col-span-2" x-data="{ fundingSource: '{{ old('source_of_funding', 'Not Specified') }}' }">
                        <label class="block text-sm font-medium text-current mb-1">Source of Funding <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <select name="source_of_funding" id="source_of_funding" x-model="fundingSource"
                                class="input-field @error('source_of_funding') border-red-300 @enderror" required>
                                <option value="">Select Funding Source</option>
                                @foreach(\App\Models\Student::FUNDING_SOURCES as $source)
                                    <option value="{{ $source }}" {{ old('source_of_funding', 'Not Specified') == $source ? 'selected' : '' }}>{{ $source }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('source_of_funding') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                        <!-- Custom input if Other is chosen -->
                        <div x-show="fundingSource === 'Other'" class="mt-3" style="display: none;">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-1">Specify Other Funding Source *</label>
                            <input type="text" name="funding_source_other" id="funding_source_other" value="{{ old('funding_source_other') }}"
                                placeholder="e.g. Rotary Foundation Grant, NGO Sponsorship, Community Trust..."
                                class="w-full px-4 py-2.5 border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm">
                            @error('funding_source_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Academic Year -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <select name="academic_year_id" id="academic_year_id"
                            class="input-field @error('academic_year_id') border-red-300 @enderror">
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->year_name }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Term (with data attributes for dates) -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <select name="term_id" id="term_id"
                            class="input-field @error('term_id') border-red-300 @enderror">
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}"
                                    data-academic-year-id="{{ $term->academic_year_id }}"
                                    data-start-date="{{ $term->start_date ? $term->start_date->format('Y-m-d') : '' }}"
                                    data-end-date="{{ $term->end_date ? $term->end_date->format('Y-m-d') : '' }}"
                                    {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                    {{ $term->term_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('term_id') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Enrollment Date (auto-filled) -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="date" name="enrollment_date" id="enrollment_date"
                            value="{{ old('enrollment_date', date('Y-m-d')) }}"
                            class="input-field @error('enrollment_date') border-red-300 @enderror" required>
                        @error('enrollment_date') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Expected Graduation Date (auto-filled) -->
                    <div class="input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <input type="date" name="expected_graduation_date" id="expected_graduation_date"
                            value="{{ old('expected_graduation_date') }}"
                            class="input-field @error('expected_graduation_date') border-red-300 @enderror">
                        @error('expected_graduation_date') <p class="text-red-500 text-xs -mt-2 mb-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2 input-group">
                        <div class="input-icon" style="top:1.2rem; transform:none;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <textarea name="notes" id="notes" rows="2"
                            class="input-field"
                            placeholder="Additional notes (optional)">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Step 3: Review & Confirm -->
            <div x-show="currentStep === 2" x-transition:enter.duration.300ms>
                <h3 class="text-lg font-semibold text-current mb-3">Review Student Details</h3>
                <p class="text-sm text-gray-500 mb-4">Please verify all information before creating the student account.</p>
                <div class="space-y-2">
                    <div class="review-item">
                        <span class="label">Registration Number</span>
                        <span class="value" x-text="regNumber || '—'"></span>
                    </div>
                    <div class="review-item">
                        <span class="label">Full Name</span>
                        <span class="value" x-text="name || '—'"></span>
                        <button class="edit-btn" @click="goToStep(0)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Email</span>
                        <span class="value" x-text="email || '—'"></span>
                        <button class="edit-btn" @click="goToStep(0)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Phone</span>
                        <span class="value" x-text="phone || 'Not provided'"></span>
                        <button class="edit-btn" @click="goToStep(0)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Parent / Guardian</span>
                        <span class="value" x-text="parentName ? `${parentName} (${parentPhone || 'No Phone'})` : 'Not provided'"></span>
                        <button class="edit-btn" @click="goToStep(0)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Programme</span>
                        <span class="value" x-text="programme || '—'"></span>
                        <button class="edit-btn" @click="goToStep(1)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Academic Year</span>
                        <span class="value" x-text="academicYear || '—'"></span>
                        <button class="edit-btn" @click="goToStep(1)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Term</span>
                        <span class="value" x-text="term || '—'"></span>
                        <button class="edit-btn" @click="goToStep(1)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Enrollment Date</span>
                        <span class="value" x-text="enrollmentDate || '—'"></span>
                        <button class="edit-btn" @click="goToStep(1)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Expected Graduation</span>
                        <span class="value" x-text="graduationDate || 'Not set'"></span>
                        <button class="edit-btn" @click="goToStep(1)">Edit</button>
                    </div>
                    <div class="review-item">
                        <span class="label">Notes</span>
                        <span class="value" x-text="notes || 'None'"></span>
                        <button class="edit-btn" @click="goToStep(1)">Edit</button>
                    </div>
                </div>
                <div class="mt-6 p-4 bg-[var(--accent-soft)] rounded-xl border border-[var(--border-color)] flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h5 class="text-sm font-semibold text-[var(--text-primary)]">Ready to create?</h5>
                        <p class="text-xs text-[var(--text-secondary)]">Click the "Create Student" button below to complete the registration.</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="wizard-nav">
                <div>
                    <button type="button" class="btn btn-secondary" x-show="currentStep > 0" @click="prevStep()">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" x-show="currentStep < 2" @click="nextStep()">
                        Next
                        <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button type="submit" class="btn btn-primary" x-show="currentStep === 2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Create Student
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function wizard() {
            return {
                currentStep: 0,
                steps: [
                    { label: 'Account' },
                    { label: 'Programme' },
                    { label: 'Review' }
                ],
                regNumber: '',
                name: '',
                email: '',
                phone: '',
                parentName: '',
                parentPhone: '',
                programme: '',
                academicYear: '',
                term: '',
                enrollmentDate: '',
                graduationDate: '',
                notes: '',
                init() {
                    this.$watch('currentStep', () => this.updateReviewData());
                    this.updateReviewData();
                },
                updateReviewData() {
                    this.regNumber = document.getElementById('reg_number')?.value || '';
                    this.name = document.getElementById('name')?.value || '';
                    this.email = document.getElementById('email')?.value || '';
                    this.phone = document.getElementById('phone')?.value || '';
                    this.parentName = document.getElementById('parent_name')?.value || '';
                    this.parentPhone = document.getElementById('parent_phone')?.value || '';
                    this.programme = document.getElementById('programmeHidden')?.value || '';
                    const aySelect = document.getElementById('academic_year_id');
                    this.academicYear = aySelect?.options[aySelect.selectedIndex]?.text || '';
                    const termSelect = document.getElementById('term_id');
                    this.term = termSelect?.options[termSelect.selectedIndex]?.text || '';
                    this.enrollmentDate = document.getElementById('enrollment_date')?.value || '';
                    this.graduationDate = document.getElementById('expected_graduation_date')?.value || '';
                    this.notes = document.getElementById('notes')?.value || '';
                },
                goToStep(step) {
                    this.currentStep = step;
                    this.updateReviewData();
                },
                nextStep() {
                    if (this.currentStep === 0) {
                        const name = document.getElementById('name').value.trim();
                        const email = document.getElementById('email').value.trim();
                        const password = document.getElementById('password').value;
                        const confirm = document.getElementById('password_confirmation').value;
                        if (!name) { alert('Please enter the student\'s full name.'); return; }
                        if (!email) { alert('Please enter an email address.'); return; }
                        if (password.length < 8) { alert('Password must be at least 8 characters.'); return; }
                        if (password !== confirm) { alert('Passwords do not match.'); return; }
                    } else if (this.currentStep === 1) {
                        const programme = document.getElementById('programmeHidden').value;
                        const ay = document.getElementById('academic_year_id').value;
                        const term = document.getElementById('term_id').value;
                        const enrollDate = document.getElementById('enrollment_date').value;
                        if (!programme) { alert('Please select or enter a programme.'); return; }
                        if (!ay) { alert('Please select an academic year.'); return; }
                        if (!term) { alert('Please select a term.'); return; }
                        if (!enrollDate) { alert('Please select an enrollment date.'); return; }
                    }
                    if (this.currentStep < 2) {
                        this.currentStep++;
                        this.updateReviewData();
                    }
                },
                prevStep() {
                    if (this.currentStep > 0) {
                        this.currentStep--;
                        this.updateReviewData();
                    }
                },
                submitForm() {
                    this.updateReviewData();
                    if (!this.regNumber || !this.name || !this.email || !this.programme || !this.academicYear || !this.term || !this.enrollmentDate) {
                        alert('Please fill in all required fields.');
                        return;
                    }
                    document.getElementById('studentForm').submit();
                }
            }
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const iconId = fieldId === 'password' ? 'passwordEyeIcon' : 'confirmPasswordEyeIcon';
            const icon = document.getElementById(iconId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 013.563-4.92m4.75-2.334A9.985 9.985 0 0112 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-1.062 0-2.09-.157-3.062-.447M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />`;
            } else {
                field.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }

        async function fetchNextRegNumber() {
            try {
                const res = await fetch('{{ route("admin.students.next-reg-number") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                document.getElementById('regNumberDisplay').textContent = data.reg_number;
                document.getElementById('reg_number').value = data.reg_number;
                const wizardData = document.querySelector('[x-data]')?._x_dataStack?.[0];
                if (wizardData) wizardData.regNumber = data.reg_number;
            } catch (e) {
                document.getElementById('regNumberDisplay').textContent = 'Error — click Refresh';
            }
        }

        function generateNewRegNumber() {
            document.getElementById('regNumberDisplay').innerHTML = '<span class="text-sm text-gray-400 animate-pulse">Loading...</span>';
            fetchNextRegNumber();
        }

        function updateDatesFromTerm() {
            const termSelect = document.getElementById('term_id');
            const selectedOption = termSelect.options[termSelect.selectedIndex];
            const enrollmentDateInput = document.getElementById('enrollment_date');
            const graduationDateInput = document.getElementById('expected_graduation_date');

            if (!selectedOption || !selectedOption.value) {
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

            // Update review data if needed
            const wizardData = document.querySelector('[x-data]')?._x_dataStack?.[0];
            if (wizardData) wizardData.updateReviewData();
        }

        // Programme management
        const programmeSelect = document.getElementById('programmeSelect');
        const programmeHidden = document.getElementById('programmeHidden');
        const newProgrammeContainer = document.getElementById('newProgrammeContainer');
        const newProgrammeInput = document.getElementById('newProgrammeInput');
        const addProgrammeBtn = document.getElementById('addProgrammeBtn');
        const cancelProgrammeBtn = document.getElementById('cancelNewProgramme');

        if (programmeSelect) {
            programmeSelect.addEventListener('change', function () {
                if (this.value === '__new__') {
                    newProgrammeContainer.classList.remove('hidden');
                    programmeHidden.value = '';
                    newProgrammeInput.focus();
                } else if (this.value) {
                    newProgrammeContainer.classList.add('hidden');
                    newProgrammeInput.value = '';
                    programmeHidden.value = this.value;
                } else {
                    newProgrammeContainer.classList.add('hidden');
                    programmeHidden.value = '';
                }
                const wizardData = document.querySelector('[x-data]')?._x_dataStack?.[0];
                if (wizardData) wizardData.updateReviewData();
            });

            function confirmNewProgramme() {
                const name = newProgrammeInput.value.trim();
                if (!name) { newProgrammeInput.focus(); return; }

                const exists = Array.from(programmeSelect.options)
                    .some(o => o.value && o.value !== '__new__' && o.value.toLowerCase() === name.toLowerCase());
                if (exists) { alert('This programme already exists.'); return; }

                const opt = new Option(name, name);
                programmeSelect.insertBefore(opt, programmeSelect.querySelector('option[value="__new__"]'));
                programmeSelect.value = name;
                programmeHidden.value = name;
                newProgrammeContainer.classList.add('hidden');
                newProgrammeInput.value = '';
                const wizardData = document.querySelector('[x-data]')?._x_dataStack?.[0];
                if (wizardData) wizardData.updateReviewData();
            }

            addProgrammeBtn.addEventListener('click', confirmNewProgramme);
            newProgrammeInput.addEventListener('keypress', e => {
                if (e.key === 'Enter') { e.preventDefault(); confirmNewProgramme(); }
            });
            cancelProgrammeBtn.addEventListener('click', () => {
                newProgrammeContainer.classList.add('hidden');
                newProgrammeInput.value = '';
                programmeSelect.value = programmeHidden.value || '';
            });
        }

        // Academic Year → Term filter
        const academicYearSelect = document.getElementById('academic_year_id');
        const termSelect = document.getElementById('term_id');

        if (academicYearSelect) {
            academicYearSelect.addEventListener('change', function () {
                const selectedYear = this.value;
                Array.from(termSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    opt.hidden = selectedYear && opt.dataset.academicYearId !== selectedYear;
                });
                if (termSelect.selectedOptions[0]?.dataset.academicYearId !== selectedYear) {
                    termSelect.value = '';
                }
                updateDatesFromTerm();
                const wizardData = document.querySelector('[x-data]')?._x_dataStack?.[0];
                if (wizardData) wizardData.updateReviewData();
            });
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', function () {
            fetchNextRegNumber();
            @if(old('programme'))
                const oldProgramme = '{{ old('programme') }}';
                const existingOpt = Array.from(programmeSelect.options)
                    .find(o => o.value === oldProgramme);
                if (existingOpt) {
                    programmeSelect.value = oldProgramme;
                    programmeHidden.value = oldProgramme;
                } else if (oldProgramme) {
                    const opt = new Option(oldProgramme, oldProgramme, true, true);
                    programmeSelect.insertBefore(opt, programmeSelect.querySelector('option[value="__new__"]'));
                    programmeHidden.value = oldProgramme;
                }
            @endif
            if (academicYearSelect.value) {
                academicYearSelect.dispatchEvent(new Event('change'));
            }
            if (termSelect.value) {
                updateDatesFromTerm();
            }
            const wizardData = document.querySelector('[x-data]')?._x_dataStack?.[0];
            if (wizardData) wizardData.updateReviewData();
        });
    </script>
    @endpush
@endsection
