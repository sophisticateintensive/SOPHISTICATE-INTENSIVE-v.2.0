<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
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
    <meta name="theme-color" content="#09090b" id="theme-color-meta">
    <meta name="format-detection" content="telephone=no">

    <title>@yield('title', 'Sophisticate · Next-Gen Student Portal')</title>

    <!-- Next-Gen Typography: Plus Jakarta Sans & Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS with custom next-gen extensions -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"Space Grotesk"', 'monospace'],
                    },
                    colors: {
                        cyber: {
                            lime: '#ccff00',
                            emerald: '#10b981',
                            blue: '#3b82f6',
                            purple: '#a855f7',
                            pink: '#ec4899',
                            dark: '#09090b',
                            card: '#121215',
                            border: '#27272a',
                        }
                    },
                    borderRadius: {
                        '4xl': '2rem',
                        '5xl': '2.5rem',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome 6 Pro / Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --bg-canvas: #f8fafc;
            --bg-surface: #ffffff;
            --bg-surface-elevated: #f1f5f9;
            --border-subtle: #e2e8f0;
            --text-main: #09090b;
            --text-sub: #64748b;
            --accent-glow: rgba(37, 99, 235, 0.15);
            --accent-primary: #2563eb;
            --accent-contrast: #ffffff;
            --dock-bg: rgba(255, 255, 255, 0.94);
            --dock-border: rgba(226, 232, 240, 0.85);
        }

        .dark {
            --bg-canvas: #09090b;
            --bg-surface: #121215;
            --bg-surface-elevated: #18181c;
            --border-subtle: #27272a;
            --text-main: #f4f4f5;
            --text-sub: #a1a1aa;
            --accent-glow: rgba(59, 130, 246, 0.2);
            --accent-primary: #3b82f6;
            --accent-contrast: #ffffff;
            --dock-bg: rgba(18, 18, 21, 0.94);
            --dock-border: rgba(39, 39, 42, 0.85);
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            letter-spacing: -0.02em;
            transition: background-color 0.3s ease, color 0.3s ease;
            padding-top: env(safe-area-inset-top, 0px);
            padding-left: env(safe-area-inset-left, 0px);
            padding-right: env(safe-area-inset-right, 0px);
        }

        /* PWA Standalone Mode adjustments */
        body.pwa-standalone {
            user-select: none;
            -webkit-user-select: none;
        }

        [x-cloak] { display: none !important; }

        /* Subtle Next-Gen Grid Background */
        .bg-grid-pattern {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(59, 130, 246, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(59, 130, 246, 0.04) 1px, transparent 1px);
        }

        /* Dynamic Island Header */
        .dynamic-island {
            background: var(--dock-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--dock-border);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        /* Next-Gen Bottom Dock */
        .nextgen-dock {
            position: fixed;
            bottom: max(1rem, env(safe-area-inset-bottom, 1rem));
            left: 50%;
            transform: translateX(-50%);
            background: var(--dock-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--dock-border);
            border-radius: 9999px;
            padding: 0.35rem 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.2rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            z-index: 50;
            width: calc(100% - 1.5rem);
            max-width: 540px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .nextgen-dock::-webkit-scrollbar {
            display: none;
        }

        .dock-tab {
            flex: 1 1 0;
            min-width: 46px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.45rem 0.15rem;
            border-radius: 9999px;
            color: var(--text-sub);
            font-size: 0.62rem;
            font-weight: 700;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-decoration: none;
            position: relative;
            white-space: nowrap;
        }

        .dock-tab.active {
            background: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 4px 18px rgba(37, 99, 235, 0.4);
            transform: translateY(-2px);
        }

        .dock-tab i {
            font-size: 1.05rem;
            margin-bottom: 2px;
        }

        /* Next-Gen Card Utilities */
        .cyber-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 2rem;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .cyber-card:hover {
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px var(--accent-glow);
        }

        /* Clean Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-subtle); border-radius: 9999px; }
    </style>
</head>

<body class="bg-grid-pattern antialiased" x-data="{
    darkMode: true,
    init() {
        const saved = localStorage.getItem('theme');
        if (saved) {
            this.darkMode = (saved === 'dark');
        } else {
            this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this.applyTheme();
    },
    toggleTheme() {
        this.darkMode = !this.darkMode;
        this.applyTheme();
    },
    applyTheme() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            document.getElementById('theme-color-meta').setAttribute('content', '#09090b');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            document.getElementById('theme-color-meta').setAttribute('content', '#f8fafc');
        }
    }
}">

    <!-- Top Ambient Light Glow (School Blue) -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-48 bg-gradient-to-b from-blue-600/20 dark:from-blue-500/20 to-transparent blur-3xl pointer-events-none -z-10"></div>

    <!-- ==================== OFFLINE STATUS NOTIFICATION BAR ==================== -->
    <div id="pwaOfflineIndicator" class="hidden sticky top-0 z-50 bg-amber-500 text-zinc-950 px-4 py-2 text-xs font-mono font-bold flex items-center justify-center gap-2 shadow-lg backdrop-blur-md">
        <i class="fas fa-wifi-slash"></i>
        <span>Operating offline. Live changes will synchronize once internet is restored.</span>
    </div>

    <!-- ==================== 1. FLOATING DYNAMIC ISLAND TOP BAR ==================== -->
    <header class="sticky top-3 z-40 px-4 max-w-5xl mx-auto">
        <div class="dynamic-island rounded-full px-3.5 sm:px-4 py-2 flex items-center justify-between gap-2.5 sm:gap-3">
            
            <!-- Left: Brand Logo -->
            <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-2.5 group flex-shrink-0">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="School Logo"
                         class="w-8 h-8 rounded-full object-cover shadow-md group-hover:scale-110 transition-transform">
                @else
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xs font-mono group-hover:rotate-6 group-hover:scale-110 transition-transform shadow-md select-none leading-none">
                        <span class="tracking-tight">SIC</span>
                    </div>
                @endif
                <div class="hidden sm:block leading-tight">
                    <span class="text-[11px] font-black tracking-tight block text-zinc-900 dark:text-white">SOPHISTICATE</span>
                    <span class="text-[9px] font-mono text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider">Student Portal</span>
                </div>
            </a>

            <!-- Center: Intelligent Status Capsule -->
            <div class="flex items-center space-x-1.5 sm:space-x-2 px-2.5 sm:px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 text-xs font-mono min-w-0">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse flex-shrink-0"></span>
                <span class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 truncate">
                    {{ Auth::user()->student->reg_number ?? 'STUDENT' }}
                </span>
                @if(isset($activeTerm))
                    <span class="hidden md:inline-block text-[10px] text-zinc-400 font-medium pl-1 border-l border-zinc-300 dark:border-zinc-700 truncate">
                        {{ $activeTerm->term_name }}
                    </span>
                @endif
            </div>

            <!-- Right: Actions (Install App, Messages, Notifications, Theme, Profile & Logout) -->
            <div class="flex items-center space-x-1 sm:space-x-1.5 flex-shrink-0">
                
                <!-- Install App Button (Auto-detected PWA Trigger) -->
                <button type="button"
                    class="pwa-install-trigger hidden h-8 px-2.5 sm:px-3 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-black text-xs font-mono flex items-center gap-1.5 shadow-md hover:scale-105 active:scale-95 transition cursor-pointer"
                    title="Install Sophisticate Portal on your device">
                    <i class="fas fa-download text-[10px]"></i>
                    <span class="hidden sm:inline">Install App</span>
                    <span class="sm:hidden">Install</span>
                </button>

                <!-- Direct Messages Pill with live badge -->
                @php
                    $unreadMessagesCount = \App\Models\Message::where('student_id', Auth::user()->student?->id ?? 0)
                        ->where('is_read', false)
                        ->count();
                @endphp
                <a href="{{ route('student.messages.index') }}"
                    class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-xs relative hover:scale-110 active:scale-95 transition"
                    title="Advisory Messages">
                    <i class="fas fa-comment-dots"></i>
                    @if($unreadMessagesCount > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-pink-500 rounded-full ring-2 ring-zinc-900 animate-pulse"></span>
                    @endif
                </a>

                <!-- Notifications Pill -->
                <a href="{{ route('student.notifications.index') }}"
                    class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-xs relative hover:scale-110 active:scale-95 transition"
                    title="Notifications">
                    <i class="fas fa-bell"></i>
                    @php
                        $unreadCount = Auth::user()->student
                            ? Auth::user()->student->notifications()->wherePivot('is_read', false)->count()
                            : 0;
                    @endphp
                    @if($unreadCount > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full ring-2 ring-zinc-900"></span>
                    @endif
                </a>

                <!-- Theme Toggle -->
                <button @click="toggleTheme()"
                    class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-xs hover:scale-110 active:scale-95 transition"
                    title="Toggle Theme">
                    <i class="fas" :class="darkMode ? 'fa-sun text-amber-400' : 'fa-moon text-indigo-500'"></i>
                </button>

                <!-- Profile Avatar Pill -->
                <a href="{{ route('student.profile') }}"
                    class="h-8 pl-1 pr-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 flex items-center space-x-1.5 hover:ring-2 hover:ring-blue-500 transition">
                    @php $profilePic = Auth::user()->student?->profile_picture; @endphp
                    @if($profilePic)
                        <img src="{{ Storage::url($profilePic) }}" alt="avatar"
                             class="w-6 h-6 rounded-full object-cover ring-1 ring-blue-500/40 flex-shrink-0">
                    @else
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white font-black text-[10px] flex items-center justify-center shadow-sm flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="text-xs font-bold hidden md:inline truncate max-w-[80px]">{{ Auth::user()->name }}</span>
                </a>

                <!-- Website Link -->
                <a href="{{ route('welcome') }}"
                    class="w-8 h-8 rounded-full bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white flex items-center justify-center text-xs hover:scale-110 active:scale-95 transition"
                    title="Visit Website">
                    <i class="fas fa-home"></i>
                </a>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline m-0 p-0">
                    @csrf
                    <button type="submit"
                        class="w-8 h-8 rounded-full bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white flex items-center justify-center text-xs hover:scale-110 active:scale-95 transition cursor-pointer"
                        title="Sign Out of Student Portal">
                        <i class="fas fa-power-off"></i>
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- ==================== 2. MAIN VIEW CONTAINER ==================== -->
    <main class="max-w-5xl mx-auto px-4 pt-4 pb-36 sm:pb-32">
        <!-- Flash Notification Toast -->
        @if(session('success'))
            <div class="mb-4 p-4 rounded-3xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 text-xs font-bold flex items-center gap-2 backdrop-blur-md">
                <i class="fas fa-check-circle text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-3xl bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs font-bold flex items-center gap-2 backdrop-blur-md">
                <i class="fas fa-exclamation-triangle text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- ==================== 3. MOBILE FLOATING INSTALL BANNER ==================== -->
    <div id="pwaInstallBanner" class="hidden fixed bottom-24 left-4 right-4 max-w-md mx-auto z-40 bg-zinc-900/95 dark:bg-zinc-900/95 border border-blue-500/30 rounded-3xl p-4 shadow-2xl backdrop-blur-xl flex items-center justify-between gap-3 text-white">
        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ asset('icons/icon-96x96.png') }}" alt="App Icon" class="w-10 h-10 rounded-2xl object-cover ring-1 ring-blue-500/40 flex-shrink-0">
            <div class="min-w-0">
                <h4 class="text-xs font-black tracking-tight truncate">Install Sophisticate App</h4>
                <p class="text-[11px] text-zinc-400 font-mono line-clamp-1">Install to your home screen for quick access</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <button type="button" class="pwa-install-trigger px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs font-mono shadow-md transition">
                Install
            </button>
            <button type="button" onclick="window.SophisticatePWA.dismissBanner()" class="w-7 h-7 rounded-full bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white text-xs flex items-center justify-center transition">
                &times;
            </button>
        </div>
    </div>

    <!-- ==================== 4. iOS INSTALL INSTRUCTIONS MODAL ==================== -->
    <div id="pwaIOSModal" class="hidden fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-black/80 backdrop-blur-md p-4" onclick="if(event.target === this) window.SophisticatePWA.closeIOSGuide()">
        <div class="bg-zinc-900 border border-zinc-700 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5 text-white animate-in fade-in zoom-in duration-200">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('icons/icon-96x96.png') }}" alt="Logo" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-blue-500/30">
                    <div>
                        <h3 class="text-base font-black">Install on iPhone / iPad</h3>
                        <p class="text-xs text-zinc-400 font-mono">Sophisticate Student Portal</p>
                    </div>
                </div>
                <button type="button" onclick="window.SophisticatePWA.closeIOSGuide()" class="w-8 h-8 rounded-full bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center text-sm font-bold">
                    &times;
                </button>
            </div>

            <div class="space-y-3 font-mono text-xs text-zinc-300">
                <div class="p-3.5 rounded-2xl bg-zinc-800/80 border border-zinc-700/60 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">1</span>
                    <p class="leading-relaxed">Tap the <strong class="text-white">Share</strong> button <i class="fas fa-arrow-up-from-bracket text-blue-400 mx-1"></i> in the Safari toolbar at the bottom.</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-zinc-800/80 border border-zinc-700/60 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">2</span>
                    <p class="leading-relaxed">Scroll down the share sheet and select <strong class="text-white"><i class="far fa-plus-square text-blue-400 mr-1"></i> Add to Home Screen</strong>.</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-zinc-800/80 border border-zinc-700/60 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">3</span>
                    <p class="leading-relaxed">Tap <strong class="text-white">Add</strong> in the top right corner to complete installation.</p>
                </div>
            </div>

            <button type="button" onclick="window.SophisticatePWA.closeIOSGuide()" class="w-full py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 font-bold text-xs text-white transition">
                Got It
            </button>
        </div>
    </div>

    <!-- ==================== 5. NEXT-GEN FLOATING CAPSULE DOCK ==================== -->
    <nav class="nextgen-dock">
        <!-- 1. Home / Dashboard -->
        <a href="{{ route('student.dashboard') }}" class="dock-tab {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="fas fa-compass"></i>
            <span>Hub</span>
        </a>

        <!-- 2. Courses -->
        <a href="{{ route('student.subjects.index') }}" class="dock-tab {{ request()->routeIs('student.subjects.*') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Courses</span>
        </a>

        <!-- 3. Learning Vault / Resources -->
        <a href="{{ route('student.resources.index') }}" class="dock-tab {{ request()->routeIs('student.resources.*') ? 'active' : '' }}">
            <i class="fas fa-folder-open text-cyan-500 dark:text-cyan-400"></i>
            <span>Vault</span>
        </a>

        <!-- 4. Quizzes (Interactive Test Center) -->
        <a href="{{ route('student.quizzes.index') }}" class="dock-tab {{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}">
            <i class="fas fa-bolt text-blue-500 dark:text-blue-400"></i>
            <span>Quizzes</span>
        </a>

        <!-- 5. Messages / Advisory -->
        <a href="{{ route('student.messages.index') }}" class="dock-tab {{ request()->routeIs('student.messages.*') ? 'active' : '' }}">
            <i class="fas fa-comment-dots"></i>
            <span>Advisory</span>
        </a>

        <!-- 6. Results / Transcripts -->
        <a href="{{ route('student.results.index') }}" class="dock-tab {{ request()->routeIs('student.results.*') ? 'active' : '' }}">
            <i class="fas fa-chart-simple"></i>
            <span>Grades</span>
        </a>

        <!-- 7. Timetable -->
        <a href="{{ route('student.timetable.index') }}" class="dock-tab {{ request()->routeIs('student.timetable.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Schedule</span>
        </a>

        <!-- 8. Fees / Billing -->
        <a href="{{ route('student.fees.index') }}" class="dock-tab {{ request()->routeIs('student.fees.*') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i>
            <span>Wallet</span>
        </a>
    </nav>

    <!-- Client-side PWA installer and lifecycle script -->
    <script src="{{ asset('js/pwa.js') }}"></script>

    @stack('scripts')
</body>
</html>
