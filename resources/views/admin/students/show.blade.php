@extends('layouts.admin')

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
        }

        body.dark-mode {
            --bg-primary: #0a0e1a;
            --bg-card: rgba(10, 14, 26, 0.7);
            --border-color: rgba(255, 255, 255, 0.06);
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --shadow-color: rgba(59, 130, 246, 0.25);
            --blob-opacity: 0.2;
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
        .details-container {
            max-width: 1200px;
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
            display: grid;
            grid-template-columns: 1fr 2fr;
            min-height: 600px;
        }

        @media (max-width: 1024px) {
            .details-container {
                grid-template-columns: 1fr;
                border-radius: 2rem;
            }
        }

        /* ----- Left panel – profile & stats (glass) ----- */
        .profile-panel {
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.06), rgba(37, 99, 235, 0.03));
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-right: 1px solid var(--border-color);
            transition: border-color 0.4s;
        }

        @media (max-width: 1024px) {
            .profile-panel {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 2rem 1.5rem;
            }
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 12px 40px rgba(59, 130, 246, 0.3);
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }
        .profile-avatar:hover {
            transform: scale(1.04);
        }

        .profile-name {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .profile-reg {
            font-size: 0.9rem;
            font-family: monospace;
            color: var(--text-secondary);
            background: var(--bg-card);
            padding: 0.15rem 1rem;
            border-radius: 9999px;
            border: 1px solid var(--border-color);
            display: inline-block;
            margin-bottom: 0.75rem;
        }

        .profile-badge {
            display: inline-block;
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 0.25rem 1.25rem;
            border-radius: 9999px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            margin-bottom: 1.5rem;
        }

        .profile-stats {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .stat-item {
            background: var(--bg-card);
            border-radius: 1rem;
            padding: 0.75rem 0.5rem;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(4px);
            transition: transform 0.2s;
        }
        .stat-item:hover {
            transform: translateY(-2px);
        }
        .stat-item .number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .stat-item .label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            letter-spacing: 0.05em;
        }
        .stat-item .number.positive { color: #10b981; }
        .stat-item .number.negative { color: #ef4444; }
        .stat-item .number.warning { color: #f59e0b; }

        /* ----- Right panel – details (glass) ----- */
        .details-panel {
            padding: 2.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            overflow-y: auto;
            max-height: 80vh;
        }

        @media (max-width: 1024px) {
            .details-panel {
                padding: 2rem 1.5rem;
                max-height: none;
            }
        }

        .details-panel .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .details-panel .section-title svg {
            color: #3b82f6;
        }

        /* Info rows */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem 2rem;
        }
        @media (max-width: 640px) {
            .info-grid { grid-template-columns: 1fr; }
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            border-bottom: 1px dashed var(--border-color);
        }
        .info-item .label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .info-item .value {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* ----- Fee cards (cool animated) ----- */
        .fee-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .fee-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 1.2rem;
            background: var(--bg-card);
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            transition: all 0.2s;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .fee-item:hover {
            background: rgba(59, 130, 246, 0.04);
            transform: translateX(4px);
        }
        .fee-item .fee-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex: 1;
        }
        .fee-item .fee-amount {
            font-weight: 700;
            color: var(--text-primary);
        }
        .fee-item .fee-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.15rem 0.8rem;
            border-radius: 9999px;
        }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-partial { background: #fef3c7; color: #92400e; }
        .status-unpaid { background: #fecaca; color: #991b1b; }
        .status-overdue { background: #fee2e2; color: #b91c1c; }

        .fee-progress {
            width: 100px;
            height: 6px;
            background: var(--border-color);
            border-radius: 9999px;
            overflow: hidden;
        }
        .fee-progress .bar {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.6s ease;
        }
        .bar-green { background: #10b981; }
        .bar-yellow { background: #f59e0b; }
        .bar-red { background: #ef4444; }

        /* ----- Empty state ----- */
        .empty-state {
            text-align: center;
            padding: 2rem 0;
            color: var(--text-secondary);
        }
        .empty-state svg {
            color: var(--text-secondary);
            opacity: 0.5;
            margin-bottom: 0.5rem;
        }

        /* ----- Action buttons (floating) ----- */
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-bottom: 0.5rem;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-primary);
            text-decoration: none;
        }
        .action-btn-primary {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(59,130,246,0.25);
        }
        .action-btn-primary:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(59,130,246,0.35);
        }
        .action-btn:hover {
            transform: scale(1.02);
        }

        /* ----- Responsive tweaks ----- */
        @media (max-width: 640px) {
            .profile-avatar { width: 90px; height: 90px; font-size: 2.2rem; }
            .profile-name { font-size: 1.4rem; }
            .action-buttons { justify-content: center; }
            .details-panel { padding: 1.5rem; }
            .fee-item { flex-direction: column; align-items: stretch; }
            .fee-item .fee-info { flex-wrap: wrap; }
        }
    </style>

    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Main container -->
    <div class="details-container">
        <!-- Left: Profile Panel -->
        <div class="profile-panel">
            <div class="profile-avatar">
                {{ substr($student->user->name, 0, 1) }}
            </div>
            <div class="profile-name">{{ $student->user->name }}</div>
            <div class="profile-reg">{{ $student->reg_number }}</div>
            <span class="profile-badge">{{ $student->programme }}</span>

            <!-- Quick stats (fee-focused) -->
            @php
                $totalFees = $student->fees->sum('amount');
                $totalPaid = $student->fees->sum('paid');
                $balance = $totalFees - $totalPaid;
                $paidPercent = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100) : 0;
                $overdueCount = $student->fees->filter(function($fee) {
                    return $fee->paid < $fee->amount && $fee->due_date < now();
                })->count();
            @endphp

            <div class="profile-stats">
                <div class="stat-item">
                    <div class="number">{{ number_format($totalFees, 0) }}</div>
                    <div class="label">Total Fees</div>
                </div>
                <div class="stat-item">
                    <div class="number positive">{{ number_format($totalPaid, 0) }}</div>
                    <div class="label">Paid</div>
                </div>
                <div class="stat-item">
                    <div class="number {{ $balance > 0 ? 'negative' : 'positive' }}">{{ number_format($balance, 0) }}</div>
                    <div class="label">Balance</div>
                </div>
                <div class="stat-item">
                    <div class="number warning">{{ $overdueCount }}</div>
                    <div class="label">Overdue</div>
                </div>
            </div>

            <!-- Payment progress (large) -->
            <div class="mt-4 w-full">
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Paid: {{ $paidPercent }}%</span>
                    <span>MK {{ number_format($totalPaid, 0) }} / MK {{ number_format($totalFees, 0) }}</span>
                </div>
                <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-1">
                    <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full transition-all duration-1000" style="width: {{ $paidPercent }}%"></div>
                </div>
            </div>

            <div class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                Joined {{ $student->created_at->format('M d, Y') }} · Updated {{ $student->updated_at->diffForHumans() }}
            </div>
        </div>

        <!-- Right: Details Panel -->
        <div class="details-panel">
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('admin.students.edit', $student) }}" class="action-btn action-btn-primary">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Student
                </a>
                <a href="{{ route('admin.students.index') }}" class="action-btn">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>
            </div>

            <!-- Personal Information -->
            <div>
                <div class="section-title">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Personal Information
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Full Name</span>
                        <span class="value">{{ $student->user->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email</span>
                        <span class="value">{{ $student->user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Registration Number</span>
                        <span class="value">{{ $student->reg_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Programme</span>
                        <span class="value">{{ $student->programme }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Source of Funding</span>
                        <span class="value">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                <i class="fas fa-hand-holding-usd mr-1.5 text-[10px]"></i>
                                {{ $student->display_funding_source }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contact & Guardian Information -->
            <div>
                <div class="section-title">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Contact & Guardian Details
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Student Phone</span>
                        <span class="value">
                            @if($student->phone)
                                <a href="tel:{{ $student->phone }}" class="text-blue-600 hover:underline inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $student->phone }}
                                </a>
                            @else
                                <span class="text-gray-400 font-normal">Not Provided</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Parent / Guardian Name</span>
                        <span class="value">{{ $student->parent_name ?? 'Not Provided' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Parent / Guardian Phone</span>
                        <span class="value">
                            @if($student->parent_phone)
                                <a href="tel:{{ $student->parent_phone }}" class="text-blue-600 hover:underline inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $student->parent_phone }}
                                </a>
                            @else
                                <span class="text-gray-400 font-normal">Not Provided</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Emergency Contact</span>
                        <span class="value">{{ $student->emergency_contact ?? 'Not Provided' }}</span>
                    </div>
                    <div class="info-item md:col-span-2">
                        <span class="label">Physical Address</span>
                        <span class="value">{{ $student->address ?? 'Not Provided' }}</span>
                    </div>
                </div>
            </div>

            <!-- Enrollment Details -->
            @if($student->activeEnrollment)
                <div>
                    <div class="section-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Enrollment
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Academic Year</span>
                            <span class="value">{{ optional($student->activeEnrollment->academicYear)->year_name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Term</span>
                            <span class="value">{{ optional($student->activeEnrollment->term)->term_name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Enrollment Date</span>
                            <span class="value">{{ optional($student->activeEnrollment)->enrollment_date ? \Carbon\Carbon::parse($student->activeEnrollment->enrollment_date)->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Expected Graduation</span>
                            <span class="value">{{ optional($student->activeEnrollment)->expected_graduation_date ? \Carbon\Carbon::parse($student->activeEnrollment->expected_graduation_date)->format('M d, Y') : 'Not set' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Fees Breakdown -->
            <div>
                <div class="section-title">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Fee Records
                    <span class="ml-auto text-xs text-gray-500 font-normal">{{ $student->fees->count() }} entries</span>
                </div>

                @if($student->fees->count() > 0)
                    <div class="fee-list">
                        @foreach($student->fees->sortByDesc('created_at') as $fee)
                            @php
                                $paid = $fee->paid;
                                $amount = $fee->amount;
                                $percent = $amount > 0 ? round(($paid / $amount) * 100) : 0;
                                $status = $fee->status; // assuming 'paid', 'partial', 'unpaid', 'overdue'
                                $statusClass = $status === 'paid' ? 'status-paid' : ($status === 'partial' ? 'status-partial' : ($status === 'overdue' ? 'status-overdue' : 'status-unpaid'));
                                $barClass = $percent >= 100 ? 'bar-green' : ($percent >= 50 ? 'bar-yellow' : 'bar-red');
                            @endphp
                            <div class="fee-item">
                                <div class="fee-info">
                                    <span class="text-sm font-medium text-gray-600">{{ $fee->description ?? 'Fee' }}</span>
                                    <span class="fee-amount">MK {{ number_format($amount, 0) }}</span>
                                    <span class="fee-status {{ $statusClass }}">{{ ucfirst($status) }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="fee-progress">
                                        <div class="bar {{ $barClass }}" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600">{{ $percent }}%</span>
                                    <span class="text-xs text-gray-400">
                                        Due: {{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('M d, Y') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm">No fee records found for this student.</p>
                    </div>
                @endif
            </div>

            <!-- Optional: Quick stats row (paid/unpaid summary) -->
            @if($student->fees->count() > 0)
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-2 text-center border border-green-200 dark:border-green-800/30">
                        <span class="text-xs text-gray-500">Paid</span>
                        <div class="text-lg font-bold text-green-600">{{ number_format($student->fees->sum('paid'), 0) }}</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-2 text-center border border-red-200 dark:border-red-800/30">
                        <span class="text-xs text-gray-500">Outstanding</span>
                        <div class="text-lg font-bold text-red-600">{{ number_format($student->fees->sum('amount') - $student->fees->sum('paid'), 0) }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
