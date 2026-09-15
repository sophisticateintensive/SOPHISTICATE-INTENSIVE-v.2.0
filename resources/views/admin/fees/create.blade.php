@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.fees.index') }}" class="hover:text-blue-500 transition">Financial Ledger</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">New Assessment</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Issue Fee Assessment & Invoice
            </h2>
        </div>

        <a href="{{ route('admin.fees.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-1.5"></i>
            Back to Ledger
        </a>
    </div>
@endsection

@section('content')
    <div x-data="feeCalculator()" class="max-w-5xl mx-auto space-y-8">

        <!-- ========== HERO BANNER (STUDENT MANAGEMENT DESIGN) ========== -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 shadow-2xl">
            <!-- Animated blobs -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        Active Session: {{ $currentTerm->term_name ?? 'Active Semester' }}
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Tuition & Fee Invoicing Studio</h1>
                    <p class="text-blue-100/80 mt-1">Generate official tuition billing records, payment receipts, and balance ledgers</p>
                </div>

                <!-- Live Computed Balance Card -->
                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-center min-w-[200px] shadow-lg font-mono">
                    <span class="text-[10px] text-blue-200 uppercase tracking-wider block">Net Balance Due</span>
                    <p class="text-3xl font-black text-white mt-1" x-text="'$' + formatMoney(computedBalance())"></p>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full inline-block mt-1"
                          :class="computedBalance() <= 0 ? 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-400/20 text-amber-300 border border-amber-400/30'"
                          x-text="computedBalance() <= 0 ? 'FULLY SETTLED' : 'OUTSTANDING BALANCE'"></span>
                </div>
            </div>
        </div>

        <!-- ========== MAIN FORM & BREAKDOWN ========== -->
        <form action="{{ route('admin.fees.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT: INVOICE DETAILS (2 COLS) -->
                <div class="lg:col-span-2 bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 sm:p-8 border border-[var(--border-color)] shadow-lg space-y-6">
                    <h3 class="text-base font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-blue-500"></i>
                        <span>Candidate & Fee Categorization</span>
                    </h3>

                    <!-- Student Select -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Select Student Candidate *</label>
                        <select name="student_id" required
                                class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] cursor-pointer">
                            <option value="">Select Candidate...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->user->name ?? 'Student' }} &mdash; {{ $student->reg_number ?? 'REG-N/A' }} ({{ $student->programme ?? 'Regular' }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Fee Type Presets -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Fee Category *</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
                            <button type="button" @click="feeType = 'Tuition Fee'; amountDue = 1200"
                                    :class="feeType === 'Tuition Fee' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow' : 'bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)]'"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition text-center">
                                Tuition
                            </button>
                            <button type="button" @click="feeType = 'Examination Fee'; amountDue = 250"
                                    :class="feeType === 'Examination Fee' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow' : 'bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)]'"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition text-center">
                                Examination
                            </button>
                            <button type="button" @click="feeType = 'Registration Fee'; amountDue = 150"
                                    :class="feeType === 'Registration Fee' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow' : 'bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)]'"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition text-center">
                                Registration
                            </button>
                            <button type="button" @click="feeType = 'Laboratory & Tech Fee'; amountDue = 300"
                                    :class="feeType === 'Laboratory & Tech Fee' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow' : 'bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)]'"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition text-center">
                                Lab / Tech
                            </button>
                        </div>

                        <input type="text" name="type" required x-model="feeType"
                               placeholder="Or type custom fee description..."
                               class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)]">
                    </div>

                    <!-- Academic Year & Term -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Academic Session *</label>
                            <select name="academic_year_id" required
                                    class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-bold">
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $ay->is_current ? 'selected' : '' }}>{{ $ay->year_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Semester Term *</label>
                            <select name="term_id" required
                                    class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-bold">
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ $term->is_current ? 'selected' : '' }}>
                                        {{ $term->term_name }} ({{ $term->academicYear->year_name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Payment Deadline (Due Date)</label>
                        <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] font-mono">
                    </div>
                </div>

                <!-- RIGHT: FINANCIAL VALUES & PAYMENT (1 COL) -->
                <div class="space-y-6">
                    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-lg space-y-5">
                        <h3 class="text-sm font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-1.5">
                            <i class="fas fa-money-bill-wave text-emerald-500"></i>
                            <span>Financial Assessment</span>
                        </h3>

                        <!-- Amount Due -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Amount Invoiced ($) *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-sm text-[var(--text-muted)] font-mono font-bold">$</span>
                                <input type="number" step="0.01" min="0" name="amount" required
                                       x-model="amountDue"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-4 py-3 text-lg font-black font-mono bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)]">
                            </div>
                        </div>

                        <!-- Amount Paid Initially -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Initial Deposit / Amount Paid ($)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-sm text-[var(--text-muted)] font-mono font-bold">$</span>
                                <input type="number" step="0.01" min="0" name="paid"
                                       x-model="amountPaid"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-4 py-3 text-lg font-black font-mono bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)]">
                            </div>
                        </div>

                        <!-- Live Summary Box -->
                        <div class="p-4 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] space-y-2 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="text-[var(--text-muted)]">Invoiced Total:</span>
                                <span class="font-bold text-[var(--text-primary)]" x-text="'$' + formatMoney(amountDue)"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[var(--text-muted)]">Paid Today:</span>
                                <span class="font-bold text-emerald-500" x-text="'$' + formatMoney(amountPaid)"></span>
                            </div>
                            <div class="flex items-center justify-between border-t border-[var(--border-color)] pt-2 font-black">
                                <span class="text-[var(--text-primary)]">Balance Due:</span>
                                <span class="text-sm" :class="computedBalance() <= 0 ? 'text-emerald-500' : 'text-amber-500'" x-text="'$' + formatMoney(computedBalance())"></span>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            <span>Generate Official Assessment</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
@endsection

@push('scripts')
<script>
    function feeCalculator() {
        return {
            feeType: '{{ old('fee_type', 'Tuition Fee') }}',
            amountDue: {{ old('amount_due', 1200) }},
            amountPaid: {{ old('amount_paid', 0) }},

            computedBalance() {
                const due = parseFloat(this.amountDue) || 0;
                const paid = parseFloat(this.amountPaid) || 0;
                return Math.max(0, due - paid);
            },

            formatMoney(val) {
                const n = parseFloat(val) || 0;
                return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        };
    }
</script>
@endpush
