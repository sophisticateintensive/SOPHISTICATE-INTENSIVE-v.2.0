@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Create Academic Year
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Configure and initialize a new academic calendar period</p>
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
    <!-- LEFT PANEL: Real-time Live Preview & Format Helper -->
    <div class="space-y-4">
        <!-- Preview Card -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between pb-4 border-b border-[var(--border-color)]">
                <span class="text-[11px] font-black uppercase tracking-wider text-[var(--text-muted)] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Year Card Preview
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20">
                    Preview Mode
                </span>
            </div>

            <!-- Simulated Academic Year Card -->
            <div class="mt-5 p-5 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl shadow-md">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] leading-tight" x-text="yearName || '2026 Academic Year'">
                                2026 Academic Year
                            </h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5 font-medium">Academic Period Record</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                        <i class="fas fa-check-circle text-[9px]"></i> New Entry
                    </span>
                </div>

                <!-- Simulated Sub-metrics -->
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[var(--border-color)] text-xs">
                    <div class="p-2.5 rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)]">
                        <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase block">Terms Capacity</span>
                        <p class="font-bold text-[var(--text-primary)] mt-0.5 flex items-center gap-1">
                            <i class="fas fa-layer-group text-blue-500 text-[10px]"></i> 3 - 4 Terms
                        </p>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)]">
                        <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase block">Status</span>
                        <p class="font-bold text-amber-500 mt-0.5 flex items-center gap-1">
                            <i class="fas fa-clock text-[10px]"></i> Ready to Publish
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Template Starters -->
            <div class="mt-5 pt-4 border-t border-[var(--border-color)]">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-2">
                    Quick Preset Formats (Click to apply)
                </span>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="setYear('{{ date('Y') }} Academic Year')"
                        class="format-chip p-2.5 text-left rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold flex items-center justify-between">
                        <span>{{ date('Y') }} Academic Year</span>
                        <i class="fas fa-magic text-blue-500 text-[10px]"></i>
                    </button>
                    <button type="button" @click="setYear('{{ date('Y') }}-{{ date('Y') + 1 }}')"
                        class="format-chip p-2.5 text-left rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold flex items-center justify-between">
                        <span>{{ date('Y') }}-{{ date('Y') + 1 }}</span>
                        <i class="fas fa-magic text-blue-500 text-[10px]"></i>
                    </button>
                    <button type="button" @click="setYear('{{ date('Y') + 1 }} Academic Year')"
                        class="format-chip p-2.5 text-left rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold flex items-center justify-between">
                        <span>{{ date('Y') + 1 }} Academic Year</span>
                        <i class="fas fa-magic text-blue-500 text-[10px]"></i>
                    </button>
                    <button type="button" @click="setYear('Academic Year {{ date('Y') }}')"
                        class="format-chip p-2.5 text-left rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold flex items-center justify-between">
                        <span>Academic Year {{ date('Y') }}</span>
                        <i class="fas fa-magic text-blue-500 text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Academic Hierarchy Info Box -->
        <div class="glass-card p-5 bg-gradient-to-br from-blue-500/5 via-indigo-500/5 to-purple-500/5">
            <div class="flex items-start space-x-3">
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0 text-sm">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="text-xs text-[var(--text-secondary)] space-y-1">
                    <p class="font-bold text-[var(--text-primary)]">Why create Academic Years?</p>
                    <p>Academic years act as the parent foundation for terms, course enrollments, student fee structures, and examination result records across the institution.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Creation Form -->
    <div class="glass-card">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-6 text-white">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-xl shadow-md">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-wide">Academic Year Details</h3>
                    <p class="text-xs text-blue-100 mt-0.5">Define name and hierarchy parameters</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.academic-years.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

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
                        value="{{ old('year_name') }}"
                        class="input-glass @error('year_name') border-red-400 bg-red-500/5 @enderror"
                        placeholder="e.g. 2026 Academic Year or 2026-2027"
                        required autofocus>
                </div>
                @error('year_name')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1 font-semibold">
                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                    </p>
                @enderror
                <p class="text-[11px] text-[var(--text-muted)]">Give this academic year a recognizable name for students and faculty.</p>
            </div>

            <!-- Action Controls -->
            <div class="pt-4 border-t border-[var(--border-color)] flex items-center justify-end space-x-3">
                <a href="{{ route('admin.academic-years.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-[var(--border-color)] text-xs font-bold text-[var(--text-primary)] hover:bg-[var(--glass-bg)] transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-black rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Save Academic Year</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function academicYearPreview() {
        return {
            yearName: '{{ old('year_name', '') }}',
            setYear(name) {
                this.yearName = name;
                document.getElementById('year_name').value = name;
            }
        };
    }
</script>
@endsection
