@extends('layouts.admin')

@section('header')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-blue-500/10 text-blue-500 text-lg">
                    <i class="fas fa-chart-line"></i>
                </span>
                <h2 class="font-black text-2xl sm:text-3xl text-[var(--text-primary)] tracking-tight">
                    Institutional Intelligence & Reports
                </h2>
            </div>
            <p class="text-sm text-[var(--text-secondary)] mt-1 ml-11">
                Real-time academic telemetry, financial liquidity analysis, assessment analytics, and early-warning indicators.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Semester Selector -->
            <x-semester-selector 
                :selected="$selectedTermId" 
                :activeTerm="$activeTerm" 
                :isHistorical="$isHistorical" 
                :allTerms="$allTerms"
            />

            <!-- Print Executive Report -->
            <button onclick="window.print()"
                class="inline-flex items-center px-4 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-primary)] text-sm font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 hover:scale-105 transition-all shadow-sm">
                <i class="fas fa-print mr-2 text-xs text-blue-500"></i>
                Print Report
            </button>

            <!-- Export Hub Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-500 via-teal-600 to-green-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                    <i class="fas fa-file-export text-xs"></i>
                    Export Center
                    <i class="fas fa-chevron-down text-xs ml-0.5"></i>
                </button>

                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute right-0 mt-2 w-72 bg-[var(--bg-card-solid)] border border-[var(--border-color)] rounded-2xl shadow-2xl z-50 py-2 text-sm backdrop-blur-xl">
                    <div class="px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] border-b border-[var(--border-color)]">
                        Standard CSV Data Downloads
                    </div>
                    <a href="{{ route('admin.reports.export.results') }}"
                        class="w-full text-left px-4 py-3 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 text-[var(--text-primary)] flex items-center justify-between border-b border-[var(--border-color)]">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base text-blue-500"><i class="fas fa-graduation-cap"></i></span>
                            <div>
                                <p class="font-semibold text-xs text-[var(--text-primary)]">Academic Results Report</p>
                                <p class="text-[10px] text-[var(--text-muted)]">Scores, marks, grades, and terms</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-xs text-[var(--text-muted)]"></i>
                    </a>
                    <a href="{{ route('admin.reports.export.fees') }}"
                        class="w-full text-left px-4 py-3 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 text-[var(--text-primary)] flex items-center justify-between border-b border-[var(--border-color)]">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base text-emerald-500"><i class="fas fa-receipt"></i></span>
                            <div>
                                <p class="font-semibold text-xs text-[var(--text-primary)]">Fee Statements & Revenue</p>
                                <p class="text-[10px] text-[var(--text-muted)]">Invoices, collections, and overdue</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-xs text-[var(--text-muted)]"></i>
                    </a>
                    <a href="{{ route('admin.reports.export.students') }}"
                        class="w-full text-left px-4 py-3 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 text-[var(--text-primary)] flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base text-purple-500"><i class="fas fa-users"></i></span>
                            <div>
                                <p class="font-semibold text-xs text-[var(--text-primary)]">Student Registry Census</p>
                                <p class="text-[10px] text-[var(--text-muted)]">Demographics, GPAs, and balances</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-xs text-[var(--text-muted)]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
        // Core Statistics Calculations (scoped to selected/active semester)
        $totalStudents = $totalStudents ?? \App\Models\Student::count();
        $totalResults = $totalResults ?? \App\Models\Result::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->count();
        $totalFeesAll = $totalFees ?? \App\Models\Fee::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->sum('amount');
        $totalPaidAll = $totalPaid ?? \App\Models\Fee::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->sum('paid');
        $totalBalanceAll = max(0, $totalFeesAll - $totalPaidAll);
        $collectionRate = $totalFeesAll > 0 ? round(($totalPaidAll / $totalFeesAll) * 100, 1) : 0;
        
        $passedResults = \App\Models\Result::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->where('marks', '>=', 40)->count();
        $passRate = $totalResults > 0 ? round(($passedResults / $totalResults) * 100, 1) : 0;
        $avgInstituteScore = \App\Models\Result::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->avg('marks') ?? 0;
        
        $exam1Results = \App\Models\Result::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->where('exam_type', 'exam1')->get();
        $exam2Results = \App\Models\Result::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->where('exam_type', 'exam2')->get();
    @endphp

    @if($isHistorical)
        <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-4 font-mono text-xs shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-sm"></i>
                </div>
                <div>
                    <span class="font-bold uppercase tracking-wider block">HISTORICAL AUDIT REPORT</span>
                    <span>Displaying institutional telemetry for: <strong>{{ $selectedYear?->year_name }} &bull; {{ $selectedTerm?->term_name }}</strong>. All grade distributions, financials, and passing rates reflect this historical semester.</span>
                </div>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 text-zinc-950 font-bold hover:scale-105 transition flex-shrink-0">
                Switch to Active Semester &rarr;
            </a>
        </div>
    @endif

    @php
        $distinctionsCount = \App\Models\Result::when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))->where('marks', '>=', 70)->count();
        $distinctionRate = $totalResults > 0 ? round(($distinctionsCount / $totalResults) * 100, 1) : 0;

        // Quizzes Analytics
        $totalQuizzes = \App\Models\Quiz::count();
        $totalQuizAttempts = \App\Models\QuizAttempt::count();
        $passedQuizAttempts = \App\Models\QuizAttempt::where('is_passed', true)->count();
        $quizPassRate = $totalQuizAttempts > 0 ? round(($passedQuizAttempts / $totalQuizAttempts) * 100, 1) : 0;
        $avgQuizScore = \App\Models\QuizAttempt::avg('score') ?? 0;

        // Grade Distribution Tiers
        $gradeCounts = [
            'A' => ['label' => 'Distinction', 'range' => '70 - 100%', 'count' => \App\Models\Result::where('marks', '>=', 70)->count(), 'color' => 'from-emerald-500 to-green-600', 'badge' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20'],
            'B' => ['label' => 'Merit', 'range' => '60 - 69%', 'count' => \App\Models\Result::whereBetween('marks', [60, 69.99])->count(), 'color' => 'from-blue-500 to-indigo-600', 'badge' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20'],
            'C' => ['label' => 'Credit', 'range' => '50 - 59%', 'count' => \App\Models\Result::whereBetween('marks', [50, 59.99])->count(), 'color' => 'from-amber-500 to-yellow-600', 'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'],
            'D' => ['label' => 'Pass', 'range' => '40 - 49%', 'count' => \App\Models\Result::whereBetween('marks', [40, 49.99])->count(), 'color' => 'from-orange-500 to-orange-600', 'badge' => 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20'],
            'F' => ['label' => 'Fail', 'range' => '< 40%', 'count' => \App\Models\Result::where('marks', '<', 40)->count(), 'color' => 'from-red-500 to-rose-600', 'badge' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20'],
        ];

        // Programme Performance Intelligence
        $programmes = \App\Models\Student::distinct()->pluck('programme')->filter()->values()->toArray();
        if (empty($programmes)) {
            $programmes = ['Computer Science', 'Information Technology', 'Software Engineering', 'Data Science'];
        }

        $programmeData = [];
        foreach ($programmes as $prog) {
            $studIds = \App\Models\Student::where('programme', $prog)->pluck('id');
            $res = \App\Models\Result::whereIn('student_id', $studIds)->get();
            $avgMarks = $res->avg('marks') ?? 0;
            $passCount = $res->where('marks', '>=', 40)->count();
            $distCount = $res->where('marks', '>=', 70)->count();
            $failCount = $res->where('marks', '<', 40)->count();
            $progPassRate = $res->count() > 0 ? round(($passCount / $res->count()) * 100, 1) : 0;
            $progDistRate = $res->count() > 0 ? round(($distCount / $res->count()) * 100, 1) : 0;

            $programmeData[] = [
                'name' => $prog,
                'students' => count($studIds),
                'avg_score' => round($avgMarks, 1),
                'pass_rate' => $progPassRate,
                'distinction_rate' => $progDistRate,
                'fail_count' => $failCount,
                'results_count' => $res->count(),
            ];
        }
        usort($programmeData, function($a, $b) { return $b['avg_score'] <=> $a['avg_score']; });

        // Subject Analytics Heatmap
        $subjectsData = \App\Models\Subject::with('results')->get()->map(function($sub) {
            $avg = $sub->results->avg('marks') ?? 0;
            $passed = $sub->results->where('marks', '>=', 40)->count();
            $dist = $sub->results->where('marks', '>=', 70)->count();
            $rate = $sub->results->count() > 0 ? round(($passed / $sub->results->count()) * 100, 1) : 0;
            $maxMark = $sub->results->max('marks') ?? 0;
            $minMark = $sub->results->min('marks') ?? 0;
            return [
                'name' => $sub->name,
                'code' => $sub->code,
                'avg' => round($avg, 1),
                'pass_rate' => $rate,
                'distinctions' => $dist,
                'max' => $maxMark,
                'min' => $minMark,
                'total_students' => $sub->results->count(),
            ];
        })->sortByDesc('avg');

        // Revenue Breakdown by Fee Type
        $feeTypes = ['tuition', 'registration', 'library', 'laboratory', 'sports', 'other'];
        $feeTypeBreakdown = [];
        foreach ($feeTypes as $type) {
            $invoiced = \App\Models\Fee::where('type', $type)->sum('amount');
            $collected = \App\Models\Fee::where('type', $type)->sum('paid');
            $rate = $invoiced > 0 ? round(($collected / $invoiced) * 100, 1) : 0;
            if ($invoiced > 0) {
                $feeTypeBreakdown[] = [
                    'type' => ucfirst($type),
                    'invoiced' => $invoiced,
                    'collected' => $collected,
                    'balance' => max(0, $invoiced - $collected),
                    'rate' => $rate,
                ];
            }
        }

        // Top 8 Academic Champions
        $topStudents = \App\Models\Student::with(['user', 'results'])->get()->map(function ($s) {
            $s->avg_score = $s->results->avg('marks') ?? 0;
            $s->total_exams = $s->results->count();
            $s->distinctions = $s->results->where('marks', '>=', 70)->count();
            return $s;
        })->where('avg_score', '>', 0)->sortByDesc('avg_score')->take(8);

        // At-Risk Students Radar (Scoring <40% or multiple failed exams)
        $atRiskStudents = \App\Models\Student::with(['user', 'results'])->get()->map(function ($s) {
            $s->avg_score = $s->results->avg('marks') ?? 0;
            $s->failed_exams = $s->results->where('marks', '<', 40)->count();
            $s->total_exams = $s->results->count();
            return $s;
        })->filter(function($s) {
            return ($s->total_exams > 0 && ($s->avg_score < 45 || $s->failed_exams > 0));
        })->sortBy('avg_score')->take(6);

        // Overdue Fees Aging Ledger
        $overdueFees = \App\Models\Fee::with(['student.user'])
            ->whereRaw('paid < amount')
            ->where('due_date', '<', now())
            ->get()
            ->groupBy('student_id')
            ->map(function ($fees) {
                return [
                    'student' => $fees->first()->student,
                    'total' => $fees->sum('amount') - $fees->sum('paid'),
                    'count' => $fees->count(),
                    'last_due' => $fees->min('due_date'),
                ];
            })->sortByDesc('total')->take(8);

        // 6-Month Fee Cash Flow Trend
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyPaid = \App\Models\Fee::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('paid');
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'short' => $month->format('M'),
                'collected' => $monthlyPaid,
            ];
        }
        $maxMonthlyCollected = max(array_column($monthlyData, 'collected')) ?: 1;

        // Recent Institutional Activity
        $recentResults = \App\Models\Result::with(['student.user', 'subject'])->latest()->take(6)->get();
        $recentQuizAttempts = \App\Models\QuizAttempt::with(['student.user', 'quiz'])->latest()->take(6)->get();
    @endphp

    <style>
        .reports-container {
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 2.5rem;
            box-shadow: 0 25px 80px var(--shadow-color);
            overflow: hidden;
            transition: background 0.4s, border-color 0.4s, box-shadow 0.4s;
            padding: 2rem 2.5rem;
            position: relative;
        }

        .reports-container::before {
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
            .reports-container { padding: 1.5rem; border-radius: 2rem; }
            .reports-container::before { display: none; }
        }

        .bento-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .bento-card {
            background: var(--bg-card);
            backdrop-filter: blur(4px);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 1.35rem 1.25rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .bento-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px var(--shadow-color);
        }
        .bento-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        }

        .glass-panel {
            background: var(--bg-card);
            backdrop-filter: blur(4px);
            border: 1px solid var(--border-color);
            border-radius: 1.75rem;
            overflow: hidden;
            padding: 1.5rem;
        }

        .nav-tab-btn {
            padding: 0.75rem 1.25rem;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 1rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            border: 1px solid transparent;
            cursor: pointer;
            white-space: nowrap;
        }
        .nav-tab-btn:hover {
            color: var(--text-primary);
            background: var(--glass-bg);
        }
        .nav-tab-btn.active {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.35);
        }

        @media print {
            body { background: white !important; color: black !important; }
            .reports-container { box-shadow: none !important; border: none !important; padding: 0 !important; }
            .reports-container::before, button, a[href*="export"], select, .no-print { display: none !important; }
            .tab-pane { display: block !important; }
        }
    </style>

    <div class="reports-container space-y-8">
        <!-- ========== 1. TOP BENTO KPI METRICS MATRIX ========== -->
        <div>
            <div class="bento-grid">
                <!-- Students Card -->
                <div class="bento-card">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Total Enrollment</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-xs">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-[var(--text-primary)] mt-2">{{ number_format($totalStudents) }}</p>
                    <div class="flex items-center justify-between text-xs text-[var(--text-muted)] mt-2 pt-2 border-t border-[var(--border-color)]">
                        <span>{{ count($programmes) }} Programmes</span>
                        <span class="text-emerald-500 font-bold"><i class="fas fa-check-circle mr-1"></i>Active</span>
                    </div>
                </div>

                <!-- Academic Pass Rate -->
                <div class="bento-card">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Overall Pass Rate</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xs">
                            <i class="fas fa-award"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $passRate }}%</p>
                    <div class="flex items-center justify-between text-xs text-[var(--text-muted)] mt-2 pt-2 border-t border-[var(--border-color)]">
                        <span>Avg: <strong>{{ round($avgInstituteScore, 1) }}%</strong></span>
                        <span class="text-blue-500 font-bold">{{ $distinctionRate }}% Distinctions</span>
                    </div>
                </div>

                <!-- Fees Recovery -->
                <div class="bento-card">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Fee Recovery Ratio</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center text-xs">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-purple-600 dark:text-purple-400 mt-2">{{ $collectionRate }}%</p>
                    <div class="flex items-center justify-between text-xs text-[var(--text-muted)] mt-2 pt-2 border-t border-[var(--border-color)]">
                        <span>Collected: MK {{ number_format($totalPaidAll, 0) }}</span>
                        <span class="text-amber-500 font-bold">Bal: MK {{ number_format($totalBalanceAll, 0) }}</span>
                    </div>
                </div>

                <!-- Assessments Mastery -->
                <div class="bento-card">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Quiz Mastery</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-xs">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-2">{{ $quizPassRate }}%</p>
                    <div class="flex items-center justify-between text-xs text-[var(--text-muted)] mt-2 pt-2 border-t border-[var(--border-color)]">
                        <span>{{ $totalQuizAttempts }} Submissions</span>
                        <span class="text-blue-500 font-bold">Avg: {{ round($avgQuizScore, 1) }} pts</span>
                    </div>
                </div>

                <!-- At-Risk / Support Needed -->
                <div class="bento-card">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">At-Risk Students</span>
                        <div class="w-8 h-8 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center text-xs">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-red-500 mt-2">{{ count($atRiskStudents) }}</p>
                    <div class="flex items-center justify-between text-xs text-[var(--text-muted)] mt-2 pt-2 border-t border-[var(--border-color)]">
                        <span>Scoring &lt;45% or failing</span>
                        <span class="text-red-500 font-bold">Needs Support</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== 2. INTERACTIVE SUITE NAVIGATION TABS ========== -->
        <div class="no-print border-b border-[var(--border-color)] pb-3">
            <div class="flex items-center gap-2 overflow-x-auto py-1">
                <button type="button" onclick="switchReportTab('academic')" id="tab-academic" class="nav-tab-btn active">
                    <i class="fas fa-graduation-cap"></i>
                    Academic Intelligence
                </button>
                <button type="button" onclick="switchReportTab('financial')" id="tab-financial" class="nav-tab-btn">
                    <i class="fas fa-hand-holding-usd"></i>
                    Financial & Revenue
                </button>
                <button type="button" onclick="switchReportTab('quizzes')" id="tab-quizzes" class="nav-tab-btn">
                    <i class="fas fa-question-circle"></i>
                    Quiz & Assessments
                </button>
                <button type="button" onclick="switchReportTab('champions')" id="tab-champions" class="nav-tab-btn">
                    <i class="fas fa-crown"></i>
                    Champions & At-Risk Radar
                </button>
                <button type="button" onclick="switchReportTab('activity')" id="tab-activity" class="nav-tab-btn">
                    <i class="fas fa-stream"></i>
                    Live Institutional Stream
                </button>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 1: ACADEMIC PERFORMANCE INTELLIGENCE                                   -->
        <!-- ========================================================================= -->
        <div id="pane-academic" class="tab-pane space-y-6">
            <!-- Visual Grade Tiers & Programme Matrix -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Grade Distribution Tiers -->
                <div class="lg:col-span-5 glass-panel space-y-4">
                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <div>
                            <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                                <i class="fas fa-chart-bar text-blue-500"></i>
                                Grade Mastery Tiers
                            </h4>
                            <p class="text-[11px] text-[var(--text-muted)]">Distribution across all recorded assessments</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-500 border border-blue-500/20">
                            {{ $totalResults }} Marks
                        </span>
                    </div>

                    <div class="space-y-4 pt-1">
                        @foreach(['A', 'B', 'C', 'D', 'F'] as $tier)
                            @php
                                $info = $gradeCounts[$tier];
                                $pct = $totalResults > 0 ? round(($info['count'] / $totalResults) * 100, 1) : 0;
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-black border {{ $info['badge'] }}">
                                            {{ $tier }}
                                        </span>
                                        <span class="text-[var(--text-primary)]">{{ $info['label'] }} ({{ $info['range'] }})</span>
                                    </div>
                                    <span class="text-[var(--text-secondary)] font-mono">{{ $info['count'] }} &bull; {{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700/60 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r {{ $info['color'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Programme Benchmarking Table -->
                <div class="lg:col-span-7 glass-panel space-y-4">
                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <div>
                            <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                                <i class="fas fa-layer-group text-purple-500"></i>
                                Programme Academic Comparison Matrix
                            </h4>
                            <p class="text-[11px] text-[var(--text-muted)]">Benchmark GPA, pass rates & distinction percentages</p>
                        </div>
                        <span class="text-xs text-[var(--text-muted)] font-mono">Ranked by GPA</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] border-b border-[var(--border-color)]">
                                    <th class="pb-3">Programme</th>
                                    <th class="pb-3">Students</th>
                                    <th class="pb-3">Avg Mark</th>
                                    <th class="pb-3">Pass Rate</th>
                                    <th class="pb-3 text-right">Distinctions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border-color)] text-xs">
                                @foreach($programmeData as $p)
                                    <tr class="hover:bg-blue-50/20 dark:hover:bg-blue-900/10 transition">
                                        <td class="py-3 font-bold text-[var(--text-primary)]">{{ $p['name'] }}</td>
                                        <td class="py-3 text-[var(--text-secondary)] font-mono">{{ $p['students'] }}</td>
                                        <td class="py-3 font-bold {{ $p['avg_score'] >= 70 ? 'text-emerald-500' : ($p['avg_score'] >= 50 ? 'text-blue-500' : 'text-amber-500') }}">
                                            {{ $p['avg_score'] }}%
                                        </td>
                                        <td class="py-3">
                                            <span class="px-2.5 py-0.5 font-bold rounded-full {{ $p['pass_rate'] >= 70 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                                {{ $p['pass_rate'] }}%
                                            </span>
                                        </td>
                                        <td class="py-3 text-right font-bold text-purple-500">
                                            {{ $p['distinction_rate'] }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Subject Performance Heatmap -->
            <div class="glass-panel space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-book text-blue-500"></i>
                            Course Performance & Difficulty Heatmap
                        </h4>
                        <p class="text-[11px] text-[var(--text-muted)]">Subject scoring averages, student enrollment volume, and max/min score range</p>
                    </div>
                    <span class="text-xs text-[var(--text-muted)] font-mono">{{ count($subjectsData) }} Active Courses</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] border-b border-[var(--border-color)]">
                                <th class="pb-3">Course Code & Title</th>
                                <th class="pb-3">Total Assessed</th>
                                <th class="pb-3">Average Score</th>
                                <th class="pb-3">Score Range</th>
                                <th class="pb-3">Pass Rate</th>
                                <th class="pb-3 text-right">Academic Health</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)] text-xs">
                            @forelse($subjectsData as $sub)
                                <tr class="hover:bg-blue-50/20 dark:hover:bg-blue-900/10 transition">
                                    <td class="py-3">
                                        <div class="font-bold text-sm text-[var(--text-primary)]">{{ $sub['name'] }}</div>
                                        <div class="text-[11px] text-[var(--text-muted)] font-mono">{{ $sub['code'] }}</div>
                                    </td>
                                    <td class="py-3 text-[var(--text-secondary)] font-mono">{{ $sub['total_students'] }} students</td>
                                    <td class="py-3 font-bold text-sm {{ $sub['avg'] >= 70 ? 'text-emerald-500' : ($sub['avg'] >= 50 ? 'text-blue-500' : 'text-amber-500') }}">
                                        {{ $sub['avg'] }}%
                                    </td>
                                    <td class="py-3 text-[var(--text-muted)] font-mono">
                                        {{ $sub['min'] }}% &mdash; {{ $sub['max'] }}%
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-1.5 rounded-full" style="width: {{ $sub['pass_rate'] }}%"></div>
                                            </div>
                                            <span class="font-bold text-[11px] text-[var(--text-primary)]">{{ $sub['pass_rate'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-right">
                                        @if($sub['pass_rate'] >= 75)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                <i class="fas fa-check-circle mr-1"></i> Optimal
                                            </span>
                                        @elseif($sub['pass_rate'] >= 50)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-500 border border-blue-500/20">
                                                <i class="fas fa-info-circle mr-1"></i> Satisfactory
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-500 border border-red-500/20">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Attention
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-[var(--text-muted)]">No subject assessment results recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: FINANCIAL & REVENUE INTELLIGENCE                                    -->
        <!-- ========================================================================= -->
        <div id="pane-financial" class="tab-pane hidden space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- 6-Month Inflow Bar Chart -->
                <div class="lg:col-span-6 glass-panel space-y-4">
                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <div>
                            <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-emerald-500"></i>
                                Monthly Cash Collection Trajectory
                            </h4>
                            <p class="text-[11px] text-[var(--text-muted)]">Actual payments cleared across past 6 months</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500">
                            Cash Inflow
                        </span>
                    </div>

                    <div class="space-y-4 pt-1">
                        @foreach($monthlyData as $mon)
                            @php
                                $pctFlow = round(($mon['collected'] / $maxMonthlyCollected) * 100, 1);
                            @endphp
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs font-bold">
                                    <span class="text-[var(--text-primary)]">{{ $mon['month'] }}</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono">MK {{ number_format($mon['collected'], 0) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700/60 rounded-full h-3 overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-3 rounded-full transition-all duration-500" style="width: {{ $pctFlow }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Fee Category Breakdown -->
                <div class="lg:col-span-6 glass-panel space-y-4">
                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <div>
                            <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                                <i class="fas fa-pie-chart text-purple-500"></i>
                                Revenue Recovery by Category
                            </h4>
                            <p class="text-[11px] text-[var(--text-muted)]">Invoiced charges versus collected funds</p>
                        </div>
                        <span class="text-xs text-[var(--text-muted)] font-mono">Institutional Revenue</span>
                    </div>

                    <div class="space-y-3.5 pt-1">
                        @forelse($feeTypeBreakdown as $fb)
                            <div class="p-3.5 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-[var(--text-primary)]">{{ $fb['type'] }}</span>
                                    <span class="text-purple-600 dark:text-purple-400">{{ $fb['rate'] }}% Recovery</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700/60 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 h-2 rounded-full" style="width: {{ min($fb['rate'], 100) }}%"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-[var(--text-muted)] font-mono pt-1">
                                    <span>Invoiced: MK {{ number_format($fb['invoiced'], 0) }}</span>
                                    <span class="text-emerald-500 font-bold">Paid: MK {{ number_format($fb['collected'], 0) }}</span>
                                    <span class="text-red-500">Bal: MK {{ number_format($fb['balance'], 0) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-center py-6 text-[var(--text-muted)]">No fee categorization records found.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Overdue Accounts Aging Table -->
            <div class="glass-panel space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-red-500"></i>
                            Critical Overdue Accounts Aging Ledger
                        </h4>
                        <p class="text-[11px] text-[var(--text-muted)]">Students with past-due balances requiring collection intervention</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-red-500/10 text-red-500">
                        {{ count($overdueFees) }} Overdue Accounts
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] border-b border-[var(--border-color)]">
                                <th class="pb-3">Student Name</th>
                                <th class="pb-3">Registration & Programme</th>
                                <th class="pb-3">Overdue Invoices</th>
                                <th class="pb-3">Outstanding Balance</th>
                                <th class="pb-3">Initial Due Date</th>
                                <th class="pb-3 text-right">Intervention Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)] text-xs">
                            @forelse($overdueFees as $item)
                                <tr class="hover:bg-red-50/10 transition">
                                    <td class="py-3 font-bold text-[var(--text-primary)]">{{ $item['student']->user->name ?? 'Student' }}</td>
                                    <td class="py-3 text-[var(--text-secondary)]">{{ $item['student']->reg_number }} &bull; {{ $item['student']->programme ?? 'N/A' }}</td>
                                    <td class="py-3 font-mono text-[var(--text-secondary)]">{{ $item['count'] }} item(s)</td>
                                    <td class="py-3 font-bold text-red-500 font-mono">MK {{ number_format($item['total'], 0) }}</td>
                                    <td class="py-3 text-[var(--text-muted)]">{{ $item['last_due'] ? $item['last_due']->format('M d, Y') : 'Overdue' }}</td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('admin.messages.create', ['student_id' => $item['student']->id]) }}"
                                            class="inline-flex items-center px-3 py-1 bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white rounded-lg text-xs font-bold transition">
                                            <i class="fas fa-paper-plane mr-1 text-[10px]"></i> Send Reminder
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-emerald-500 text-xs font-bold">
                                        <i class="fas fa-check-circle mr-1"></i> No outstanding overdue fees at this time.
                                    </td>
                                </tr>
                            @endforelse
            <!-- Source of Funding Financial Ledger -->
            <div class="glass-panel space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-hand-holding-usd text-blue-500"></i>
                            Source of Funding Financial Ledger & Recovery Analysis
                        </h4>
                        <p class="text-[11px] text-[var(--text-muted)]">Institutional fee distribution, collection volume, and outstanding balances by sponsorship category</p>
                    </div>
                    <span class="text-xs font-mono text-[var(--text-muted)]">Semester Scoped</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] border-b border-[var(--border-color)]">
                                <th class="pb-3">Source of Funding</th>
                                <th class="pb-3">Students</th>
                                <th class="pb-3">Expected Fees</th>
                                <th class="pb-3">Amount Paid</th>
                                <th class="pb-3">Outstanding</th>
                                <th class="pb-3 text-right">Collection Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)] text-xs">
                            @forelse($fundingSourceReport ?? [] as $fsr)
                                <tr class="hover:bg-blue-50/20 dark:hover:bg-blue-900/10 transition">
                                    <td class="py-3 font-bold text-[var(--text-primary)] flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        <span>{{ $fsr['source'] }}</span>
                                    </td>
                                    <td class="py-3 text-[var(--text-secondary)] font-mono">{{ $fsr['students'] }}</td>
                                    <td class="py-3 font-bold font-mono text-[var(--text-primary)]">MK {{ number_format($fsr['expected_fees'], 0) }}</td>
                                    <td class="py-3 font-bold font-mono text-emerald-600 dark:text-emerald-400">MK {{ number_format($fsr['paid'], 0) }}</td>
                                    <td class="py-3 font-bold font-mono {{ $fsr['outstanding'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-[var(--text-muted)]' }}">
                                        MK {{ number_format($fsr['outstanding'], 0) }}
                                    </td>
                                    <td class="py-3 text-right font-mono font-bold">
                                        <span class="px-2.5 py-0.5 rounded-full {{ $fsr['collection_rate'] >= 80 ? 'bg-emerald-500/10 text-emerald-500' : ($fsr['collection_rate'] >= 50 ? 'bg-amber-500/10 text-amber-500' : 'bg-rose-500/10 text-rose-500') }}">
                                            {{ $fsr['collection_rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-[var(--text-muted)] text-xs">
                                        No funding source records found for this semester.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 3: QUIZ & ASSESSMENT TELEMETRY                                         -->
        <!-- ========================================================================= -->
        <div id="pane-quizzes" class="tab-pane hidden space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Quiz Overview KPI Bento -->
                <div class="glass-panel space-y-4 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-2xl mx-auto shadow-sm">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-[var(--text-primary)]">Quiz Completion Velocity</h4>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">Online test submissions summary</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] grid grid-cols-2 gap-3 text-left">
                        <div>
                            <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase">Active Quizzes</span>
                            <p class="text-lg font-black text-[var(--text-primary)]">{{ $totalQuizzes }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase">Total Submissions</span>
                            <p class="text-lg font-black text-blue-500">{{ $totalQuizAttempts }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase">Passed Attempts</span>
                            <p class="text-lg font-black text-emerald-500">{{ $passedQuizAttempts }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase">Average Score</span>
                            <p class="text-lg font-black text-amber-500">{{ round($avgQuizScore, 1) }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Submissions Feed -->
                <div class="lg:col-span-2 glass-panel space-y-4">
                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <div>
                            <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                                <i class="fas fa-history text-amber-500"></i>
                                Recent Quiz Submissions Feed
                            </h4>
                            <p class="text-[11px] text-[var(--text-muted)]">Live student assessment results</p>
                        </div>
                        <span class="text-xs text-[var(--text-muted)] font-mono">Latest Attempts</span>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($recentQuizAttempts as $qa)
                            <div class="p-3 rounded-xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold">
                                        <i class="fas fa-check-double text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-[var(--text-primary)]">{{ $qa->student->user->name ?? 'Student' }}</p>
                                        <p class="text-[11px] text-[var(--text-muted)]">{{ $qa->quiz->title ?? 'Quiz' }} &bull; {{ $qa->created_at ? $qa->created_at->diffForHumans() : '' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2.5">
                                    <span class="font-bold text-sm {{ $qa->is_passed ? 'text-emerald-500' : 'text-red-500' }}">
                                        {{ $qa->score }}%
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $qa->is_passed ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                                        {{ $qa->is_passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-center py-6 text-[var(--text-muted)]">No quiz attempts submitted yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 4: HONOR ROLL & AT-RISK RADAR                                          -->
        <!-- ========================================================================= -->
        <div id="pane-champions" class="tab-pane hidden space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Academic Champions Honor Roll -->
                <div class="glass-panel space-y-4">
                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <div>
                            <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                                <i class="fas fa-crown text-amber-500"></i>
                                Academic Champions Honor Roll
                            </h4>
                            <p class="text-[11px] text-[var(--text-muted)]">Highest cumulative examination GPA</p>
                        </div>
                        <span class="text-xs text-amber-500 font-bold"><i class="fas fa-star mr-1"></i>Dean's List</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($topStudents as $idx => $s)
                            @php
                                $badgeClass = match($idx) {
                                    0 => 'bg-amber-500 text-white shadow-amber-500/40 shadow-md',
                                    1 => 'bg-slate-400 text-white shadow-sm',
                                    2 => 'bg-amber-700 text-white shadow-sm',
                                    default => 'bg-blue-500/10 text-blue-500 font-bold border border-blue-500/20'
                                };
                            @endphp
                            <div class="p-3.5 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] flex items-center justify-between hover:scale-[1.01] transition-transform">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black {{ $badgeClass }}">
                                        {{ $idx + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-[var(--text-primary)]">{{ $s->user->name ?? 'Student' }}</p>
                                        <p class="text-xs text-[var(--text-muted)]">{{ $s->reg_number }} &bull; {{ $s->programme ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-base font-black text-blue-500">{{ number_format($s->avg_score, 1) }}%</span>
                                    <p class="text-[10px] text-emerald-500 font-semibold">{{ $s->distinctions }} Distinctions</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-center py-6 text-[var(--text-muted)]">No exam results recorded yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- At-Risk Early Warning Radar -->
                <div class="glass-panel space-y-4">
                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <div>
                            <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                                <i class="fas fa-heartbeat text-red-500"></i>
                                Academic Early-Warning Radar
                            </h4>
                            <p class="text-[11px] text-[var(--text-muted)]">Students with sub-45% average or failed assessments</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-red-500/10 text-red-500">
                            Intervention Required
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($atRiskStudents as $ars)
                            <div class="p-3.5 rounded-2xl bg-[var(--bg-card-solid)] border border-red-500/20 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center text-xs font-bold">
                                        <i class="fas fa-exclamation"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-[var(--text-primary)]">{{ $ars->user->name ?? 'Student' }}</p>
                                        <p class="text-xs text-[var(--text-muted)]">{{ $ars->reg_number }} &bull; {{ $ars->failed_exams }} failed exam(s)</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-red-500">{{ number_format($ars->avg_score, 1) }}%</span>
                                        <p class="text-[10px] text-[var(--text-muted)]">GPA Average</p>
                                    </div>
                                    <a href="{{ route('admin.messages.create', ['student_id' => $ars->id]) }}"
                                        class="p-2 rounded-xl bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white transition shadow-sm text-xs font-bold"
                                        title="Send Academic Advisory Message">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-[var(--text-muted)] text-xs">
                                <i class="fas fa-check-circle text-emerald-500 text-lg mb-1 block"></i>
                                All active students are performing above target thresholds!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 5: LIVE INSTITUTIONAL ACTIVITY STREAM                                  -->
        <!-- ========================================================================= -->
        <div id="pane-activity" class="tab-pane hidden space-y-6">
            <div class="glass-panel space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-stream text-blue-500"></i>
                            Live Institutional Examination Stream
                        </h4>
                        <p class="text-[11px] text-[var(--text-muted)]">Real-time chronology of academic score entries and updates</p>
                    </div>
                    <span class="text-xs font-mono text-[var(--text-muted)]">Chronological feed</span>
                </div>

                <div class="divide-y divide-[var(--border-color)]">
                    @forelse($recentResults as $res)
                        <div class="py-3.5 flex items-center justify-between hover:bg-blue-50/10 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></div>
                                <div>
                                    <p class="font-bold text-sm text-[var(--text-primary)]">{{ $res->student->user->name ?? 'Student' }}</p>
                                    <p class="text-xs text-[var(--text-muted)]">{{ $res->subject->name ?? 'Course' }} &bull; {{ $res->created_at ? $res->created_at->diffForHumans() : '' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-sm {{ $res->marks >= 70 ? 'text-emerald-500' : ($res->marks >= 50 ? 'text-blue-500' : 'text-red-500') }}">
                                    {{ $res->marks }}% (Grade {{ $res->grade }})
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-center py-6 text-[var(--text-muted)]">No recent assessment recordings.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchReportTab(tabName) {
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.add('hidden'));
            document.querySelectorAll('.nav-tab-btn').forEach(btn => btn.classList.remove('active'));

            const targetPane = document.getElementById('pane-' + tabName);
            const targetBtn = document.getElementById('tab-' + tabName);

            if (targetPane) targetPane.classList.remove('hidden');
            if (targetBtn) targetBtn.classList.add('active');
        }
    </script>
    @endpush
@endsection
