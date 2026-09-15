@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Edit Fee Record
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Update payments, fee amounts, and due dates</p>
        </div>

        <a href="{{ route('admin.fees.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-2 text-xs"></i>
            Back to Fees
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Main Card -->
        <div class="bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-[var(--border-color)]">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 text-white flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-xl p-2.5 backdrop-blur-sm">
                        <i class="fas fa-edit text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Edit Fee Assessment</h3>
                        <p class="text-xs text-blue-100 mt-0.5">{{ $fee->student->user->name ?? 'Student' }} ({{ $fee->student->reg_number ?? 'N/A' }})</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold uppercase tracking-wider">
                    {{ $fee->type }}
                </span>
            </div>

            <!-- Form Body -->
            <div class="p-6 sm:p-8">
                <form action="{{ route('admin.fees.update', $fee) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Display (Read Only) -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Student
                            </label>
                            <div class="p-3.5 bg-blue-500/10 rounded-xl border border-blue-500/20 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="h-9 w-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                                        {{ substr($fee->student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-[var(--text-primary)]">{{ $fee->student->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-[var(--text-secondary)]">{{ $fee->student->reg_number ?? 'N/A' }} &bull; {{ $fee->student->programme ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="student_id" value="{{ $fee->student_id }}">
                            </div>
                        </div>

                        <!-- Academic Year -->
                        <div class="space-y-2">
                            <label for="academic_year_id" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Academic Year <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="academic_year_id" id="academic_year_id"
                                    class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm appearance-none" required>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $fee->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->year_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Term -->
                        <div class="space-y-2">
                            <label for="term_id" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Term / Semester <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="term_id" id="term_id"
                                    class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm appearance-none" required>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}"
                                            data-academic-year-id="{{ $term->academic_year_id }}"
                                            {{ old('term_id', $fee->term_id) == $term->id ? 'selected' : '' }}>
                                            {{ $term->term_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Type -->
                        <div class="space-y-2">
                            <label for="type" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Fee Type <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="type" id="type"
                                    class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm appearance-none" required>
                                    <option value="tuition" {{ old('type', $fee->type) == 'tuition' ? 'selected' : '' }}>Tuition</option>
                                    <option value="registration" {{ old('type', $fee->type) == 'registration' ? 'selected' : '' }}>Registration</option>
                                    <option value="library" {{ old('type', $fee->type) == 'library' ? 'selected' : '' }}>Library</option>
                                    <option value="laboratory" {{ old('type', $fee->type) == 'laboratory' ? 'selected' : '' }}>Laboratory</option>
                                    <option value="sports" {{ old('type', $fee->type) == 'sports' ? 'selected' : '' }}>Sports</option>
                                    <option value="other" {{ old('type', $fee->type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label for="description" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Description (Optional)
                            </label>
                            <input type="text" name="description" id="description" value="{{ old('description', $fee->description) }}"
                                class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm">
                        </div>

                        <!-- Amount -->
                        <div class="space-y-2">
                            <label for="amount" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Invoiced Amount (MK) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="amount" id="amount" value="{{ old('amount', $fee->amount) }}" step="100" min="0"
                                class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm font-semibold" required>
                        </div>

                        <!-- Amount Paid -->
                        <div class="space-y-2">
                            <label for="paid" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Amount Paid (MK) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="paid" id="paid" value="{{ old('paid', $fee->paid) }}" step="100" min="0"
                                class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm font-semibold" required>
                        </div>

                        <!-- Due Date -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="due_date" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Due Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $fee->due_date ? $fee->due_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm" required>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="notes" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                Notes (Optional)
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm">{{ old('notes', $fee->notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Live Balance Preview -->
                    <div class="p-5 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 rounded-2xl border border-blue-500/20">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-[var(--text-primary)] flex items-center">
                                <i class="fas fa-calculator text-blue-500 mr-2"></i>
                                Calculated Balance
                            </h4>
                            <div class="text-xl font-extrabold" id="balancePreview">
                                MK 0
                            </div>
                        </div>
                        <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-2 rounded-full transition-all" id="progressBar" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-[var(--text-muted)] mt-2">
                            <span id="paidPreview">Paid: MK 0 (0%)</span>
                            <span id="amountPreview">Total: MK 0</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-[var(--border-color)]">
                        <a href="{{ route('admin.fees.index') }}"
                            class="px-5 py-2.5 bg-[var(--bg-card)] text-[var(--text-secondary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Update Fee Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const amountInput = document.getElementById('amount');
        const paidInput = document.getElementById('paid');
        const balancePreview = document.getElementById('balancePreview');
        const progressBar = document.getElementById('progressBar');
        const paidPreview = document.getElementById('paidPreview');
        const amountPreview = document.getElementById('amountPreview');

        function updateBalance() {
            const amount = parseFloat(amountInput.value) || 0;
            const paid = parseFloat(paidInput.value) || 0;
            const balance = Math.max(0, amount - paid);
            const percentage = amount > 0 ? Math.min(100, (paid / amount) * 100) : 0;

            balancePreview.textContent = 'MK ' + balance.toLocaleString();
            balancePreview.className = 'text-xl font-extrabold ' + (balance > 0 ? 'text-red-500' : 'text-emerald-500');

            progressBar.style.width = percentage + '%';
            paidPreview.textContent = `Paid: MK ${paid.toLocaleString()} (${percentage.toFixed(1)}%)`;
            amountPreview.textContent = `Total: MK ${amount.toLocaleString()}`;
        }

        amountInput?.addEventListener('input', updateBalance);
        paidInput?.addEventListener('input', updateBalance);

        document.addEventListener('DOMContentLoaded', updateBalance);
    </script>
    @endpush
@endsection
