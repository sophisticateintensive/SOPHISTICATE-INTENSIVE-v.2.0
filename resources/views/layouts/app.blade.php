<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sophisticate Intensive Classes'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js (for interactivity) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Theme detection (before CSS to prevent flash) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', initialTheme);
        })();
    </script>

    <style>
        /* ============================================================
           CSS VARIABLES – THEME SYSTEM
           ============================================================ */
        :root {
            /* Base colors */
            --bg-primary: #f0f7ff;
            --bg-secondary: #f8fafc;
            --bg-card: rgba(255, 255, 255, 0.65);
            --glass-bg: rgba(255, 255, 255, 0.5);
            --border-color: rgba(255, 255, 255, 0.4);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --shadow-color: rgba(59, 130, 246, 0.15);
            --glass-blur: 16px;

            /* Accent */
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --accent-soft: rgba(59, 130, 246, 0.15);
            --accent-glow: rgba(59, 130, 246, 0.4);
            --accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
            --brand-gradient: linear-gradient(135deg, #3b82f6, #8b5cf6);

            /* Background blobs */
            --blob-opacity: 0.3;
            --blob-color1: #3b82f6;
            --blob-color2: #2563eb;
            --blob-color3: #60a5fa;

            /* Overlays & misc */
            --overlay-light: rgba(255, 255, 255, 0.5);
            --overlay-strong: rgba(255, 255, 255, 0.8);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* Dark theme overrides */
        html[data-theme="dark"] {
            --bg-primary: #0a0e1a;
            --bg-secondary: #111827;
            --bg-card: rgba(10, 14, 26, 0.65);
            --glass-bg: rgba(10, 14, 26, 0.5);
            --border-color: rgba(255, 255, 255, 0.06);
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --shadow-color: rgba(59, 130, 246, 0.25);
            --glass-blur: 20px;

            --accent: #3b82f6;
            --accent-hover: #60a5fa;
            --accent-soft: rgba(59, 130, 246, 0.2);
            --accent-glow: rgba(59, 130, 246, 0.6);
            --accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
            --brand-gradient: linear-gradient(135deg, #60a5fa, #a78bfa);

            --blob-opacity: 0.2;
            --blob-color1: #1d4ed8;
            --blob-color2: #1e40af;
            --blob-color3: #3b82f6;

            --overlay-light: rgba(255, 255, 255, 0.05);
            --overlay-strong: rgba(255, 255, 255, 0.1);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        /* ============================================================
           GLOBAL RESET & BASE
           ============================================================ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'figtree', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: background 0.4s ease, color 0.4s ease;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            line-height: 1.5;
        }

        /* ============================================================
           ANIMATED BACKGROUND BLOBS
           ============================================================ */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: var(--blob-opacity);
            animation: blobFloat 18s ease-in-out infinite alternate;
            transition: background 0.6s ease, opacity 0.3s ease;
            z-index: -1;
            pointer-events: none;
        }
        .blob-1 { width: 50vw; height: 50vw; background: var(--blob-color1); top: -20%; right: -10%; }
        .blob-2 { width: 40vw; height: 40vw; background: var(--blob-color2); bottom: -20%; left: -10%; animation-delay: 5s; }
        .blob-3 { width: 30vw; height: 30vw; background: var(--blob-color3); top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: 10s; }

        @keyframes blobFloat {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, -30px) scale(1.1); }
        }

        /* ============================================================
           CUSTOM SCROLLBAR
           ============================================================ */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--accent-gradient);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--accent-hover), #4f46e5);
        }

        /* ============================================================
           SCROLL PROGRESS BAR
           ============================================================ */
        #scrollProgress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: var(--accent-gradient);
            z-index: 2000;
            transition: width 0.1s linear;
        }

        /* ============================================================
           GLASS HEADER (NAVIGATION)
           ============================================================ */
        .glass-header {
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: background 0.4s, border-color 0.4s, box-shadow 0.4s;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* ============================================================
           GLASS CARD (used for content sections)
           ============================================================ */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            transition: background 0.4s, border-color 0.4s, box-shadow 0.4s;
            padding: 1.5rem;
        }

        /* ============================================================
           THEME TOGGLE (floating)
           ============================================================ */
        .theme-toggle {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 1500;
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-primary);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
            font-size: 1.25rem;
        }
        .theme-toggle:hover {
            transform: scale(1.1);
            border-color: var(--accent);
            box-shadow: 0 4px 16px var(--accent-glow);
        }

        /* ============================================================
           SCROLL TO TOP BUTTON
           ============================================================ */
        #scrollTopBtn {
            position: fixed;
            bottom: 1.5rem;
            left: 1.5rem;
            z-index: 1500;
            width: 48px;
            height: 48px;
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-primary);
            box-shadow: var(--shadow-sm);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s;
            font-size: 1.25rem;
            pointer-events: none;
        }
        #scrollTopBtn.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
        #scrollTopBtn:hover {
            background: var(--accent-soft);
            border-color: var(--accent);
            transform: scale(1.1);
        }

        /* ============================================================
           UTILITY CLASSES (theme-aware text)
           ============================================================ */
        .text-muted {
            color: var(--text-muted);
        }
        .text-accent {
            color: var(--accent);
        }
        .hover-text-accent:hover {
            color: var(--accent-hover);
        }

        /* ============================================================
           RESPONSIVE ADJUSTMENTS
           ============================================================ */
        @media (max-width: 640px) {
            .theme-toggle, #scrollTopBtn {
                width: 42px;
                height: 42px;
                font-size: 1rem;
                bottom: 1rem;
                right: 1rem;
            }
            #scrollTopBtn {
                left: 1rem;
                right: auto;
            }
            .glass-card {
                padding: 1rem;
                border-radius: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Scroll progress -->
    <div id="scrollProgress"></div>

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Floating theme toggle -->
    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
        <!-- Sun icon (shown in dark mode) -->
        <i class="fas fa-sun" style="display: none;"></i>
        <!-- Moon icon (shown in light mode) -->
        <i class="fas fa-moon"></i>
    </button>

    <!-- Scroll to top button -->
    <button id="scrollTopBtn" aria-label="Scroll to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- ============================================================
         MAIN HEADER / NAVIGATION
         You can override this section in child views using @section('header')
         ============================================================ -->
    @hasSection('header')
        <header class="glass-header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                @yield('header')
            </div>
        </header>
    @else
        <!-- Default navigation (customizable) -->
        <header class="glass-header" x-data="{ mobileOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo / brand -->
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 rounded-xl shadow-md">
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-current">Sophisticate</span>
                            <span class="text-[10px] font-medium uppercase tracking-[0.2em] text-accent block -mt-0.5">Intensive Classes</span>
                        </div>
                    </a>

                    <!-- Desktop nav links -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="{{ url('/') }}" class="text-current/60 hover:text-current transition font-medium">Home</a>
                        <a href="{{ url('/about') }}" class="text-current/60 hover:text-current transition font-medium">About</a>
                        <a href="{{ url('/programs') }}" class="text-current/60 hover:text-current transition font-medium">Programs</a>
                        <a href="{{ url('/features') }}" class="text-current/60 hover:text-current transition font-medium">Features</a>
                        <a href="{{ url('/contact') }}" class="text-current/60 hover:text-current transition font-medium">Contact</a>
                    </nav>

                    <!-- Right side: login + mobile menu button -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="hidden sm:inline-block text-sm font-medium text-current/70 hover:text-current transition px-4 py-2 rounded-xl border"
                           style="background: var(--overlay-light); border-color: var(--border-color);">
                            Log in
                        </a>
                        <!-- Mobile menu toggle -->
                        <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Toggle menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile menu (Alpine.js) -->
                <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden pb-4">
                    <div class="flex flex-col space-y-3">
                        <a href="{{ url('/') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Home</a>
                        <a href="{{ url('/about') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">About</a>
                        <a href="{{ url('/programs') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Programs</a>
                        <a href="{{ url('/features') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Features</a>
                        <a href="{{ url('/contact') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Contact</a>
                        <a href="{{ route('login') }}" class="block px-4 py-2 rounded-lg text-center border"
                           style="background: var(--overlay-light); border-color: var(--border-color);">Log in</a>
                    </div>
                </div>
            </div>
        </header>
    @endif

    <!-- ============================================================
         MAIN CONTENT
         ============================================================ -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- ============================================================
         FOOTER (optional, can be overridden)
         ============================================================ -->
    @hasSection('footer')
        <footer class="mt-8">
            @yield('footer')
        </footer>
    @else
        <footer class="glass-header mt-8 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-muted">
                &copy; {{ date('Y') }} Sophisticate Intensive Classes. All rights reserved.
            </div>
        </footer>
    @endif

    <!-- ============================================================
         SCRIPTS
         ============================================================ -->
    <script>
        // ======================
        // THEME TOGGLE
        // ======================
        (function() {
            const toggleBtn = document.getElementById('themeToggle');
            const sunIcon = toggleBtn.querySelector('.fa-sun');
            const moonIcon = toggleBtn.querySelector('.fa-moon');

            function updateIcon(theme) {
                if (theme === 'dark') {
                    sunIcon.style.display = 'inline-block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'inline-block';
                }
            }

            // Set initial icon
            updateIcon(document.documentElement.getAttribute('data-theme'));

            toggleBtn.addEventListener('click', function() {
                const current = document.documentElement.getAttribute('data-theme');
                const next = current === 'light' ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
                updateIcon(next);
            });
        })();

        // ======================
        // SCROLL PROGRESS & SCROLL TO TOP
        // ======================
        (function() {
            const progress = document.getElementById('scrollProgress');
            const scrollTopBtn = document.getElementById('scrollTopBtn');

            window.addEventListener('scroll', () => {
                const scrollTop = window.scrollY;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const percent = (scrollTop / docHeight) * 100;
                if (progress) progress.style.width = percent + '%';

                if (scrollTopBtn) {
                    if (scrollTop > 300) {
                        scrollTopBtn.classList.add('visible');
                    } else {
                        scrollTopBtn.classList.remove('visible');
                    }
                }
            });

            if (scrollTopBtn) {
                scrollTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>