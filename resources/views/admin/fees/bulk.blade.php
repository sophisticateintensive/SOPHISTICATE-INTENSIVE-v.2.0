@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.fees.index') }}" class="hover:text-blue-500 transition">Financial Ledger</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">Bulk Fee Generation</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                ⚡ Bulk Fee Generation
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Generate the same fee for multiple students in one click</p>
        </div>
        <a href="{{ route('admin.fees.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-1.5"></i> Back to Ledger
        </a>
    </div>
@endsection

@section('content')
<div x-data="bulkFeeForm()" class="max-w-6xl mx-auto space-y-6">

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-300 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-300 text-sm">
            <p class="font-bold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Please fix the errors below:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.fees.bulk.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: Student selection --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Fee config card --}}
                <div class="bg-[var(--glass-bg)] rounded-2xl border border-[var(--border-color)] p-6 shadow-lg space-y-5">
                    <h3 class="text-sm font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-blue-500"></i> Fee Configuration
                    </h3>

                    {{-- Fee Type --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Fee Category *</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
                            @foreach(['Tuition Fee','Examination Fee','Registration Fee','Laboratory & Tech Fee'] as $preset)
                                <button type="button" @click="feeType = '{{ $preset }}'"
                                    :class="feeType === '{{ $preset }}' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow' : 'bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)]'"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition text-center truncate">
                                    {{ Str::before($preset, ' Fee') ?: $preset }}
                                </button>
                            @endforeach
                        </div>
                        <input type="text" name="type" required x-model="feeType"
                               placeholder="Or type a custom fee name..."
                               class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)]">
                    </div>

                    {{-- Academic Year & Term --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Academic Session *</label>
                            <select name="academic_year_id" required
                                    class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] font-bold">
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $ay->is_current ? 'selected' : '' }}>{{ $ay->year_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Semester Term *</label>
                            <select name="term_id" required
                                    class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] font-bold">
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ $term->is_current ? 'selected' : '' }}>
                                        {{ $term->term_name }} ({{ $term->academicYear->year_name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Amount & Due Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Amount (MK) *</label>
                            <input type="number" step="1" min="0" name="amount" required x-model="amount"
                                   placeholder="0"
                                   class="w-full px-4 py-3 text-lg font-black font-mono bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Initial Deposit (MK)</label>
                            <input type="number" step="1" min="0" name="paid" x-model="paid"
                                   placeholder="0"
                                   class="w-full px-4 py-3 text-lg font-black font-mono bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Due Date *</label>
                            <input type="date" name="due_date" required
                                   value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] font-mono">
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Notes (optional)</label>
                        <textarea name="notes" rows="2" placeholder="Internal reference notes..."
                                  class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)] resize-none"></textarea>
                    </div>
                </div>

                {{-- Student selection card --}}
                <div class="bg-[var(--glass-bg)] rounded-2xl border border-[var(--border-color)] p-6 shadow-lg space-y-4">
                    <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-3">
                        <h3 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-users text-blue-500"></i>
                            Select Students
                            <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-blue-500/10 text-blue-600" x-text="selectedCount + ' selected'"></span>
                        </h3>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="selectAll()"
                                    class="px-3 py-1.5 text-xs font-bold bg-blue-500/10 text-blue-600 rounded-lg hover:bg-blue-500/20 transition">
                                Select All
                            </button>
                            <button type="button" @click="clearAll()"
                                    class="px-3 py-1.5 text-xs font-bold bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)] rounded-lg hover:bg-[var(--glass-bg)] transition">
                                Clear
                            </button>
                        </div>
                    </div>

                    {{-- Search box --}}
                    <input type="text" x-model="search" placeholder="Search by name or reg number..."
                           class="w-full px-4 py-2.5 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)]">

                    {{-- Student list --}}
                    <div class="max-h-96 overflow-y-auto space-y-1.5 pr-1">
                        @foreach($students as $student)
                            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer hover:bg-[var(--accent-soft)] transition border border-transparent hover:border-[var(--border-color)]"
                                   x-show="matchesSearch('{{ strtolower(addslashes($student->user->name ?? '')) }}', '{{ strtolower($student->reg_number ?? '') }}')"
                                   x-transition>
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                       x-model="selectedIds"
                                       @change="updateCount()"
                                       class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 flex-shrink-0">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-[var(--text-primary)] truncate">{{ $student->user->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-[var(--text-muted)] font-mono">{{ $student->reg_number ?? 'N/A' }} &bull; {{ $student->programme ?? 'Regular' }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('student_ids')
                        <p class="text-xs text-red-500 font-semibold flex items-center gap-1">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- RIGHT: Summary + submit --}}
            <div class="space-y-4">
                <div class="bg-[var(--glass-bg)] rounded-2xl border border-[var(--border-color)] p-6 shadow-lg space-y-5 sticky top-24">
                    <h3 class="text-sm font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-2">
                        <i class="fas fa-calculator text-emerald-500"></i> Summary
                    </h3>

                    <div class="space-y-3 font-mono text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--text-muted)]">Students:</span>
                            <span class="font-black text-[var(--text-primary)]" x-text="selectedCount"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--text-muted)]">Fee per student:</span>
                            <span class="font-black text-[var(--text-primary)]">MK <span x-text="Number(amount || 0).toLocaleString()"></span></span>
                        </div>
                        <div class="border-t border-[var(--border-color)] pt-3 flex items-center justify-between">
                            <span class="font-bold text-[var(--text-primary)]">Total invoiced:</span>
                            <span class="text-xl font-black text-blue-600" x-text="'MK ' + (selectedCount * Number(amount || 0)).toLocaleString()"></span>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-700 dark:text-emerald-300 space-y-1">
                        <p class="font-bold flex items-center gap-1"><i class="fas fa-info-circle"></i> Smart Duplicate Skip</p>
                        <p>Students who already have this exact fee type for the selected term will be automatically skipped — no duplicates.</p>
                    </div>

                    <button type="submit" :disabled="selectedCount === 0 || !amount"
                            :class="selectedCount > 0 && amount ? 'from-blue-500 to-indigo-600 hover:shadow-xl hover:scale-[1.02]' : 'from-gray-400 to-gray-500 cursor-not-allowed opacity-60'"
                            class="w-full py-4 bg-gradient-to-r text-white text-sm font-black rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-bolt"></i>
                        <span>Generate <span x-text="selectedCount"></span> Fee Invoice(s)</span>
                    </button>

                    <a href="{{ route('admin.fees.create') }}"
                       class="block w-full py-2.5 text-center text-xs font-bold text-[var(--text-secondary)] border border-[var(--border-color)] rounded-xl hover:bg-[var(--glass-bg)] transition">
                        <i class="fas fa-user mr-1"></i> Single Student Instead
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
function bulkFeeForm() {
    return {
        feeType: 'Tuition Fee',
        amount: '',
        paid: '',
        search: '',
        selectedIds: [],
        selectedCount: 0,

        selectAll() {
            const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
            this.selectedIds = [];
            checkboxes.forEach(cb => {
                if (cb.closest('label').style.display !== 'none') {
                    this.selectedIds.push(cb.value);
                }
            });
            this.selectedCount = this.selectedIds.length;
        },

        clearAll() {
            this.selectedIds = [];
            this.selectedCount = 0;
        },

        updateCount() {
            this.selectedCount = this.selectedIds.length;
        },

        matchesSearch(name, reg) {
            if (!this.search) return true;
            const q = this.search.toLowerCase();
            return name.includes(q) || reg.includes(q);
        }
    };
}
</script>
@endpush
@endsection
