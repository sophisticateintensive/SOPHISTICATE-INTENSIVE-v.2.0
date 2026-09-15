@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Edit Academic Year
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Modify academic calendar configuration for {{ $academicYear->year_name }}</p>
        </div>

        <div class="flex items-center space-x-3">
            <div class="hidden sm:flex items-center space-x-2 px-3.5 py-1.5 bg-[var(--bg-card)] backdrop-blur-sm rounded-xl border border-[var(--border-color)] text-xs text-[var(--text-secondary)]">
                <i class="fas fa-calendar-alt text-blue-500"></i>
                <span>{{ now()->format('F j, Y') }}</span>
            </div>
            <a href="{{ route('admin.academic-years.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-[var(--glass-bg)] transition-all">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Back to Years
            </a>
        </div>
    </div>
@endsection

@section('content')
<style>
    .split-create-container {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 1.5rem;
        max-width: 1200px;
        margin: 0 auto;
        align-items: start;
    }
    @media (max-width: 900px) {
        .split-create-container {
            grid-template-columns: 1fr;
        }
    }
    .glass-card {
        background: var(--bg-card);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-color);
        border-radius: 2rem;
        box-shadow: 0 20px 60px var(--shadow-color);
        overflow: hidden;
        position: relative;
    }
    .input-glass {
        width: 100%;
        background: var(--bg-card-solid);
        border: 1.5px solid var(--border-color);
        border-radius: 1rem;
        padding: 0.85rem 1rem 0.85rem 2.75rem;
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: all 0.25s;
    }
    .input-glass:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        outline: none;
    }
    .format-chip {
        cursor: pointer;
        transition: all 0.2s;
    }
    .format-chip:hover {
        transform: translateY(-2px);
        border-color: #3b82f6;
        background: rgba(59, 130, 246, 0.08);
    }
</style>

<div class="split-create-container" x-data="academicYearPreview()">
    <!-- LEFT PANEL: Real-time Live Preview & Stats -->
    <div class="space-y-4">
        <!-- Preview Card -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between pb-4 border-b border-[var(--border-color)]">
                <span class="text-[11px] font-black uppercase tracking-wider text-[var(--text-muted)] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Current Year Card Preview
                </span>
                @if($academicYear->is_current)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-500/10 text-green-600 border border-green-500/20">
                        Active Year
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-500/10 text-gray-600 border border-gray-500/20">
                        Inactive
                    </span>
                @endif
            </div>

            <!-- Simulated Academic Year Card -->
            <div class="mt-5 p-5 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl shadow-md">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] leading-tight" x-text="yearName || '{{ $academicYear->year_name }}'">
                                {{ $academicYear->year_name }}
                            </h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5 font-mono">ID: #{{ $academicYear->id }} &bull; Created {{ $academicYear->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                    @if($academicYear->is_current)
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center gap-1">
                            <i class="fas fa-star text-[9px]"></i> Current
                        </span>
                    @endif
                </div>

                <!-- Terms count indicator -->
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[var(--border-color)] text-xs">
                    <div class="p-2.5 rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)]">
                        <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase block">Associated Terms</span>
                        <p class="font-bold text-[var(--text-primary)] mt-0.5 flex items-center gap-1">
                            <i class="fas fa-layer-group text-blue-500 text-[10px]"></i> {{ $academicYear->terms->count() ?? 0 }} Terms
                        </p>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)]">
                        <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase block">Last Updated</span>
                        <p class="font-bold text-[var(--text-secondary)] mt-0.5 truncate">
                            {{ $academicYear->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Preset Formats -->
            <div class="mt-5 pt-4 border-t border-[var(--border-color)]">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-2">
                    Quick Presets
                </span>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="setYear('{{ date('Y') }} Academic Year')"
                        class="format-chip p-2 text-left rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold flex items-center justify-between">
                        <span>{{ date('Y') }} Academic Year</span>
                        <i class="fas fa-magic text-blue-500 text-[10px]"></i>
                    </button>
                    <button type="button" @click="setYear('{{ date('Y') }}-{{ date('Y') + 1 }}')"
                        class="format-chip p-2 text-left rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold flex items-center justify-between">
                        <span>{{ date('Y') }}-{{ date('Y') + 1 }}</span>
                        <i class="fas fa-magic text-blue-500 text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Edit Form -->
    <div class="glass-card">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-6 text-white">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-xl shadow-md">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-wide">Edit Academic Year</h3>
                    <p class="text-xs text-blue-100 mt-0.5">Update name and period identifier</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.academic-years.update', $academicYear) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Year Name Field -->
            <div class="space-y-2">
                <label for="year_name" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-primary)]">
                    Academic Year Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                        <i class="fas fa-signature text-sm"></i>
                    </div>
                    <input type="text" name="year_name" id="year_name"
                        x-model="yearName"
                        value="{{ old('year_name', $academicYear->year_name) }}"
                        class="input-glass @error('year_name') border-red-400 bg-red-500/5 @enderror"
                        placeholder="e.g. 2026 Academic Year"
                        required autofocus>
                </div>
                @error('year_name')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1 font-semibold">
                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Action Controls -->
            <div class="pt-4 border-t border-[var(--border-color)] flex items-center justify-end space-x-3">
                <a href="{{ route('admin.academic-years.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-[var(--border-color)] text-xs font-bold text-[var(--text-primary)] hover:bg-[var(--glass-bg)] transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-black rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Update Academic Year</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function academicYearPreview() {
        return {
            yearName: '{{ old('year_name', $academicYear->year_name) }}',
            setYear(name) {
                this.yearName = name;
                document.getElementById('year_name').value = name;
            }
        };
    }
</script>
@endsection
