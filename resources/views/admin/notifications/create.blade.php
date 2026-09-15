@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.notifications.index') }}" class="hover:text-blue-500 transition">Announcements</a>
                <span>/</span>
                <span class="text-[var(--text-primary)] font-bold">New Circular</span>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Broadcast Campus Circular & Notification
            </h2>
        </div>

        <a href="{{ route('admin.notifications.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-1.5"></i>
            Back to Dispatches
        </a>
    </div>
@endsection

@section('content')
    <div x-data="broadcastStudio()" class="max-w-5xl mx-auto space-y-8">

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
                        Omnichannel Circular & Alert Studio
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Broadcast Announcement Dispatcher</h1>
                    <p class="text-blue-100/80 mt-1">Send official circulars, examination deadlines, and urgent pastoral alerts</p>
                </div>
            </div>
        </div>

        <!-- ========== BROADCAST FORM & LIVE PREVIEW ========== -->
        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT: ANNOUNCEMENT COMPOSER (2 COLS) -->
                <div class="lg:col-span-2 bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 sm:p-8 border border-[var(--border-color)] shadow-lg space-y-6">
                    <h3 class="text-base font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-2">
                        <i class="fas fa-bullhorn text-blue-500"></i>
                        <span>Announcement Parameters</span>
                    </h3>

                    <!-- Audience Toggle -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Target Audience *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 p-4 rounded-xl border cursor-pointer select-none transition"
                                   :class="audience === 'all' ? 'bg-blue-500/10 border-blue-500 font-bold text-blue-600' : 'bg-[var(--bg-card)] border-[var(--border-color)] text-[var(--text-primary)]'">
                                <input type="radio" name="is_sent_to_all" value="1" x-model="audience" class="w-4 h-4 text-blue-600">
                                <div>
                                    <span class="text-xs font-bold block">Campus-wide Broadcast</span>
                                    <span class="text-[10px] text-[var(--text-secondary)]">Delivered to all active students</span>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 p-4 rounded-xl border cursor-pointer select-none transition"
                                   :class="audience === 'individual' ? 'bg-purple-500/10 border-purple-500 font-bold text-purple-600' : 'bg-[var(--bg-card)] border-[var(--border-color)] text-[var(--text-primary)]'">
                                <input type="radio" name="is_sent_to_all" value="0" x-model="audience" class="w-4 h-4 text-purple-600">
                                <div>
                                    <span class="text-xs font-bold block">Target Individual Candidate</span>
                                    <span class="text-[10px] text-[var(--text-secondary)]">Direct pastoral alert</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Individual Student Select -->
                    <div x-show="audience === 'individual'" class="pt-2" style="display: none;">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Select Student Candidate *</label>
                        <select name="student_id"
                                class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 text-[var(--text-primary)]">
                            <option value="">Select Candidate...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->user->name ?? 'Student' }} &mdash; {{ $student->reg_number ?? 'REG-N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quick Preset Templates -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Preset Circular Templates</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="title = 'Official Midterm Examination Schedule'; message = 'Please be advised that midterm practical and theory examinations commence on Monday. Check your assessment studio for time allocations.'"
                                    class="px-3 py-1.5 rounded-lg bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)] text-xs font-bold hover:border-blue-500 transition">
                                📅 Exam Schedule
                            </button>
                            <button type="button" @click="title = 'Tuition Fee Payment Reminder'; message = 'This is a gentle reminder that semester tuition fee installments are due by the end of this month. Kindly settle your balance.'"
                                    class="px-3 py-1.5 rounded-lg bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)] text-xs font-bold hover:border-blue-500 transition">
                                💳 Fee Reminder
                            </button>
                            <button type="button" @click="title = 'Campus Holiday Notice'; message = 'The campus administration office will be closed on Friday in observance of public holiday. Online portals remain operational.'"
                                    class="px-3 py-1.5 rounded-lg bg-[var(--bg-card)] text-[var(--text-secondary)] border border-[var(--border-color)] text-xs font-bold hover:border-blue-500 transition">
                                🏛️ Holiday Notice
                            </button>
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Announcement Title *</label>
                        <input type="text" name="title" required x-model="title"
                               placeholder="e.g. Schedule Revision: End-of-Semester Examinations"
                               class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)]">
                        @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Message Body -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] mb-2">Announcement Message Content *</label>
                        <textarea name="message" rows="4" required x-model="message"
                                  placeholder="Type the full announcement content..."
                                  class="w-full px-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)]"></textarea>
                        @error('message') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- RIGHT: LIVE PREVIEW CARD (1 COL) -->
                <div class="space-y-6">
                    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-lg space-y-4">
                        <h3 class="text-sm font-bold text-[var(--text-primary)] border-b border-[var(--border-color)] pb-3 flex items-center gap-1.5">
                            <i class="fas fa-eye text-blue-500"></i>
                            <span>Student Notification Preview</span>
                        </h3>

                        <!-- Live Preview Card -->
                        <div class="p-4 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-color)] shadow space-y-3">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-base flex-shrink-0 mt-0.5">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-sm text-[var(--text-primary)]" x-text="title || 'Announcement Title Preview'"></p>
                                    <p class="text-xs text-[var(--text-secondary)] mt-1 line-clamp-3" x-text="message || 'The announcement message body will be rendered here as students will see it on their portal...'"></p>
                                    <span class="text-[10px] font-mono text-[var(--text-muted)] block mt-2">Just now &bull; Campus Admin</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Broadcast Announcement</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
@endsection

@push('scripts')
<script>
    function broadcastStudio() {
        return {
            audience: 'all',
            title: '{{ old('title', '') }}',
            message: '{{ old('message', '') }}',
        };
    }
</script>
@endpush
