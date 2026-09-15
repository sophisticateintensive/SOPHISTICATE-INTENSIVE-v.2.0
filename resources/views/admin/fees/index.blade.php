@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Fees & Tuition Management
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Track student invoices, settlements, balances and overdue payments</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-semester-selector 
                :selected="$selectedTermId" 
                :activeTerm="$activeTerm" 
                :isHistorical="$isHistorical" 
                :allTerms="$terms"
            />

            <a href="{{ route('admin.fees.create') }}"
                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
                <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New Fee
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $totalFeesSum = (int)($totalFeesAll ?? 0);
        $totalPaidSum = (int)($totalPaidAll ?? 0);
        $totalBalanceSum = (int)($totalBalanceAll ?? 0);
        $overdueCount = (int)($overdueCountAll ?? 0);
    @endphp

    <!-- ========== HISTORICAL BANNER NOTIFICATION ========== -->
    @if($isHistorical)
        <div class="mb-8 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-4 font-mono text-xs shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-sm"></i>
                </div>
                <div>
                    <span class="font-bold uppercase tracking-wider block">HISTORICAL ARCHIVE VIEW</span>
                    <span>Viewing fee statements for previous semester. Data is isolated to this specific session.</span>
                </div>
            </div>
            <a href="{{ route('admin.fees.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 text-zinc-950 font-bold hover:scale-105 transition flex-shrink-0">
                Switch to Active Semester &rarr;
            </a>
        </div>
    @endif

    <!-- ========== HERO / STATS SECTION (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 mb-8 shadow-2xl">
        <!-- Animated blobs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4"
             x-data="{ counters: { invoiced: 0, paid: 0, balance: 0, overdue: 0 }, init() {
                const targets = { invoiced: {{ $totalFeesSum }}, paid: {{ $totalPaidSum }}, balance: {{ $totalBalanceSum }}, overdue: {{ $overdueCount }} };
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
                    {{ $fees->total() }} fee records · {{ $paidCountAll }} fully cleared
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Tuition Financial Ledger</h1>
                <p class="text-blue-100/80 mt-1">Real-time revenue monitoring and student payment status</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-xl sm:text-2xl font-bold text-white">MK <span x-text="counters.invoiced.toLocaleString()"></span></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Invoiced</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-xl sm:text-2xl font-bold text-emerald-300">MK <span x-text="counters.paid.toLocaleString()"></span></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Collected</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-xl sm:text-2xl font-bold text-amber-300">MK <span x-text="counters.balance.toLocaleString()"></span></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Balance</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-rose-300" x-text="counters.overdue"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Overdue</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== SEARCH & FILTER (STUDENT MANAGEMENT DESIGN) ========== -->
    <form method="GET" action="{{ route('admin.fees.index') }}" id="filterForm" class="mb-8">
        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-[var(--border-color)] shadow-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                               placeholder="Search student name, registration number..."
                               class="w-full pl-10 pr-4 py-3 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                        @if(request('search'))
                            <a href="{{ route('admin.fees.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Source of Funding Filter -->
                <div class="relative">
                    <select name="funding_source" id="fundingSourceFilter"
                            class="w-full px-3 py-3 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm appearance-none cursor-pointer text-[var(--text-primary)] font-medium">
                        <option value="">All Funding Sources</option>
                        @foreach(\App\Models\Student::FUNDING_SOURCES as $src)
                            <option value="{{ $src }}" {{ request('funding_source') == $src ? 'selected' : '' }}>{{ $src }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Programme Filter -->
                <div class="relative">
                    <select name="programme" id="programmeFilter"
                            class="w-full px-3 py-3 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm appearance-none cursor-pointer text-[var(--text-primary)] font-medium">
                        <option value="">All Programmes</option>
                        @foreach($programmes ?? [] as $prog)
                            <option value="{{ $prog }}" {{ request('programme') == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="relative">
                    <select name="status" id="statusFilter"
                            class="w-full px-3 py-3 text-xs bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm appearance-none cursor-pointer text-[var(--text-primary)] font-medium">
                        <option value="">All Statuses</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial Balance</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-3 py-3 text-xs font-bold bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-filter mr-1.5"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.index') }}" class="px-3 py-3 text-xs bg-[var(--bg-card)] text-[var(--text-primary)] rounded-xl border border-[var(--border-color)] hover:bg-[var(--glass-bg)] transition flex items-center" title="Reset">
                        <i class="fas fa-undo"></i>
                    </a>
                    <a href="{{ route('admin.fees.export', request()->all()) }}" class="px-3 py-3 text-xs bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:shadow-lg transition flex items-center" title="Export">
                        <i class="fas fa-file-csv"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- ========== SOURCE OF FUNDING ANALYTICS BENTO ========== -->
    @if(!empty($fundingBreakdown) && count($fundingBreakdown) > 0)
        <div class="mb-8 bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-lg space-y-4">
            <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-sm">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[var(--text-primary)]">Source of Funding Revenue Telemetry</h3>
                        <p class="text-[11px] text-[var(--text-secondary)]">Revenue performance and collection breakdown by financial sponsorship</p>
                    </div>
                </div>
                <span class="text-[11px] font-mono text-[var(--text-muted)]">
                    {{ count($fundingBreakdown) }} Active Sponsorship Categories
                </span>
            </div>

            <!-- Bento Grid of Funding Sources -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($fundingBreakdown as $fb)
                    <div class="p-4 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] shadow-sm hover:border-blue-500/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-[var(--text-primary)] truncate" title="{{ $fb['source'] }}">{{ $fb['source'] }}</span>
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    {{ $fb['students_count'] }} std
                                </span>
                            </div>

                            <div class="space-y-1 font-mono text-[11px]">
                                <div class="flex items-center justify-between text-[var(--text-muted)]">
                                    <span>Expected:</span>
                                    <span class="font-bold text-[var(--text-primary)]">MK {{ number_format($fb['expected_fees'], 0) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                                    <span>Collected:</span>
                                    <span class="font-bold">MK {{ number_format($fb['paid_fees'], 0) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-rose-600 dark:text-rose-400">
                                    <span>Balance:</span>
                                    <span class="font-bold">MK {{ number_format($fb['balance'], 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-2 border-t border-[var(--border-color)]/60">
                            <div class="flex items-center justify-between text-[10px] font-mono mb-1">
                                <span class="text-[var(--text-muted)]">Collection</span>
                                <span class="font-bold {{ $fb['completion_rate'] >= 80 ? 'text-emerald-500' : ($fb['completion_rate'] >= 50 ? 'text-amber-500' : 'text-rose-500') }}">{{ $fb['completion_rate'] }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full" style="width: {{ min(100, $fb['completion_rate']) }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ========== FEES TABLE (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-wide">All Fee Invoices</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Manage student invoices, balances, and recorded receipts</p>
                </div>
            </div>
            <span class="text-xs text-white/80 bg-white/10 px-3 py-1 rounded-full font-mono">
                {{ $fees->total() }} Invoices
            </span>
        </div>

        <div class="p-6">
            @if($fees->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Funding Source</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Fee Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Academic Term</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Invoiced</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Paid</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)]">
                            @foreach($fees as $fee)
                                <tr class="hover:bg-[var(--accent-soft)] transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                {{ substr($fee->student->user->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.students.show', $fee->student) }}" class="text-sm font-bold text-[var(--text-primary)] hover:text-blue-600 transition">
                                                    {{ $fee->student->user->name ?? 'Unknown Student' }}
                                                </a>
                                                <p class="text-xs text-[var(--text-secondary)] font-mono">{{ $fee->student->reg_number ?? 'N/A' }} &bull; {{ $fee->student->programme ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            <i class="fas fa-hand-holding-usd mr-1 text-[10px]"></i>
                                            {{ $fee->student->display_funding_source ?? 'Not Specified' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300">
                                            {{ $fee->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                        {{ $fee->academicYear->year_name ?? 'N/A' }} &bull; {{ $fee->term->term_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold font-mono text-[var(--text-primary)]">
                                        MK {{ number_format($fee->amount, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold font-mono text-emerald-600 dark:text-emerald-400">
                                        MK {{ number_format($fee->paid, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold font-mono {{ $fee->balance > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-[var(--text-muted)]' }}">
                                        MK {{ number_format($fee->balance, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($fee->is_fully_paid)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                <i class="fas fa-check mr-1.5 text-[10px]"></i> Paid
                                            </span>
                                        @elseif($fee->is_overdue)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                                <i class="fas fa-clock mr-1.5 text-[10px]"></i> Overdue
                                            </span>
                                        @elseif($fee->paid > 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                                                <i class="fas fa-adjust mr-1.5 text-[10px]"></i> Partial
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                                <i class="fas fa-hourglass-start mr-1.5 text-[10px]"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.fees.show', $fee) }}" class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all" title="View & Payment">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.fees.edit', $fee) }}" class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.fees.destroy', $fee) }}" method="POST" id="delete-fee-{{ $fee->id }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="openDeleteModal(
                                                    'Delete Fee Record?',
                                                    'You are about to delete the {{ $fee->type }} fee for {{ addslashes($fee->student->user->name ?? 'Student') }} (Amount: MK {{ number_format($fee->amount, 0) }}). This action cannot be undone.',
                                                    document.getElementById('delete-fee-{{ $fee->id }}')
                                                )" class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-all" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $fees->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="bg-[var(--glass-bg)] rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center border border-[var(--border-color)]">
                        <svg class="w-12 h-12 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Fee Records Found</h4>
                    <p class="text-[var(--text-secondary)] mb-4">Get started by creating your first fee invoice</p>
                    <a href="{{ route('admin.fees.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add First Fee
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
