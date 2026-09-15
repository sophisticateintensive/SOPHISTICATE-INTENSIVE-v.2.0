@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Executive Dashboard
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Institutional overview, operations and real-time telemetry</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-semester-selector 
                :selected="$selectedTermId" 
                :activeTerm="$activeTerm" 
                :isHistorical="$isHistorical" 
                :allTerms="$allTerms"
            />

            <a href="{{ route('admin.students.create') }}"
               class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
                <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Student
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $totalStudentsCount = $totalStudents ?? \App\Models\Student::count();
        $totalSubjectsCount = $totalSubjects ?? \App\Models\Subject::count();
        $totalResultsCount = $totalResults ?? \App\Models\Result::count();
        $collectionRate = $totalFeesInvoiced > 0 ? round(($totalFeesCollected / $totalFeesInvoiced) * 100, 1) : 0;
        
        $upcomingDeadlines = \App\Models\Fee::with('student.user')
            ->when($selectedTermId, fn($q) => $q->where('term_id', $selectedTermId))
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(14))
            ->whereRaw('paid < amount')
            ->orderBy('due_date')
            ->take(4)
            ->get();
            
        $recentStudents = \App\Models\Student::with('user')->latest()->take(3)->get();
    @endphp

    <!-- ========== HISTORICAL NOTIFICATION BANNER ========== -->
    @if($isHistorical)
        <div class="mb-8 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-4 font-mono text-xs shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-sm"></i>
                </div>
                <div>
                    <span class="font-bold uppercase tracking-wider block">HISTORICAL ARCHIVE VIEW</span>
                    <span>Currently viewing analytics for: <strong>{{ $selectedYear?->year_name }} &bull; {{ $selectedTerm?->term_name }}</strong>. All telemetry is scoped strictly to this historical period.</span>
                </div>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-amber-500 text-zinc-950 font-bold hover:scale-105 transition flex-shrink-0">
                Return to Active Semester &rarr;
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
             x-data="{ counters: { students: 0, subjects: 0, results: 0, collection: 0 }, init() {
                const targets = { students: {{ $totalStudentsCount }}, subjects: {{ $totalSubjectsCount }}, results: {{ $totalResultsCount }}, collection: {{ (int)$collectionRate }} };
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
                    <span class="w-2 h-2 {{ $isHistorical ? 'bg-amber-400' : 'bg-emerald-400' }} rounded-full animate-pulse"></span>
                    {{ $selectedYear?->year_name ?? 'Session' }} &bull; {{ $selectedTerm?->term_name ?? 'Active Semester' }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">System Operations Center</h1>
                <p class="text-blue-100/80 mt-1">Live institutional metrics, student attendance, assessments & revenue</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.students"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Students</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.subjects"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Courses</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.results"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Results</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white"><span x-text="counters.collection"></span>%</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Collection</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== QUICK SHORTCUTS ROW ========== -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mb-8">
        <a href="{{ route('admin.students.create') }}" class="p-4 rounded-2xl bg-[var(--glass-bg)] border border-[var(--border-color)] hover:border-blue-500/50 hover:shadow-lg transition-all flex items-center gap-3.5 group">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 group-hover:scale-110 transition-transform flex items-center justify-center text-lg">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-[var(--text-primary)]">Enroll Student</p>
                <p class="text-[10px] text-[var(--text-secondary)]">Register new profile</p>
            </div>
        </a>

        <a href="{{ route('admin.results.create') }}" class="p-4 rounded-2xl bg-[var(--glass-bg)] border border-[var(--border-color)] hover:border-indigo-500/50 hover:shadow-lg transition-all flex items-center gap-3.5 group">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-500 group-hover:scale-110 transition-transform flex items-center justify-center text-lg">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-[var(--text-primary)]">Record Marks</p>
                <p class="text-[10px] text-[var(--text-secondary)]">Enter Exam 1 & 2</p>
            </div>
        </a>

        <a href="{{ route('admin.fees.create') }}" class="p-4 rounded-2xl bg-[var(--glass-bg)] border border-[var(--border-color)] hover:border-emerald-500/50 hover:shadow-lg transition-all flex items-center gap-3.5 group">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 group-hover:scale-110 transition-transform flex items-center justify-center text-lg">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-[var(--text-primary)]">Issue Invoice</p>
                <p class="text-[10px] text-[var(--text-secondary)]">Tuition & charges</p>
            </div>
        </a>

        <a href="{{ route('admin.reports.index') }}" class="p-4 rounded-2xl bg-[var(--glass-bg)] border border-[var(--border-color)] hover:border-purple-500/50 hover:shadow-lg transition-all flex items-center gap-3.5 group">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 group-hover:scale-110 transition-transform flex items-center justify-center text-lg">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-[var(--text-primary)]">Telemetry & Audit</p>
                <p class="text-[10px] text-[var(--text-secondary)]">Analytics reports</p>
            </div>
        </a>
    </div>

    <!-- ========== MAIN 2-COLUMN OPERATIONAL BENTO ========== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Recent Results Table (Glass design) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm text-white">
                            <i class="fas fa-poll-h text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white tracking-wide">Recent Assessment Entries</h3>
                            <p class="text-xs text-blue-200 mt-0.5">Showing latest scores recorded for this semester</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.results.index', ['term_id' => $selectedTermId]) }}"
                       class="text-xs font-bold text-white/90 bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-xl transition">
                        View All Results &rarr;
                    </a>
                </div>

                <div class="p-6">
                    @if($recentResults->count() > 0)
                        <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                            <table class="min-w-full divide-y divide-[var(--border-color)]">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Student</th>
                                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Subject & Exam</th>
                                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Score</th>
                                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Grade</th>
                                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--border-color)]">
                                    @foreach($recentResults as $res)
                                        <tr class="hover:bg-[var(--accent-soft)] transition-colors duration-200">
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                                        {{ substr($res->student->user->name ?? 'S', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-[var(--text-primary)]">{{ $res->student->user->name ?? 'Unknown' }}</p>
                                                        <p class="text-xs text-[var(--text-secondary)] font-mono">{{ $res->student->reg_number ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <p class="text-sm font-medium text-[var(--text-primary)]">{{ $res->subject->name ?? 'Subject' }}</p>
                                                <span class="text-xs px-2 py-0.5 rounded-full font-mono font-bold {{ $res->exam_type == 'exam1' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' }}">
                                                    {{ $res->exam_type == 'exam1' ? 'Exam 1 (Midterm)' : 'Exam 2 (Final)' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap font-mono font-bold text-sm text-[var(--text-primary)]">
                                                {{ number_format($res->marks, 1) }}%
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono {{ $res->grade == 'A' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : ($res->grade == 'B' ? 'bg-blue-100 text-blue-700' : ($res->grade == 'C' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) }}">
                                                    Grade {{ $res->grade }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                                <a href="{{ route('admin.results.show', $res) }}" class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all inline-block" title="View">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10">
                            <p class="text-sm text-[var(--text-secondary)]">No results recorded yet for this semester.</p>
                            <a href="{{ route('admin.results.create') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow">
                                Record First Result
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Financial & Pending Deadlines Card -->
        <div class="space-y-6">
            <!-- Financial Performance Card -->
            <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-base text-[var(--text-primary)]">Semester Financials</h3>
                    <span class="text-xs font-mono font-bold text-emerald-500">{{ $collectionRate }}% Settled</span>
                </div>

                <div class="space-y-3 font-mono text-xs">
                    <div class="p-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] flex justify-between items-center">
                        <span class="text-[var(--text-secondary)]">Invoiced:</span>
                        <span class="font-bold text-[var(--text-primary)]">MK {{ number_format($totalFeesInvoiced, 0) }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] flex justify-between items-center text-emerald-600 dark:text-emerald-400">
                        <span>Collected:</span>
                        <span class="font-bold">MK {{ number_format($totalFeesCollected, 0) }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] flex justify-between items-center text-rose-600 dark:text-rose-400">
                        <span>Outstanding:</span>
                        <span class="font-bold">MK {{ number_format($totalFeesBalance, 0) }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-[var(--border-color)]">
                    <a href="{{ route('admin.fees.index', ['term_id' => $selectedTermId]) }}"
                       class="w-full py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 shadow hover:scale-102 transition">
                        <span>Manage Invoices & Billing</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Pending Deadlines Card -->
            <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-base text-[var(--text-primary)]">Due Fee Invoices</h3>
                    <a href="{{ route('admin.fees.index', ['term_id' => $selectedTermId, 'status' => 'overdue']) }}" class="text-xs font-bold text-blue-600 hover:underline">
                        View All
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($upcomingDeadlines as $deadline)
                        <div class="p-3 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-red-500/10 text-red-500 rounded-xl flex items-center justify-center font-bold font-mono text-xs">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[var(--text-primary)] truncate">{{ $deadline->student->user->name ?? 'Student' }}</p>
                                    <p class="text-[10px] text-[var(--text-secondary)] font-mono">Due: {{ $deadline->due_date?->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold text-red-600 dark:text-red-400">
                                MK {{ number_format($deadline->balance, 0) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-center text-[var(--text-secondary)] py-4">No critical overdue deadlines for this session.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
