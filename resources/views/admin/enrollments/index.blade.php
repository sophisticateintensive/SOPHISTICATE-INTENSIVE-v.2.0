{{-- resources/views/admin/enrollments/index.blade.php --}}

@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Student Enrollments
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Manage student enrollments for academic years and terms</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-semester-selector 
                :selected="$selectedTermId" 
                :activeTerm="$activeTerm" 
                :isHistorical="$isHistorical" 
                :allTerms="$terms"
            />

            <a href="{{ route('admin.enrollments.export', request()->all()) }}"
                class="inline-flex items-center px-3 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-lg border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-file-export mr-1.5 text-xs text-emerald-500"></i>
                Export
            </a>

            <a href="{{ route('admin.enrollments.create') }}"
                class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
                <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Enrollment
            </a>

            <button onclick="openBulkEnrollModal()"
                class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Bulk Enroll
            </button>
        </div>
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
        .enrollments-container {
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
            padding: 2rem 2.5rem;
            position: relative;
        }
        .enrollments-container::before {
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
            .enrollments-container { padding: 1.5rem; border-radius: 2rem; }
            .enrollments-container::before { display: none; }
        }

        /* ----- Stats row (glass) ----- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-glass {
            background: var(--bg-card);
            backdrop-filter: blur(4px);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 1rem 1.25rem;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-glass:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px var(--shadow-color);
        }
        .stat-glass .number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .stat-glass .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            letter-spacing: 0.05em;
        }
        .stat-glass .number.active { color: #10b981; }
        .stat-glass .number.graduated { color: #3b82f6; }
        .stat-glass .number.suspended { color: #f59e0b; }
        .stat-glass .number.withdrawn { color: #ef4444; }

        /* ----- Filter bar (glass) ----- */
        .filter-glass {
            background: var(--bg-card);
            backdrop-filter: blur(4px);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
        }
        .filter-glass select, .filter-glass input {
            width: 100%;
            padding: 0.6rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            background: rgba(255,255,255,0.15);
            color: var(--text-primary);
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        .filter-glass select:focus, .filter-glass input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
            outline: none;
        }
        .filter-glass select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
            padding-right: 2.5rem;
        }
        .filter-glass .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .filter-glass .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-blue { background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; box-shadow: 0 4px 12px rgba(59,130,246,0.25); }
        .btn-blue:hover { transform: scale(1.02); box-shadow: 0 6px 20px rgba(59,130,246,0.35); }
        .btn-gray { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); }
        .btn-gray:hover { border-color: #3b82f6; transform: scale(1.02); }
        .btn-green { background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 12px rgba(16,185,129,0.25); }
        .btn-green:hover { transform: scale(1.02); box-shadow: 0 6px 20px rgba(16,185,129,0.35); }

        /* ----- Enrollment Cards (glass) ----- */
        .enrollment-card {
            background: var(--bg-card);
            backdrop-filter: blur(4px);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            position: relative;
        }
        .enrollment-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px var(--shadow-color);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .enrollment-card .card-header {
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
        }
        .enrollment-card .card-body {
            padding: 1rem 1.25rem;
        }
        .enrollment-card .card-footer {
            padding: 0.75rem 1.25rem;
            background: var(--bg-card);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        .enrollment-card .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .enrollment-card .status-badge {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
        }
        .status-active { background: #d1fae5; color: #065f46; }
        .status-graduated { background: #dbeafe; color: #1e40af; }
        .status-suspended { background: #fef3c7; color: #92400e; }
        .status-withdrawn { background: #fecaca; color: #991b1b; }

        .card-detail {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
            font-size: 0.85rem;
            border-bottom: 1px dashed var(--border-color);
        }
        .card-detail:last-child { border-bottom: none; }
        .card-detail .label { color: var(--text-secondary); }
        .card-detail .value { font-weight: 600; color: var(--text-primary); }

        /* ----- Pagination (glass) ----- */
        .pagination-glass {
            background: var(--bg-card);
            backdrop-filter: blur(4px);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 0.75rem 1.25rem;
            margin-top: 1.5rem;
        }

        /* ----- BULK ENROLL MODAL (glass) ----- */
        #bulkEnrollModal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow-y: auto;
        }
        #bulkEnrollModal.open {
            display: flex;
        }
        .modal-glass {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 2rem;
            box-shadow: 0 30px 80px var(--shadow-color);
            max-width: 900px;
            width: 100%;
            max-height: 95vh;
            overflow-y: auto;
            padding: 0;
            position: relative;
        }
        .modal-glass .modal-header {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            padding: 1.25rem 1.5rem;
            border-radius: 2rem 2rem 0 0;
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-glass .modal-header h3 {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
        }
        .modal-glass .modal-header button {
            color: white;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            transition: transform 0.2s;
        }
        .modal-glass .modal-header button:hover { transform: scale(1.1); }
        .modal-glass .modal-body {
            padding: 1.5rem;
        }
        .modal-glass .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: var(--bg-card);
            border-radius: 0 0 2rem 2rem;
            position: sticky;
            bottom: 0;
            z-index: 10;
            backdrop-filter: blur(8px);
        }

        .modal-glass .student-table-wrap {
            overflow-x: auto;
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            margin-top: 0.5rem;
        }
        .modal-glass .student-table-wrap table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        .modal-glass .student-table-wrap th {
            background: var(--bg-card);
            padding: 0.6rem 0.8rem;
            text-align: left;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border-color);
        }
        .modal-glass .student-table-wrap td {
            padding: 0.5rem 0.8rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        .modal-glass .student-table-wrap tr:hover {
            background: rgba(59, 130, 246, 0.04);
        }
        .modal-glass .checkbox-column { width: 40px; }
        .modal-glass .select-all-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .modal-glass .select-all-wrap input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #3b82f6;
        }
        .modal-glass .select-all-wrap label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .modal-glass .search-filters {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .modal-glass .search-filters input, .modal-glass .search-filters select {
            padding: 0.5rem 0.8rem;
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            background: rgba(255,255,255,0.15);
            color: var(--text-primary);
            font-size: 0.9rem;
        }
        .modal-glass .search-filters input:focus, .modal-glass .search-filters select:focus {
            border-color: #3b82f6;
            outline: none;
        }

        /* ----- Empty state ----- */
        .empty-state-glass {
            background: var(--bg-card);
            backdrop-filter: blur(4px);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 3rem 2rem;
            text-align: center;
        }

        /* ----- Responsive ----- */
        @media (max-width: 640px) {
            .enrollments-container { padding: 1rem; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
            .filter-glass .grid { grid-template-columns: 1fr; }
            .filter-glass .btn-group { flex-wrap: wrap; }
            .filter-glass .btn { flex: 1; text-align: center; }
            .enrollment-card .card-header { flex-wrap: wrap; gap: 0.5rem; }
            .modal-glass .search-filters { grid-template-columns: 1fr; }
            .modal-glass .modal-body { padding: 1rem; }
        }
    </style>

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    @if($isHistorical)
        <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-4 font-mono text-xs shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-sm"></i>
                </div>
                <div>
                    <span class="font-bold uppercase tracking-wider block">HISTORICAL ARCHIVE VIEW</span>
                    <span>Viewing enrollment records for historical semester.</span>
                </div>
            </div>
            <a href="{{ route('admin.enrollments.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 text-zinc-950 font-bold hover:scale-105 transition flex-shrink-0">
                Switch to Active Semester &rarr;
            </a>
        </div>
    @endif

    <div class="enrollments-container">
        @php
            $total = $enrollments->total();
            $activeCount = $enrollments->filter(fn($e) => $e->status === 'active')->count();
            $graduatedCount = $enrollments->filter(fn($e) => $e->status === 'graduated')->count();
            $suspendedCount = $enrollments->filter(fn($e) => $e->status === 'suspended')->count();
            $withdrawnCount = $enrollments->filter(fn($e) => $e->status === 'withdrawn')->count();
        @endphp

        <!-- ========== HERO / STATS SECTION (STUDENT MANAGEMENT DESIGN) ========== -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 mb-8 shadow-2xl">
            <!-- Animated blobs -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
            </div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4"
                 x-data="{ counters: { total: 0, active: 0, graduated: 0, suspended: 0 }, init() {
                    const targets = { total: {{ $total }}, active: {{ $activeCount }}, graduated: {{ $graduatedCount }}, suspended: {{ $suspendedCount }} };
                    Object.keys(targets).forEach(key => {
                        const interval = setInterval(() => {
                            if (this.counters[key] < targets[key]) {
                                this.counters[key] += Math.ceil(targets[key] / 30);
                                if (this.counters[key] > targets[key]) this.counters[key] = targets[key];
                            } else {
                                clearInterval(interval);
                            }
                        }, 40);
                    });
                }}">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        {{ $activeCount }} Active Matriculated · {{ $graduatedCount }} Graduated
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Student Enrollment Census</h1>
                    <p class="text-blue-100/80 mt-1">Official registry of academic enrollments, term statuses, and cohort progression</p>
                </div>

                <!-- Floating stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                        <div class="text-2xl font-bold text-white" x-text="counters.total"></div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Total</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                        <div class="text-2xl font-bold text-emerald-300" x-text="counters.active"></div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Active</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                        <div class="text-2xl font-bold text-purple-300" x-text="counters.graduated"></div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Graduated</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                        <div class="text-2xl font-bold text-amber-300" x-text="counters.suspended"></div>
                        <div class="text-xs text-blue-200 uppercase tracking-wider">Suspended</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== FILTER BAR (glass) ====== -->
        <div class="filter-glass">
            <form action="{{ route('admin.enrollments.index') }}" method="GET" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <select name="academic_year" id="academic_year_filter" class="w-full">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
                                    {{ $year->year_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="term" id="term_filter" class="w-full">
                            <option value="">All Terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" data-academic-year-id="{{ $term->academic_year_id }}" {{ request('term') == $term->id ? 'selected' : '' }}>
                                    {{ $term->term_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="status" id="status_filter" class="w-full">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" name="search" placeholder="Search by name or reg number..." value="{{ request('search') }}" class="w-full">
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-blue flex-1">Filter</button>
                        <button type="button" onclick="resetFilters()" class="btn btn-gray">Reset</button>
                        <button type="button" onclick="exportEnrollments()" class="btn btn-green">Export</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ====== ENROLLMENT CARDS ====== -->
        @if($enrollments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($enrollments as $enrollment)
                    <div class="enrollment-card">
                        <div class="card-header">
                            <div class="flex items-center gap-3">
                                <div class="avatar">{{ substr($enrollment->student->user->name, 0, 1) }}</div>
                                <div>
                                    <div class="font-semibold text-sm text-[var(--text-primary)]">{{ $enrollment->student->user->name }}</div>
                                    <div class="text-xs text-[var(--text-secondary)]">{{ $enrollment->student->reg_number }}</div>
                                </div>
                            </div>
                            <span class="status-badge status-{{ $enrollment->status }}">{{ ucfirst($enrollment->status) }}</span>
                        </div>

                        <div class="card-body">
                            <div class="card-detail">
                                <span class="label">Programme</span>
                                <span class="value">{{ $enrollment->programme }}</span>
                            </div>
                            <div class="card-detail">
                                <span class="label">Academic Year</span>
                                <span class="value">{{ $enrollment->academicYear->year_name ?? 'N/A' }}</span>
                            </div>
                            <div class="card-detail">
                                <span class="label">Term</span>
                                <span class="value">{{ $enrollment->term->term_name ?? 'N/A' }}</span>
                            </div>
                            <div class="card-detail">
                                <span class="label">Enrollment Date</span>
                                <span class="value">{{ $enrollment->enrollment_date->format('M d, Y') }}</span>
                            </div>
                            @if($enrollment->expected_graduation_date)
                                <div class="card-detail">
                                    <span class="label">Expected Graduation</span>
                                    <span class="value">{{ \Carbon\Carbon::parse($enrollment->expected_graduation_date)->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="p-1.5 text-indigo-500 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="p-1.5 text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" class="inline" onsubmit="return confirm('Delete this enrollment?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-glass">
                {{ $enrollments->withQueryString()->links() }}
            </div>
        @else
            <div class="empty-state-glass">
                <svg class="w-16 h-16 mx-auto text-[var(--text-secondary)] opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-3 text-lg font-semibold text-[var(--text-primary)]">No enrollments found</h3>
                <p class="text-[var(--text-secondary)]">Get started by creating your first enrollment.</p>
                <a href="{{ route('admin.enrollments.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Enrollment
                </a>
            </div>
        @endif
    </div>

    <!-- ====== BULK ENROLL MODAL (glass) ====== -->
    <div id="bulkEnrollModal" class="modal-glass-wrapper">
        <div class="modal-glass">
            <div class="modal-header">
                <h3>Bulk Enroll Students</h3>
                <button onclick="closeBulkEnrollModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.enrollments.bulk-enroll') }}" method="POST" id="bulkForm">
                    @csrf

                    <!-- Student Selection with Search & Filter -->
                    <div class="search-filters">
                        <input type="text" id="bulkSearch" placeholder="Search by name or reg..." class="w-full">
                        <select id="bulkProgrammeFilter" class="w-full">
                            <option value="">All Programmes</option>
                            @foreach($programmes ?? [] as $prog)
                                <option value="{{ $prog }}">{{ $prog }}</option>
                            @endforeach
                        </select>
                        <select id="bulkStatusFilter" class="w-full">
                            <option value="">All Status</option>
                            <option value="not_enrolled">Not Enrolled</option>
                            <option value="enrolled">Already Enrolled</option>
                        </select>
                    </div>

                    <div class="select-all-wrap">
                        <input type="checkbox" id="bulkSelectAll">
                        <label for="bulkSelectAll">Select All Visible</label>
                        <span class="text-xs text-[var(--text-secondary)] ml-auto" id="bulkFilteredCount"></span>
                    </div>

                    <div class="student-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th class="checkbox-column"><input type="checkbox" id="bulkSelectAllTable" class="hidden"></th>
                                    <th>Student</th>
                                    <th>Reg Number</th>
                                    <th>Programme</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="bulkStudentTableBody">
                                @foreach($allStudents ?? [] as $student)
                                    @php
                                        $hasActiveEnrollment = $student->enrollments()->where('status', 'active')->exists();
                                        $enrollmentStatus = $hasActiveEnrollment ? 'enrolled' : 'not_enrolled';
                                    @endphp
                                    <tr class="bulk-student-row" data-name="{{ strtolower($student->user->name) }}" data-reg="{{ strtolower($student->reg_number) }}" data-programme="{{ $student->programme }}" data-status="{{ $enrollmentStatus }}">
                                        <td class="checkbox-column">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="bulk-student-checkbox" {{ $hasActiveEnrollment ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                                                    {{ substr($student->user->name, 0, 1) }}
                                                </div>
                                                <span>{{ $student->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $student->reg_number }}</td>
                                        <td>{{ $student->programme }}</td>
                                        <td>
                                            @if($hasActiveEnrollment)
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Enrolled</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">Not Enrolled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] mt-2">Students already enrolled are disabled.</p>

                    <!-- Enrollment Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="input-group" style="margin-bottom:0;">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <select name="academic_year_id" id="bulkAcademicYear" class="input-field" required>
                                <option value="">Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->year_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group" style="margin-bottom:0;">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <select name="term_id" id="bulkTerm" class="input-field" required>
                                <option value="">Select Term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" data-academic-year-id="{{ $term->academic_year_id }}">{{ $term->term_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group md:col-span-2">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <input type="text" name="programme" id="bulkProgramme" class="input-field" placeholder="Programme (shared for all)" required>
                        </div>
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" name="enrollment_date" id="bulkEnrollmentDate" class="input-field" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="input-group">
                            <div class="input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" name="expected_graduation_date" id="bulkExpectedGraduation" class="input-field">
                        </div>
                        <div class="input-group md:col-span-2">
                            <div class="input-icon" style="top:1.2rem; transform:none;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <textarea name="notes" id="bulkNotes" rows="2" class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white/60 dark:bg-gray-800/60 text-current" placeholder="Optional notes"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" onclick="closeBulkEnrollModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="bulkSubmitBtn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Enroll Selected (0)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ---- Bulk Modal functions ----
        function openBulkEnrollModal() {
            document.getElementById('bulkEnrollModal').classList.add('open');
            document.body.style.overflow = 'hidden';
            updateBulkSelectedCount();
        }

        function closeBulkEnrollModal() {
            document.getElementById('bulkEnrollModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        // ---- Bulk modal filters & selection ----
        const bulkSearch = document.getElementById('bulkSearch');
        const bulkProgrammeFilter = document.getElementById('bulkProgrammeFilter');
        const bulkStatusFilter = document.getElementById('bulkStatusFilter');
        const bulkRows = document.querySelectorAll('.bulk-student-row');
        const bulkCheckboxes = document.querySelectorAll('.bulk-student-checkbox:not([disabled])');
        const bulkSelectAll = document.getElementById('bulkSelectAll');
        const bulkSubmitBtn = document.getElementById('bulkSubmitBtn');

        function updateBulkFilters() {
            const search = bulkSearch.value.toLowerCase().trim();
            const programme = bulkProgrammeFilter.value;
            const status = bulkStatusFilter.value;

            let visibleCount = 0;
            bulkRows.forEach(row => {
                const name = row.dataset.name;
                const reg = row.dataset.reg;
                const rowProgramme = row.dataset.programme;
                const rowStatus = row.dataset.status;

                let show = true;
                if (search && !name.includes(search) && !reg.includes(search)) show = false;
                if (programme && rowProgramme !== programme) show = false;
                if (status && rowStatus !== status) show = false;

                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            document.getElementById('bulkFilteredCount').textContent = `${visibleCount} visible`;
            updateBulkSelectAllState();
        }

        function updateBulkSelectAllState() {
            const visibleCheckboxes = document.querySelectorAll('.bulk-student-row:not([style*="display: none"]) .bulk-student-checkbox:not([disabled])');
            const checked = document.querySelectorAll('.bulk-student-row:not([style*="display: none"]) .bulk-student-checkbox:not([disabled]):checked');
            if (visibleCheckboxes.length === 0) {
                bulkSelectAll.checked = false;
                bulkSelectAll.indeterminate = false;
            } else if (checked.length === visibleCheckboxes.length) {
                bulkSelectAll.checked = true;
                bulkSelectAll.indeterminate = false;
            } else if (checked.length > 0) {
                bulkSelectAll.checked = false;
                bulkSelectAll.indeterminate = true;
            } else {
                bulkSelectAll.checked = false;
                bulkSelectAll.indeterminate = false;
            }
            updateBulkSelectedCount();
        }

        function updateBulkSelectedCount() {
            const selected = document.querySelectorAll('.bulk-student-checkbox:checked');
            const count = selected.length;
            bulkSubmitBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Enroll Selected (${count})
            `;
        }

        bulkSelectAll.addEventListener('change', function() {
            const visibleCheckboxes = document.querySelectorAll('.bulk-student-row:not([style*="display: none"]) .bulk-student-checkbox:not([disabled])');
            visibleCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkSelectedCount();
        });

        bulkCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkSelectAllState);
        });

        bulkSearch.addEventListener('input', updateBulkFilters);
        bulkProgrammeFilter.addEventListener('change', updateBulkFilters);
        bulkStatusFilter.addEventListener('change', updateBulkFilters);

        // ---- Bulk form validation ----
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.bulk-student-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Please select at least one student.');
            }
        });

        // ---- Term filtering in bulk modal ----
        document.getElementById('bulkAcademicYear').addEventListener('change', function() {
            const yearId = this.value;
            const termSelect = document.getElementById('bulkTerm');
            const options = termSelect.querySelectorAll('option');
            let firstVisible = null;
            options.forEach(opt => {
                if (opt.value === '') {
                    opt.style.display = 'block';
                    return;
                }
                const optYear = opt.getAttribute('data-academic-year-id');
                if (!yearId || optYear === yearId) {
                    opt.style.display = 'block';
                    if (!firstVisible) firstVisible = opt.value;
                } else {
                    opt.style.display = 'none';
                }
            });
            if (firstVisible && !termSelect.value) termSelect.value = firstVisible;
        });

        // ---- Main page filters ----
        function resetFilters() {
            window.location.href = '{{ route("admin.enrollments.index") }}';
        }

        function exportEnrollments() {
            const form = document.getElementById('filterForm');
            const action = '{{ route("admin.enrollments.export") }}';
            const params = new URLSearchParams(new FormData(form)).toString();
            window.location.href = action + '?' + params;
        }

        // ---- Term filter for main page ----
        function filterTermsByAcademicYear() {
            const yearId = document.getElementById('academic_year_filter').value;
            const termSelect = document.getElementById('term_filter');
            const options = termSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (opt.value === '') return;
                const optYear = opt.getAttribute('data-academic-year-id');
                if (!yearId || optYear === yearId) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                    if (opt.selected) opt.selected = false;
                }
            });
        }

        document.getElementById('academic_year_filter').addEventListener('change', filterTermsByAcademicYear);

        // ---- Initialize ----
        document.addEventListener('DOMContentLoaded', function() {
            filterTermsByAcademicYear();
            updateBulkFilters();
        });

        // Click outside to close modal
        document.getElementById('bulkEnrollModal').addEventListener('click', function(e) {
            if (e.target === this) closeBulkEnrollModal();
        });
    </script>
    @endpush
@endsection
