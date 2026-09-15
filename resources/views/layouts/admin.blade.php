<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin · Sophisticate Intensive')</title>

    <!-- Premium Typography: Plus Jakarta Sans + Outfit + Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS with custom font config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        heading: ['"Outfit"', '"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"Space Grotesk"', 'ui-monospace', 'monospace'],
                    },
                    letterSpacing: {
                        tighter: '-0.04em',
                        tight: '-0.02em',
                        normal: '-0.01em',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js (deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Theme detection before CSS (prevents flash) -->
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
           CSS VARIABLES – DARK/LIGHT THEMING
           ============================================================ */
        :root {
            /* Base colors */
            --bg-primary: #f0f7ff;
            --bg-card: rgba(255, 255, 255, 0.65);
            --bg-sidebar: rgba(255, 255, 255, 0.75);
            --border-color: rgba(255, 255, 255, 0.4);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --shadow-color: rgba(59, 130, 246, 0.15);
            --sidebar-shadow: 0 20px 60px rgba(0,0,0,0.08);
            --glass-blur: 16px;

            /* Accent */
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --accent-soft: rgba(59, 130, 246, 0.12);
            --accent-glow: rgba(59, 130, 246, 0.4);
            --accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
            --brand-gradient: linear-gradient(135deg, #3b82f6, #8b5cf6);

            /* Overlays */
            --overlay-light: rgba(255, 255, 255, 0.5);
            --overlay-strong: rgba(255, 255, 255, 0.8);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.1);

            /* Notification & modal backgrounds */
            --dropdown-bg: rgba(255, 255, 255, 0.85);
            --modal-bg: rgba(255, 255, 255, 0.9);
        }

        /* Dark theme overrides */
        html[data-theme="dark"] {
            --bg-primary: #0a0e1a;
            --bg-card: rgba(10, 14, 26, 0.65);
            --bg-sidebar: rgba(10, 14, 26, 0.8);
            --border-color: rgba(255, 255, 255, 0.06);
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --shadow-color: rgba(59, 130, 246, 0.25);
            --sidebar-shadow: 0 20px 60px rgba(0,0,0,0.4);
            --glass-blur: 20px;

            --accent: #3b82f6;
            --accent-hover: #60a5fa;
            --accent-soft: rgba(59, 130, 246, 0.2);
            --accent-glow: rgba(59, 130, 246, 0.6);
            --accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
            --brand-gradient: linear-gradient(135deg, #60a5fa, #a78bfa);

            --overlay-light: rgba(255, 255, 255, 0.05);
            --overlay-strong: rgba(255, 255, 255, 0.1);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.4);

            --dropdown-bg: rgba(10, 14, 26, 0.9);
            --modal-bg: rgba(10, 14, 26, 0.9);
        }

        /* ============================================================
           BASE STYLES
           ============================================================ */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: background 0.4s ease, color 0.4s ease;
            overflow-x: hidden;
            min-height: 100vh;
            line-height: 1.5;
            letter-spacing: -0.012em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.025em;
        }

        /* ============================================================
           ANIMATED BACKGROUND BLOBS
           ============================================================ */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.2;
            z-index: -1;
            animation: blobFloat 18s ease-in-out infinite alternate;
            transition: background 0.6s ease;
            pointer-events: none;
        }
        .blob-1 { width: 50vw; height: 50vw; background: #3b82f6; top: -20%; right: -10%; }
        .blob-2 { width: 40vw; height: 40vw; background: #2563eb; bottom: -20%; left: -10%; animation-delay: 5s; }
        .blob-3 { width: 30vw; height: 30vw; background: #60a5fa; top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: 10s; }

        @keyframes blobFloat {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, -30px) scale(1.1); }
        }

        /* ============================================================
           CUSTOM SCROLLBAR
           ============================================================ */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-primary); }
        ::-webkit-scrollbar-thumb {
            background: var(--accent-gradient);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(135deg, var(--accent-hover), #4f46e5); }

        /* ============================================================
           TOP NAVIGATION (glass)
           ============================================================ */
        .top-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: var(--bg-card);
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: background 0.4s, border-color 0.4s;
            padding: 0 1.5rem;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ============================================================
           SIDEBAR (floating glass panel)
           ============================================================ */
        .sidebar {
            position: fixed;
            top: 80px;
            left: 1.5rem;
            bottom: 1.5rem;
            width: 280px;
            background: var(--bg-sidebar);
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border: 1px solid var(--border-color);
            border-radius: 2rem;
            box-shadow: var(--sidebar-shadow);
            padding: 1.5rem 1rem;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        background 0.4s, border-color 0.4s;
            z-index: 40;
            transform: translateX(0);
        }
        .sidebar.collapsed {
            transform: translateX(-120%);
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(59,130,246,0.3); border-radius: 10px; }

        /* ============================================================
           SIDEBAR NAV LINKS
           ============================================================ */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-secondary);
            transition: all 0.2s;
            text-decoration: none;
            gap: 0.75rem;
        }
        .nav-link:hover {
            background: var(--accent-soft);
            color: var(--text-primary);
        }
        .nav-link.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 600;
            box-shadow: inset 0 0 0 1px var(--accent-glow);
        }
        .nav-link i {
            width: 1.5rem;
            text-align: center;
            font-size: 1.1rem;
            color: var(--text-secondary);
            transition: color 0.2s;
        }
        .nav-link.active i {
            color: var(--accent);
        }

        /* Sidebar footer (system info) */
        .sidebar-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* ============================================================
           MAIN CONTENT AREA
           ============================================================ */
        .main-content {
            margin-left: 0;
            padding: 2rem;
            transition: margin-left 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .main-content.shifted {
            margin-left: 300px;
        }

        /* ============================================================
           GLASS CARD & DROPDOWN & MODAL
           ============================================================ */
        .content-card {
            background: var(--bg-card);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            padding: 1.5rem;
            transition: background 0.4s, border-color 0.4s;
        }

        .dropdown-glass {
            background: var(--dropdown-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .modal-glass {
            background: var(--modal-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            box-shadow: 0 30px 80px var(--shadow-color);
        }

        /* ============================================================
           THEME TOGGLE BUTTON
           ============================================================ */
        .theme-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--text-primary);
        }
        .theme-btn:hover {
            transform: scale(1.1);
            border-color: var(--accent);
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 1024px) {
            .sidebar { width: 260px; left: 1rem; }
            .main-content.shifted { margin-left: 280px; }
        }
        @media (max-width: 768px) {
            .sidebar {
                left: 0;
                top: 0;
                bottom: 0;
                width: 100%;
                border-radius: 0;
                padding: 2rem 1.5rem;
                transform: translateX(-100%);
            }
            .sidebar.collapsed { transform: translateX(-100%); }
            .sidebar:not(.collapsed) { transform: translateX(0); }
            .main-content.shifted { margin-left: 0; }
            .top-nav { padding: 0 1rem; height: 60px; }
        }

        /* Utility to hide body scroll when mobile sidebar open */
        .no-scroll { overflow: hidden; }
    </style>
</head>

<body x-data="{
    sidebarOpen: false,
    darkMode: document.documentElement.getAttribute('data-theme') === 'dark',
    init() {
        this.$watch('darkMode', val => {
            document.documentElement.setAttribute('data-theme', val ? 'dark' : 'light');
            localStorage.setItem('theme', val ? 'dark' : 'light');
        });
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        if (window.innerWidth <= 768) {
            document.body.classList.toggle('no-scroll', this.sidebarOpen);
        }
    }
}" @resize.window="if(window.innerWidth > 768) document.body.classList.remove('no-scroll')">

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- ====== TOP NAVIGATION ====== -->
    <header class="top-nav">
        <div class="flex items-center space-x-4">
            <!-- Hamburger -->
            <button @click="toggleSidebar()" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Toggle sidebar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" x-show="!sidebarOpen"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-show="sidebarOpen"></path>
                </svg>
            </button>

            <!-- Logo + Brand -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.png') }}" alt="Sophisticate" class="h-10 w-10 object-contain rounded-xl shadow-lg">
                <div class="hidden sm:block">
                    <span class="text-lg font-bold" style="background: var(--brand-gradient); -webkit-background-clip: text; background-clip: text; color: transparent;">Sophisticate</span>
                    <span class="text-[10px] font-medium uppercase tracking-widest text-accent block -mt-0.5">Intensive Classes</span>
                </div>
            </a>

            <!-- Divider -->
            <span class="hidden md:inline-block h-6 w-px bg-gray-300/30"></span>
            <span class="hidden md:inline-block text-sm text-muted">Admin Panel</span>
        </div>

        <!-- Right side: theme + notifications + user -->
        <div class="flex items-center space-x-3">
            <!-- Theme toggle -->
            <button @click="darkMode = !darkMode" class="theme-btn" aria-label="Toggle theme">
                <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition relative">
                    <i class="fas fa-bell text-xl"></i>
                    @php
                        $unreadCount = \App\Models\Notification::where('is_sent_to_all', true)
                            ->orWhere('student_id', Auth::user()->student?->id)
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-500 rounded-full shadow-lg">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>

                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute right-0 mt-3 w-80 dropdown-glass overflow-hidden z-50">
                    <div class="px-4 py-3" style="background: var(--accent-gradient); color: white;">
                        <h3 class="text-sm font-semibold">Notifications</h3>
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800">
                        @php
                            $notifications = \App\Models\Notification::latest()->take(5)->get();
                        @endphp
                        @forelse($notifications as $notification)
                            <a href="{{ route('admin.notifications.show', $notification) }}" class="block px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                <p class="text-sm font-medium text-current">{{ $notification->title }}</p>
                                <p class="text-xs text-muted mt-1">{{ Str::limit($notification->message, 60) }}</p>
                                <p class="text-xs text-muted mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </a>
                        @empty
                            <p class="px-4 py-6 text-sm text-muted text-center">No new notifications</p>
                        @endforelse
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.notifications.index') }}" class="text-sm text-accent hover:underline">View all →</a>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold shadow-md">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span class="hidden md:inline text-sm font-medium">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down text-xs text-muted hidden md:inline"></i>
                </button>

                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute right-0 mt-3 w-56 dropdown-glass overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-semibold text-current">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-muted">{{ Auth::user()->email }}</p>
                        <p class="text-xs text-muted mt-1">Role: {{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-current hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                        <i class="fas fa-user-circle w-5 text-accent"></i>
                        <span class="ml-3">Profile Settings</span>
                    </a>
                    <a href="{{ route('welcome') }}" class="flex items-center px-4 py-2.5 text-sm text-current hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                        <i class="fas fa-home w-5 text-accent"></i>
                        <span class="ml-3">Visit Website</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-200 dark:border-gray-700">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="ml-3">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- ====== SIDEBAR (floating) ====== -->
    <aside class="sidebar" :class="sidebarOpen ? '' : 'collapsed'">
        <nav class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>

            <!-- Students -->
            <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') && !request()->routeIs('admin.students.subjects.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Students</span>
            </a>

            <!-- Enrollments -->
            <a href="{{ route('admin.enrollments.index') }}" class="nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                <i class="fas fa-file-signature"></i>
                <span>Enrollments</span>
            </a>

            <!-- Courses -->
            <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ request()->routeIs('admin.subjects.*') && !request()->routeIs('admin.students.subjects.*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i>
                <span>Courses</span>
            </a>

            <!-- Course Assignment -->
            <a href="{{ route('admin.new-subject-assignment.index') }}" class="nav-link {{ request()->routeIs('admin.students.subjects.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i>
                <span>Assign Courses</span>
            </a>

            <!-- Academic Years -->
            <a href="{{ route('admin.academic-years.index') }}" class="nav-link {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Academic Years</span>
            </a>

            <!-- Semesters -->
            <a href="{{ route('admin.terms.index') }}" class="nav-link {{ request()->routeIs('admin.terms.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                <span>Semesters</span>
            </a>

            <!-- Quizzes -->
            <a href="{{ route('admin.quizzes.index') }}" class="nav-link {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
                <i class="fas fa-puzzle-piece"></i>
                <span>Quizzes</span>
            </a>

            <!-- Results -->
            <a href="{{ route('admin.results.index') }}" class="nav-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Results</span>
            </a>

            <!-- Fees -->
            <a href="{{ route('admin.fees.index') }}" class="nav-link {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
                <i class="fas fa-coins"></i>
                <span>Fees</span>
            </a>

            <!-- Timetable -->
            <a href="{{ route('admin.timetable.index') }}" class="nav-link {{ request()->routeIs('admin.timetable.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Timetable</span>
            </a>

            <!-- Reports -->
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>

            <!-- Notifications -->
            <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>

            <!-- Messages -->
            <a href="{{ route('admin.messages.index') }}" class="nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span>Messages</span>
            </a>

            <!-- Resources -->
            <a href="{{ route('admin.resources.index') }}" class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
                <i class="fas fa-folder-open"></i>
                <span>Resources</span>
            </a>

            <!-- Backups & Cloud Sync -->
            <a href="{{ route('admin.backups.index') }}" class="nav-link {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Cloud Backups</span>
            </a>
        </nav>

        <!-- Sidebar footer (system info) -->
        <div class="sidebar-footer">
            <div class="rounded-xl p-3" style="background: var(--accent-soft);">
                <p class="text-xs font-medium text-accent uppercase tracking-wider">System</p>
                <p class="text-sm text-current">Laravel v{{ app()->version() }}</p>
                <p class="text-xs text-muted mt-1">{{ now()->format('F j, Y') }}</p>
            </div>
        </div>
    </aside>

    <!-- ====== MAIN CONTENT ====== -->
    <main class="main-content" :class="sidebarOpen ? 'shifted' : ''">
        <!-- Page Header (dynamic via yield) -->
        <div class="mb-6">
            @yield('header')
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-4 p-4 rounded-xl bg-blue-100 dark:bg-blue-900/30 border-l-4 border-blue-500 text-blue-700 dark:text-blue-300">
                {{ session('info') }}
            </div>
        @endif

        <!-- Content -->
        <div class="content-card">
            @yield('content')
        </div>
    </main>

    <!-- ====== FOOTER ====== -->
    <footer class="text-center text-sm text-muted py-6 border-t border-gray-200 dark:border-gray-800 mt-8">
        <div class="flex items-center justify-center space-x-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-5 w-5 object-contain">
            <span>&copy; {{ date('Y') }} Sophisticate Intensive Classes. All rights reserved.</span>
        </div>
    </footer>

    <!-- ====== DELETE CONFIRMATION MODAL (glass) ====== -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="modal-glass w-full max-w-md transform transition-all">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-trash-alt text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Confirm Deletion</h3>
                        <p class="text-xs text-red-200">This action cannot be undone</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-2">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-current font-medium" id="deleteMessage">
                            Are you sure you want to delete this item?
                        </p>
                        <p class="text-sm text-muted mt-1" id="deleteDetails">
                            This action is permanent and cannot be reversed.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-current rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteBtn" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition shadow-md">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>

    <!-- ====== SCRIPTS (modal, etc.) ====== -->
    <script>
        let deleteForm = null;
        let deleteCallback = null;

        function openDeleteModal(message, details, formOrCallback) {
            const modal = document.getElementById('deleteModal');
            document.getElementById('deleteMessage').textContent = message || 'Are you sure you want to delete this item?';
            document.getElementById('deleteDetails').textContent = details || 'This action is permanent and cannot be reversed.';

            if (typeof formOrCallback === 'object' && formOrCallback && formOrCallback.tagName === 'FORM') {
                deleteForm = formOrCallback;
                deleteCallback = null;
            } else {
                deleteForm = null;
                deleteCallback = formOrCallback;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            deleteForm = null;
            deleteCallback = null;
        }

        function confirmDelete() {
            if (deleteForm) {
                deleteForm.submit();
            } else if (deleteCallback && typeof deleteCallback === 'function') {
                deleteCallback();
            }
            closeDeleteModal();
        }

        // Close modal on background click
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('deleteModal');
            if (e.target === modal) closeDeleteModal();
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);
        });
    </script>

    @stack('scripts')
</body>
</html>
