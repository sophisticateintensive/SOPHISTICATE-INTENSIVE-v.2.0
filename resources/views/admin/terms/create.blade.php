@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Create Term / Semester
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Add a new academic term or semester cycle to the curriculum</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.terms.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-[var(--glass-bg)] transition-all">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Back to Terms
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

<div class="split-create-container" x-data="termPreview()">
    <!-- LEFT PANEL: Real-time Live Preview & Helpers -->
    <div class="space-y-4">
        <!-- Live Preview Card -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between pb-4 border-b border-[var(--border-color)]">
                <span class="text-[11px] font-black uppercase tracking-wider text-[var(--text-muted)] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Term Card Preview
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20">
                    Preview Mode
                </span>
            </div>

            <!-- Simulated Term Card -->
            <div class="mt-5 p-5 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl shadow-md">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] leading-tight" x-text="termName || 'Term 1'">
                                Term 1
                            </h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5" x-text="getYearName() || 'Academic Year'">
                                Academic Year
                            </p>
                        </div>
                    </div>
                    
                    <span x-show="!isLocked" class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center gap-1">
                        <i class="fas fa-lock-open text-[9px]"></i> Active (Open)
                    </span>
                    <span x-show="isLocked" class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-amber-500/10 text-amber-600 border border-amber-500/20 flex items-center gap-1">
                        <i class="fas fa-lock text-[9px]"></i> Locked
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[var(--border-color)] text-xs">
                    <div class="p-2.5 rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)]">
                        <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase block">Assessments</span>
                        <p class="font-bold text-[var(--text-primary)] mt-0.5 flex items-center gap-1">
                            <i class="fas fa-file-alt text-blue-500 text-[10px]"></i> Exam 1 & Exam 2
                        </p>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)]">
                        <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase block">Grading Access</span>
                        <p class="font-bold text-emerald-500 mt-0.5 flex items-center gap-1" x-text="isLocked ? 'Scores Protected' : 'Open for Entry'">
                            Open for Entry
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Presets -->
            <div class="mt-5 pt-4 border-t border-[var(--border-color)]">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-2">
                    Quick Preset Names (Click to apply)
                </span>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="setTerm('Term 1')"
                        class="format-chip p-2 text-center rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold">
                        Term 1
                    </button>
                    <button type="button" @click="setTerm('Term 2')"
                        class="format-chip p-2 text-center rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold">
                        Term 2
                    </button>
                    <button type="button" @click="setTerm('Term 3')"
                        class="format-chip p-2 text-center rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold">
                        Term 3
                    </button>
                    <button type="button" @click="setTerm('Semester 1')"
                        class="format-chip p-2 text-center rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold">
                        Semester 1
                    </button>
                    <button type="button" @click="setTerm('Semester 2')"
                        class="format-chip p-2 text-center rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold">
                        Semester 2
                    </button>
                    <button type="button" @click="setTerm('Special Term')"
                        class="format-chip p-2 text-center rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-xs text-[var(--text-primary)] font-semibold">
                        Special Term
                    </button>
                </div>
            </div>
        </div>

        <!-- Term Locking Info -->
        <div class="glass-card p-5 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-pink-500/5">
            <div class="flex items-start space-x-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 text-sm">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="text-xs text-[var(--text-secondary)] space-y-1">
                    <p class="font-bold text-[var(--text-primary)]">What does locking a term do?</p>
                    <p>When a term is locked, exam marks and fee records cannot be altered by regular staff, protecting historical transcripts from unintentional changes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Creation Form -->
    <div class="glass-card">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 text-white">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-xl shadow-md">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-wide">Term Configuration</h3>
                    <p class="text-xs text-indigo-100 mt-0.5">Assign academic year and locking settings</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.terms.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Academic Year Select -->
            <div class="space-y-2">
                <label for="academic_year_id" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-primary)]">
                    Parent Academic Year <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                        <i class="fas fa-calendar-alt text-sm"></i>
                    </div>
                    <select name="academic_year_id" id="academic_year_id"
                        x-model="academicYearId"
                        class="input-glass @error('academic_year_id') border-red-400 bg-red-500/5 @enderror"
                        required>
                        <option value="">-- Choose Academic Year --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->year_name }} {{ $year->is_current ? '(Current Active Year)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('academic_year_id')
                    <p class="text-red-500 text-xs mt-1 font-semibold flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Term Name Input -->
            <div class="space-y-2">
                <label for="term_name" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-primary)]">
                    Term / Semester Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                        <i class="fas fa-heading text-sm"></i>
                    </div>
                    <input type="text" name="term_name" id="term_name"
                        x-model="termName"
                        value="{{ old('term_name') }}"
                        class="input-glass @error('term_name') border-red-400 bg-red-500/5 @enderror"
                        placeholder="e.g. Term 1 or Semester 1"
                        required>
                </div>
                @error('term_name')
                    <p class="text-red-500 text-xs mt-1 font-semibold flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Lock Status Toggle Switch -->
            <div class="p-4 rounded-2xl bg-[var(--bg-card-solid)] border border-[var(--border-color)] flex items-center justify-between">
                <div class="space-y-0.5">
                    <label for="is_locked" class="text-xs font-bold text-[var(--text-primary)] block cursor-pointer">
                        Lock Term Immediately
                    </label>
                    <p class="text-[11px] text-[var(--text-muted)]">Prevent student grade edits and enrollment modifications upon creation</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_locked" id="is_locked" value="1"
                        x-model="isLocked"
                        {{ old('is_locked') ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <!-- Action Controls -->
            <div class="pt-4 border-t border-[var(--border-color)] flex items-center justify-end space-x-3">
                <a href="{{ route('admin.terms.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-[var(--border-color)] text-xs font-bold text-[var(--text-primary)] hover:bg-[var(--glass-bg)] transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-black rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Create Term</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function termPreview() {
        return {
            termName: '{{ old('term_name', '') }}',
            academicYearId: '{{ old('academic_year_id', '') }}',
            isLocked: {{ old('is_locked') ? 'true' : 'false' }},
            setTerm(name) {
                this.termName = name;
                document.getElementById('term_name').value = name;
            },
            getYearName() {
                const select = document.getElementById('academic_year_id');
                if (select && select.selectedIndex > 0) {
                    return select.options[select.selectedIndex].text;
                }
                return '';
            }
        };
    }
</script>
@endsection
