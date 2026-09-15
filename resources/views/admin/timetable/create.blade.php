@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.timetable.index') }}" class="p-2 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-blue-500 transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="font-black text-2xl text-[var(--text-primary)] tracking-tight">Add Timetable Entry</h2>
                <p class="text-sm text-[var(--text-secondary)] mt-0.5">Schedule a new class session for a specific weekly schedule</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl">
        <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl shadow-sm p-6 sm:p-8">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20">
                    <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                        @foreach($errors->all() as $error)
                            <li><i class="fas fa-exclamation-circle mr-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Active Semester Auto-select Banner -->
            @if(isset($activeTerm))
                <div class="mb-5 p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5 text-blue-600 dark:text-blue-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <span>Active Session: <strong>{{ $activeYear->year_name ?? '' }} &bull; {{ $activeTerm->term_name }}</strong></span>
                    </div>
                    <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-blue-500/20 text-blue-600 dark:text-blue-400 font-bold">Auto-attached</span>
                </div>
            @endif

            <form action="{{ route('admin.timetable.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Schedule Week Selection -->
                <div class="p-4 rounded-xl bg-blue-50/50 dark:bg-blue-900/10 border border-blue-200/50 dark:border-blue-800/30">
                    <label class="block text-sm font-bold text-[var(--text-primary)] mb-1">
                        <i class="fas fa-calendar-week text-blue-500 mr-1.5"></i> Schedule Week <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-[var(--text-secondary)] mb-2.5">Select any date within the target week. The schedule will automatically map to that week (Monday–Saturday).</p>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <input type="date" name="week_start_date" id="weekStartDateInput"
                               value="{{ old('week_start_date', $selectedWeek ?? \App\Models\Timetable::currentWeekStart()) }}" required
                               class="px-4 py-2.5 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
                        <div class="text-xs font-semibold px-3 py-2 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 flex items-center gap-1.5" id="weekRangeDisplay">
                            <i class="fas fa-info-circle"></i>
                            <span>{{ $weekRangeLabel ?? 'Current Week' }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Academic Year <span class="text-red-500">*</span></label>
                        <select name="academic_year_id" id="academicYearSelect"
                                class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none">
                            <option value="">— Select Academic Year (Defaults to Active) —</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $activeYear?->id) == $year->id ? 'selected' : '' }}>{{ $year->year_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Term <span class="text-red-500">*</span></label>
                        <select name="term_id"
                                class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none">
                            <option value="">— Select Term (Defaults to Active) —</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ old('term_id', $activeTerm?->id) == $term->id ? 'selected' : '' }}>{{ $term->academicYear->year_name ?? '' }} – {{ $term->term_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Subject <span class="text-red-500">*</span></label>
                    <select name="subject_id" required
                            class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none @error('subject_id') border-red-500 @enderror">
                        <option value="">— Select Subject —</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->code }} – {{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Day of Week <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                        @foreach($days as $day)
                            <label class="cursor-pointer">
                                <input type="radio" name="day_of_week" value="{{ $day }}" {{ old('day_of_week', 'Monday') == $day ? 'checked' : '' }} class="sr-only peer" required>
                                <div class="text-center py-2.5 rounded-xl border border-[var(--border-color)] text-sm font-semibold text-[var(--text-secondary)] peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:border-blue-400 transition-all">
                                    {{ substr($day, 0, 3) }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Start Time <span class="text-red-500">*</span></label>
                        <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}" required
                               class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">End Time <span class="text-red-500">*</span></label>
                        <input type="time" name="end_time" value="{{ old('end_time', '10:00') }}" required
                                class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Venue / Room</label>
                        <input type="text" name="venue" value="{{ old('venue') }}" placeholder="e.g. Room 101, Lab A"
                               class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-[var(--text-muted)]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Lecturer Name</label>
                        <input type="text" name="lecturer_name" value="{{ old('lecturer_name') }}" placeholder="e.g. Dr. Smith"
                               class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-[var(--text-muted)]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Optional notes..."
                              class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none placeholder:text-[var(--text-muted)]">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow hover:shadow-lg hover:scale-105 transition-all">
                        <i class="fas fa-save mr-2"></i> Save Entry
                    </button>
                    <a href="{{ route('admin.timetable.index') }}" class="px-6 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-secondary)] text-sm font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
