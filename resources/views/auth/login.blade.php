<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA & Mobile Web App Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/favicon-16x16.png') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Sophisticate">
    <meta name="application-name" content="Sophisticate">
    <meta name="theme-color" content="#09090b">

    <title>{{ __('Login') }} · Sophisticate Intensive</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js for password toggle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Theme detection (before CSS to avoid flash) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', initialTheme);
        })();
    </script>

    <style>
        /* ----- CSS Variables for theming (light default) ----- */
        :root {
            --bg-primary: #f0f7ff;
            --bg-card: rgba(255, 255, 255, 0.7);
            --border-color: rgba(255, 255, 255, 0.5);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --shadow-color: rgba(59, 130, 246, 0.15);
            --blob-opacity: 0.3;

            /* Accent colors */
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --accent-soft: rgba(59, 130, 246, 0.15);
            --accent-glow: rgba(59, 130, 246, 0.4);
            --accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
            --brand-gradient: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --button-gradient: linear-gradient(135deg, #2563eb, #4f46e5, #7c3aed);

            /* Form inputs */
            --input-bg: rgba(255, 255, 255, 0.5);
            --input-bg-focus: rgba(255, 255, 255, 0.7);
            --input-border: rgba(255, 255, 255, 0.4);

            /* Overlays */
            --overlay-light: rgba(255, 255, 255, 0.5);
            --overlay-strong: rgba(255, 255, 255, 0.8);

            /* Shadows */
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 25px 80px rgba(59, 130, 246, 0.15);
        }

        /* Dark theme overrides */
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
            --accent-hover: #60a5fa;
            --accent-soft: rgba(59, 130, 246, 0.2);
            --accent-glow: rgba(59, 130, 246, 0.6);
            --accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
            --brand-gradient: linear-gradient(135deg, #60a5fa, #a78bfa);
            --button-gradient: linear-gradient(135deg, #3b82f6, #6366f1, #8b5cf6);

            --input-bg: rgba(255, 255, 255, 0.05);
            --input-bg-focus: rgba(255, 255, 255, 0.08);
            --input-border: rgba(255, 255, 255, 0.1);

            --overlay-light: rgba(255, 255, 255, 0.05);
            --overlay-strong: rgba(255, 255, 255, 0.1);

            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 25px 80px rgba(59, 130, 246, 0.3);
        }

        /* ----- Global transition for smooth theme switching ----- */
        *,
        *::before,
        *::after {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, fill 0.3s ease, stroke 0.3s ease, box-shadow 0.3s ease, opacity 0.3s ease;
        }

        /* Exclude animations from global transition where needed */
        .blob {
            transition: background 0.6s ease, opacity 0.3s ease;
        }

        body {
            font-family: 'figtree', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }

        /* ----- Animated blobs (matching main site) ----- */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: var(--blob-opacity);
            animation: blobMorph 15s ease-in-out infinite alternate;
            z-index: -1;
        }

        .blob-1 {
            width: 40vw;
            height: 40vw;
            background: #3b82f6;
            top: -10%;
            right: -10%;
        }

        .blob-2 {
            width: 35vw;
            height: 35vw;
            background: #2563eb;
            bottom: -10%;
            left: -10%;
            animation-delay: 5s;
        }

        .blob-3 {
            width: 25vw;
            height: 25vw;
            background: #60a5fa;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 10s;
        }

        @keyframes blobMorph {
            0% {
                border-radius: 50% 50% 50% 50%;
                transform: translate(0, 0) scale(1);
            }
            25% {
                border-radius: 60% 40% 50% 50%;
                transform: translate(30px, -20px) scale(1.1);
            }
            50% {
                border-radius: 40% 60% 60% 40%;
                transform: translate(-20px, 30px) scale(0.9);
            }
            75% {
                border-radius: 50% 30% 70% 50%;
                transform: translate(40px, 10px) scale(1.05);
            }
            100% {
                border-radius: 50% 50% 50% 50%;
                transform: translate(0, 0) scale(1);
            }
        }

        /* ----- Main container – split screen (glass) ----- */
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
        }

        @media (max-width: 768px) {
            .split-container {
                grid-template-columns: 1fr;
                min-height: auto;
                border-radius: 2rem;
            }
        }

        /* ----- Left panel – branding ----- */
        .brand-panel {
            background: linear-gradient(145deg, var(--accent-soft), transparent);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border-right: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .brand-panel {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 2.5rem 2rem;
            }
        }

        .brand-logo {
            filter: drop-shadow(0 10px 30px var(--accent-glow));
            animation: floatLogo 6s ease-in-out infinite;
            width: 120px;
            height: 120px;
            object-fit: contain;
        }

        @keyframes floatLogo {
            0% {
                transform: translateY(0px) scale(1);
            }
            50% {
                transform: translateY(-10px) scale(1.02);
            }
            100% {
                transform: translateY(0px) scale(1);
            }
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-top: 1.5rem;
        }

        .brand-sub {
            font-size: 0.8rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        .brand-tagline {
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: var(--text-secondary);
            max-width: 280px;
            line-height: 1.6;
        }

        .brand-features {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
            max-width: 280px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .brand-feature svg {
            flex-shrink: 0;
            color: var(--accent);
            width: 1.25rem;
            height: 1.25rem;
        }

        /* ----- Right panel – form ----- */
        .form-panel {
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .form-panel {
                padding: 2rem 1.5rem;
            }
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .form-header p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-top: 0.25rem;
        }

        /* ----- Input styles (fully theme-aware) ----- */
        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            pointer-events: none;
            transition: color 0.3s;
        }

        .input-group:focus-within .input-icon {
            color: var(--accent);
        }

        .input-field {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 2px solid var(--input-border);
            border-radius: 1rem;
            background: var(--input-bg);
            color: var(--text-primary);
            transition: all 0.3s;
        }

        .input-field:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-soft);
            outline: none;
            background: var(--input-bg-focus);
        }

        /* ----- Theme toggle (fixed) ----- */
        .theme-toggle {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 100;
            background: var(--bg-card);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-primary);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            border-color: var(--accent);
        }

        /* ----- Additional text utilities (replaces Tailwind dark: classes) ----- */
        .text-muted {
            color: var(--text-muted);
        }

        .text-accent {
            color: var(--accent);
        }

        .hover-text-accent:hover {
            color: var(--accent-hover);
        }

        /* ----- Checkbox styling (theme-aware) ----- */
        .checkbox {
            width: 1rem;
            height: 1rem;
            accent-color: var(--accent);
            background: var(--input-bg);
            border: 2px solid var(--input-border);
            border-radius: 0.25rem;
            transition: all 0.3s;
        }

        .checkbox:focus {
            outline: none;
            box-shadow: 0 0 0 4px var(--accent-soft);
        }

        /* ----- Responsive adjustments ----- */
        @media (max-width: 640px) {
            .brand-title {
                font-size: 2rem;
            }
            .brand-logo {
                width: 90px;
                height: 90px;
            }
            .split-container {
                border-radius: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Theme toggle -->
    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
        <!-- Sun icon (shown in dark mode) -->
        <svg class="w-5 h-5 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <!-- Moon icon (shown in light mode) -->
        <svg class="w-5 h-5 moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    <!-- Split container -->
    <div class="split-container">
        <!-- Left: Branding -->
        <div class="brand-panel">
            <img src="{{ asset('images/logo.png') }}" alt="Sophisticate Logo" class="brand-logo">
            <h1 class="brand-title">Sophisticate Intensive</h1>
            <p class="brand-sub">CLASSES</p>
            <p class="brand-tagline">Empowering academic excellence through specialised instruction in Mathematics,
                Physics, and Chemistry.</p>
            <div class="brand-features">
                <div class="brand-feature">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Expert tutors</span>
                </div>
                <div class="brand-feature">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Small class sizes</span>
                </div>
                <div class="brand-feature">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Proven success rate</span>
                </div>
            </div>
            <!-- Contact info (theme-aware) -->
            <div class="mt-6 text-xs text-muted">
                <p>Need help? <a href="mailto:recchirwa@gmail.com" class="text-accent hover-text-accent hover:underline">recchirwa@gmail.com</a></p>
                <p class="mt-0.5"><a href="tel:+265888257636" class="text-accent hover-text-accent hover:underline">+265 888 257 636</a></p>
            </div>
        </div>

        <!-- Right: Login Form -->
        <div class="form-panel">
            <div class="form-header">
                <h2>Welcome back</h2>
                <p>Log in to access your dashboard.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="text-sm font-medium text-current">Email Address</label>
                    <div class="relative input-group">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            autocomplete="username"
                            class="input-field @error('email') border-red-300 @enderror"
                            placeholder="your@email.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password with toggle -->
                <div class="space-y-1.5">
                    <label for="password" class="text-sm font-medium text-current">Password</label>
                    <div class="relative input-group" x-data="{ show: false }">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" id="password" required
                            autocomplete="current-password"
                            class="input-field @error('password') border-red-300 @enderror"
                            placeholder="••••••••">
                        <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover-text-accent transition-colors">
                            <!-- Eye icon (shown when password hidden) -->
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye-off icon (shown when password visible) -->
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me & forgot (inline) -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember"
                            class="checkbox">
                        <span class="ml-2 text-sm text-muted group-hover:text-current transition-colors">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-accent hover-text-accent hover:underline">Forgot password?</a>
                </div>

                <!-- Login button -->
                <button type="submit"
                    class="w-full relative group overflow-hidden text-white py-3.5 rounded-xl font-semibold shadow-lg hover:shadow-2xl transform hover:-translate-y-0.5 transition-all duration-200"
                    style="background: var(--button-gradient);">
                    <span
                        class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></span>
                    <span class="relative flex items-center justify-center">
                        <span class="mr-2">Log in</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </span>
                </button>

                <!-- Admin contact (inline help) -->
                <div class="text-center text-xs text-muted mt-4">
                    <span>Need help? </span>
                    <a href="mailto:recchirwa@gmail.com" class="text-accent hover-text-accent hover:underline">recchirwa@gmail.com</a>
                    <span class="mx-1">·</span>
                    <a href="tel:+265888257636" class="text-accent hover-text-accent hover:underline">+265 888 257 636</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Theme toggle script -->
    <script>
        (function() {
            const toggleBtn = document.getElementById('themeToggle');
            const sunIcon = toggleBtn.querySelector('.sun-icon');
            const moonIcon = toggleBtn.querySelector('.moon-icon');

            function updateIcon(theme) {
                if (theme === 'dark') {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }

            // Initial icon
            updateIcon(document.documentElement.getAttribute('data-theme'));

            toggleBtn.addEventListener('click', function() {
                const current = document.documentElement.getAttribute('data-theme');
                const next = current === 'light' ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
                updateIcon(next);
            });
        })();
    </script>
    <script src="{{ asset('js/pwa.js') }}"></script>
</body>

</html>
