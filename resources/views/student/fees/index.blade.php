@extends('layouts.student')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-mono">
                    <i class="fas fa-wallet mr-1"></i> FINANCIAL WALLET
                </span>
                @if(Auth::user()->student)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono">
                        <i class="fas fa-hand-holding-usd text-blue-500 mr-1"></i> {{ Auth::user()->student->display_funding_source ?? 'Not Specified' }}
                    </span>
                @endif
            </div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                Fee Statement
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-semester-selector 
                :selected="$selectedTermId" 
                :activeTerm="$activeTerm" 
                :isHistorical="$isHistorical" 
                :allTerms="$terms"
            />

            <button onclick="window.print()"
                class="px-4 py-2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-mono font-bold hover:scale-105 transition flex items-center gap-2 self-start sm:self-auto border border-zinc-200 dark:border-zinc-700">
                <i class="fas fa-print"></i>
                <span>Print Statement</span>
            </button>
        </div>
    </div>

    @if($isHistorical)
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-4 font-mono text-xs shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-sm"></i>
                </div>
                <div>
                    <span class="font-bold uppercase tracking-wider block">PAST FINANCIAL LEDGER</span>
                    <span>Viewing tuition statements for previous academic semester.</span>
                </div>
            </div>
            <a href="{{ route('student.fees.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 text-zinc-950 font-bold hover:scale-105 transition flex-shrink-0">
                Back to Current &rarr;
            </a>
        </div>
    @endif

    <!-- Summary Bento Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 font-mono">
        <div class="cyber-card p-5">
            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Invoiced Fees</span>
            <p class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white mt-1">MK {{ number_format($totalFees, 0) }}</p>
            <span class="text-[10px] text-zinc-500 mt-1 block">Curriculum Total</span>
        </div>

        <div class="cyber-card p-5">
            <span class="text-[10px] text-emerald-500 uppercase font-bold block">Paid Amount</span>
            <p class="text-xl sm:text-2xl font-black text-emerald-500 mt-1">MK {{ number_format($totalPaid, 0) }}</p>
            <span class="text-[10px] text-emerald-500/70 mt-1 block">{{ $paymentCount }} Payment(s)</span>
        </div>

        <div class="cyber-card p-5">
            <span class="text-[10px] text-amber-500 uppercase font-bold block">Balance</span>
            <p class="text-xl sm:text-2xl font-black {{ $totalBalance > 0 ? 'text-amber-500' : 'text-emerald-500' }} mt-1">MK {{ number_format($totalBalance, 0) }}</p>
            <span class="text-[10px] text-zinc-500 mt-1 block">{{ $totalBalance > 0 ? 'Pending' : 'Cleared' }}</span>
        </div>

        <div class="cyber-card p-5">
            <span class="text-[10px] text-rose-500 uppercase font-bold block">Overdue</span>
            <p class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $overdueCount }}</p>
            <span class="text-[10px] text-zinc-500 mt-1 block">{{ $overdueCount > 0 ? 'Urgent Attention' : 'Good Standing' }}</span>
        </div>
    </div>

    <!-- Settlement Radar -->
    @php
        $settledPercent = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100) : 100;
    @endphp
    <div class="cyber-card p-6 space-y-3">
        <div class="flex items-center justify-between font-mono">
            <div>
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400">Total Settlement Progress</span>
                <p class="text-xs text-zinc-500 mt-0.5">Automated tracking across all academic terms</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black {{ $totalBalance > 0 ? 'bg-amber-500/10 text-amber-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                {{ $settledPercent }}% Paid
            </span>
        </div>
        <div class="w-full h-3 rounded-full bg-zinc-200 dark:bg-zinc-800 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-blue-600 to-emerald-500 rounded-full transition-all duration-700" style="width: {{ $settledPercent }}%"></div>
        </div>
    </div>

    <!-- Fee Invoices & Transactions Table -->
    <div class="cyber-card p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
            <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Fee Invoices & Payments</span>
            <span class="text-xs text-zinc-500 font-mono">{{ $fees->total() }} Records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="text-[10px] font-black uppercase text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="py-3 px-2">Invoice / Description</th>
                        <th class="py-3 px-2">Term</th>
                        <th class="py-3 px-2">Amount</th>
                        <th class="py-3 px-2">Paid</th>
                        <th class="py-3 px-2">Balance</th>
                        <th class="py-3 px-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-sans">
                    @forelse($fees as $fee)
                        @php
                            $feeBalance = $fee->amount - $fee->paid;
                        @endphp
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition">
                            <td class="py-4 px-2 font-bold text-zinc-900 dark:text-white">
                                {{ $fee->fee_type ?? 'Tuition Fee' }}
                                <span class="text-[10px] text-zinc-400 block font-mono">Ref #{{ $fee->id }}</span>
                            </td>
                            <td class="py-4 px-2 text-zinc-400 font-mono text-xs">
                                {{ $fee->term->term_name ?? 'Term 1' }} ({{ $fee->academicYear->year_name ?? '2026' }})
                            </td>
                            <td class="py-4 px-2 font-mono font-bold text-zinc-900 dark:text-white">
                                MK {{ number_format($fee->amount, 0) }}
                            </td>
                            <td class="py-4 px-2 font-mono font-bold text-emerald-500">
                                MK {{ number_format($fee->paid, 0) }}
                            </td>
                            <td class="py-4 px-2 font-mono font-bold {{ $feeBalance > 0 ? 'text-amber-500' : 'text-emerald-500' }}">
                                MK {{ number_format($feeBalance, 0) }}
                            </td>
                            <td class="py-4 px-2 font-mono">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $fee->status == 'paid' ? 'bg-emerald-500/10 text-emerald-500' : ($fee->status == 'partial' ? 'bg-amber-500/10 text-amber-500' : 'bg-rose-500/10 text-rose-500') }}">
                                    {{ $fee->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-zinc-400 font-mono text-xs">
                                No fee invoice records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fees->hasPages())
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $fees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
