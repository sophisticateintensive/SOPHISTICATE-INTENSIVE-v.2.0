@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Fee Details
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Invoice, payments & balance breakdown for {{ $fee->student->user->name ?? 'Student' }}</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.fees.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Back to Fees
            </a>
            <a href="{{ route('admin.fees.edit', $fee) }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs"></i>
                Edit Fee
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Main Card -->
    <div class="bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-[var(--border-color)]">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-xl font-bold shadow-md">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">{{ ucfirst($fee->type) }} Fee Statement</h3>
                    <p class="text-xs text-blue-100 mt-0.5">{{ $fee->student->user->name ?? 'N/A' }} &bull; {{ $fee->student->reg_number ?? 'N/A' }}</p>
                </div>
            </div>

            @if($fee->is_fully_paid)
                <span class="px-3.5 py-1.5 bg-emerald-500/20 text-emerald-100 border border-emerald-400/40 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fas fa-check-circle"></i> Fully Settled
                </span>
            @elseif($fee->is_overdue)
                <span class="px-3.5 py-1.5 bg-red-500/20 text-red-100 border border-red-400/40 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fas fa-exclamation-triangle"></i> Overdue
                </span>
            @else
                <span class="px-3.5 py-1.5 bg-amber-500/20 text-amber-100 border border-amber-400/40 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fas fa-clock"></i> Partial Payment
                </span>
            @endif
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Bento Financial Metric Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-[var(--glass-bg)] p-5 rounded-2xl border border-[var(--border-color)] text-center">
                    <p class="text-xs text-[var(--text-muted)] uppercase font-semibold">Total Invoiced</p>
                    <p class="text-2xl font-black text-[var(--text-primary)] mt-1">MK {{ number_format($fee->amount, 0) }}</p>
                </div>

                <div class="bg-[var(--glass-bg)] p-5 rounded-2xl border border-[var(--border-color)] text-center">
                    <p class="text-xs text-[var(--text-muted)] uppercase font-semibold">Amount Paid</p>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">MK {{ number_format($fee->paid, 0) }}</p>
                </div>

                <div class="bg-[var(--glass-bg)] p-5 rounded-2xl border border-[var(--border-color)] text-center">
                    <p class="text-xs text-[var(--text-muted)] uppercase font-semibold">Remaining Balance</p>
                    <p class="text-2xl font-black {{ $fee->balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }} mt-1">
                        MK {{ number_format($fee->balance, 0) }}
                    </p>
                </div>
            </div>

            <!-- Details 2-Column Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student Information -->
                <div class="bg-[var(--glass-bg)] p-5 rounded-2xl border border-[var(--border-color)] space-y-3">
                    <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center mb-3">
                        <i class="fas fa-user-graduate text-blue-500 mr-2"></i>
                        Student Information
                    </h4>
                    <div class="flex justify-between text-sm py-1.5 border-b border-[var(--border-color)]">
                        <span class="text-[var(--text-muted)]">Full Name</span>
                        <span class="font-bold text-[var(--text-primary)]">{{ $fee->student->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between text-sm py-1.5 border-b border-[var(--border-color)]">
                        <span class="text-[var(--text-muted)]">Reg Number</span>
                        <span class="font-mono font-bold text-[var(--text-primary)]">{{ $fee->student->reg_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between text-sm py-1.5 border-b border-[var(--border-color)]">
                        <span class="text-[var(--text-muted)]">Programme</span>
                        <span class="font-semibold text-[var(--text-primary)]">{{ $fee->student->programme ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Academic & Payment Period -->
                <div class="bg-[var(--glass-bg)] p-5 rounded-2xl border border-[var(--border-color)] space-y-3">
                    <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center mb-3">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        Academic Context
                    </h4>
                    <div class="flex justify-between text-sm py-1.5 border-b border-[var(--border-color)]">
                        <span class="text-[var(--text-muted)]">Academic Year</span>
                        <span class="font-bold text-[var(--text-primary)]">{{ $fee->academicYear->year_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between text-sm py-1.5 border-b border-[var(--border-color)]">
                        <span class="text-[var(--text-muted)]">Term</span>
                        <span class="font-semibold text-[var(--text-primary)]">{{ $fee->term->term_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between text-sm py-1.5 border-b border-[var(--border-color)]">
                        <span class="text-[var(--text-muted)]">Due Date</span>
                        <span class="font-semibold text-[var(--text-primary)]">{{ $fee->due_date->format('F d, Y') }}</span>
                    </div>
                </div>
            </div>

            @if($fee->notes || $fee->description)
                <div class="bg-[var(--glass-bg)] p-5 rounded-2xl border border-[var(--border-color)]">
                    <h4 class="text-sm font-bold text-[var(--text-primary)] mb-2 flex items-center">
                        <i class="fas fa-sticky-note text-amber-500 mr-2"></i>
                        Administrative Notes
                    </h4>
                    <p class="text-sm text-[var(--text-secondary)] leading-relaxed">{{ $fee->notes ?: $fee->description }}</p>
                </div>
            @endif

            <!-- Print & Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-[var(--border-color)]">
                <button onclick="window.print()" class="px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition flex items-center gap-2">
                    <i class="fas fa-print text-xs"></i>
                    Print Receipt
                </button>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.fees.edit', $fee) }}"
                        class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition flex items-center gap-1.5">
                        <i class="fas fa-edit text-xs"></i>
                        Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
