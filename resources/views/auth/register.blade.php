<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Create Account') }} · Sophisticate Intensive</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Theme detection -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', initialTheme);
        })();
    </script>

    <style>
        :root {
            --bg-primary: #f0f7ff;
            --bg-card: rgba(255, 255, 255, 0.7);
            --border-color: rgba(255, 255, 255, 0.5);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --shadow-color: rgba(59, 130, 246, 0.15);
            --blob-opacity: 0.3;
            --accent: #3b82f6;
            --accent-soft: rgba(59, 130, 246, 0.15);
            --accent-glow: rgba(59, 130, 246, 0.4);
            --brand-gradient: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --button-gradient: linear-gradient(135deg, #2563eb, #4f46e5, #7c3aed);
            --input-bg: rgba(255, 255, 255, 0.5);
            --input-border: rgba(255, 255, 255, 0.4);
            --shadow-lg: 0 25px 80px rgba(59, 130, 246, 0.15);
        }

        html[data-theme="dark"] {
            --bg-primary: #0a0e1a;
            --bg-card: rgba(10, 14, 26, 0.7);
            --border-color: rgba(255, 255, 255, 0.06);
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --shadow-color: rgba(59, 130, 246, 0.25);
            --blob-opacity: 0.2;
            --accent: #3b82f6;
            --accent-soft: rgba(59, 130, 246, 0.2);
            --accent-glow: rgba(59, 130, 246, 0.6);
            --brand-gradient: linear-gradient(135deg, #60a5fa, #a78bfa);
            --button-gradient: linear-gradient(135deg, #3b82f6, #6366f1, #8b5cf6);
            --input-bg: rgba(255, 255, 255, 0.05);
            --input-border: rgba(255, 255, 255, 0.1);
            --shadow-lg: 0 25px 80px rgba(59, 130, 246, 0.3);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
            font-family: 'Figtree', sans-serif;
        }

        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
            opacity: var(--blob-opacity);
            animation: blobMorph 20s ease-in-out infinite alternate;
        }

        .blob-1 {
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            top: -100px;
            left: -100px;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            bottom: -100px;
            right: -100px;
            animation-delay: 5s;
        }

        @keyframes blobMorph {
            0% { border-radius: 50%; transform: translate(0, 0) scale(1); }
            50% { border-radius: 40% 60% 60% 40%; transform: translate(-20px, 30px) scale(0.95); }
            100% { border-radius: 60% 40% 50% 50%; transform: translate(30px, -20px) scale(1.05); }
        }

        .split-container {
            max-width: 1100px;
            width: 100%;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 2.5rem;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
            position: relative;
            z-index: 10;
        }

        @media (max-width: 820px) {
            .split-container {
                grid-template-columns: 1fr;
                border-radius: 2rem;
            }
        }

        .theme-toggle-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 50;
            background: var(--bg-card);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 1rem;
            padding: 0.6rem;
            cursor: pointer;
            box-shadow: 0 4px 12px var(--shadow-color);
            transition: all 0.2s ease;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.08);
        }
    </style>
</head>

<body x-data="{
    theme: localStorage.getItem('theme') || 'light',
    toggleTheme() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.theme);
        document.documentElement.setAttribute('data-theme', this.theme);
    }
}">
    <!-- Background Blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <!-- Theme Toggle -->
    <button @click="toggleTheme()" class="theme-toggle-btn" title="Toggle theme">
        <svg x-show="theme === 'light'" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg x-show="theme === 'dark'" class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    <div class="split-container">
        <!-- Left Side: Brand Panel -->
        <div class="p-8 sm:p-12 flex flex-col justify-between items-center text-center border-b md:border-b-0 md:border-r border-[var(--border-color)] bg-gradient-to-br from-blue-600/10 via-indigo-600/5 to-transparent">
            <div class="flex flex-col items-center my-auto">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-black shadow-xl mb-4">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Sophisticate
                </h1>
                <p class="text-xs uppercase tracking-widest text-[var(--text-secondary)] font-semibold mt-1">
                    Intensive Classes
                </p>
                <p class="text-sm text-[var(--text-secondary)] mt-4 max-w-xs leading-relaxed">
                    Create an account to access coursework, view academic transcripts, take quizzes, and track fee records.
                </p>
            </div>

            <div class="text-xs text-[var(--text-muted)] mt-6">
                &copy; {{ date('Y') }} Sophisticate Intensive. All rights reserved.
            </div>
        </div>

        <!-- Right Side: Registration Form -->
        <div class="p-8 sm:p-12 flex flex-col justify-center">
            <div class="mb-6">
                <h2 class="text-2xl font-extrabold text-[var(--text-primary)]">Create Student Account</h2>
                <p class="text-sm text-[var(--text-secondary)] mt-1">Fill in your information to register</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider mb-1.5">Full Name</label>
                    <div class="relative">
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full pl-10 pr-4 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm shadow-sm"
                            placeholder="John Doe">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                    </div>
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider mb-1.5">Email Address</label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm shadow-sm"
                            placeholder="student@example.com">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                            <i class="fas fa-envelope text-xs"></i>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <label for="password" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                            class="w-full pl-10 pr-10 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm shadow-sm"
                            placeholder="••••••••">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                            <i class="fas fa-lock text-xs"></i>
                        </div>
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-muted)] hover:text-[var(--text-primary)]">
                            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-xs"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div x-data="{ show: false }">
                    <label for="password_confirmation" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                            class="w-full pl-10 pr-10 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm shadow-sm"
                            placeholder="••••••••">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                            <i class="fas fa-shield-alt text-xs"></i>
                        </div>
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-muted)] hover:text-[var(--text-primary)]">
                            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-xs"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition active:scale-95 text-sm">
                        Create Account
                    </button>
                </div>

                <div class="text-center pt-3 border-t border-[var(--border-color)]">
                    <p class="text-xs text-[var(--text-secondary)]">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline ml-1">
                            Sign In
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
