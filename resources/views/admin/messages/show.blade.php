@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Message Conversation
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Direct communication with {{ $message->student->user->name ?? 'Student' }}</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.messages.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Back to Messages
            </a>
            <a href="{{ route('admin.messages.create', ['student_id' => $message->student_id]) }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                <i class="fas fa-reply mr-2 text-xs"></i>
                Reply to Student
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Message Card -->
    <div class="bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-[var(--border-color)]">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-xl font-bold shadow-md">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Thread with {{ $message->student->user->name ?? 'Student' }}</h3>
                    <p class="text-xs text-blue-100 mt-0.5">{{ $message->student->reg_number ?? 'N/A' }} &bull; {{ $message->student->programme ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="self-start sm:self-auto">
                @if(!$message->is_read)
                    <span class="px-3.5 py-1.5 bg-amber-500/20 text-amber-100 border border-amber-400/40 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Unread
                    </span>
                @else
                    <span class="px-3.5 py-1.5 bg-emerald-500/20 text-emerald-100 border border-emerald-400/40 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-check"></i>
                        Read
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Academic Context Pill -->
            <div class="flex flex-wrap items-center gap-3 p-4 bg-[var(--glass-bg)] rounded-2xl border border-[var(--border-color)] text-xs text-[var(--text-secondary)]">
                <div class="flex items-center gap-1.5">
                    <i class="fas fa-calendar-alt text-blue-500"></i>
                    <span class="font-semibold text-[var(--text-primary)]">Academic Year:</span>
                    <span>{{ $message->academicYear->year_name ?? 'N/A' }}</span>
                </div>
                <span class="text-[var(--text-muted)]">&bull;</span>
                <div class="flex items-center gap-1.5">
                    <i class="fas fa-clock text-purple-500"></i>
                    <span class="font-semibold text-[var(--text-primary)]">Term:</span>
                    <span>{{ $message->term->term_name ?? 'N/A' }}</span>
                </div>
                <span class="text-[var(--text-muted)]">&bull;</span>
                <div class="flex items-center gap-1.5">
                    <i class="fas fa-history text-emerald-500"></i>
                    <span>{{ $message->created_at ? $message->created_at->format('M d, Y - h:i A') : 'N/A' }}</span>
                </div>
            </div>

            <!-- Message Bubble -->
            <div class="p-6 bg-[var(--bg-card-solid)] rounded-2xl border border-[var(--border-color)] shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr($message->student->user->name ?? 'S', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[var(--text-primary)]">{{ $message->student->user->name ?? 'Student' }}</h4>
                        <p class="text-xs text-[var(--text-muted)]">{{ $message->student->reg_number ?? '' }}</p>
                    </div>
                </div>
                <p class="text-sm text-[var(--text-primary)] leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>
            </div>

            <!-- Quick Reply Section -->
            <div class="p-6 bg-[var(--glass-bg)] rounded-2xl border border-[var(--border-color)]">
                <h4 class="text-sm font-bold text-[var(--text-primary)] mb-3 flex items-center gap-2">
                    <i class="fas fa-reply text-blue-500"></i>
                    Send Quick Reply
                </h4>
                <form action="{{ route('admin.messages.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $message->student_id }}">
                    <input type="hidden" name="academic_year_id" value="{{ $message->academic_year_id }}">
                    <input type="hidden" name="term_id" value="{{ $message->term_id }}">

                    <textarea name="message" rows="4" required
                        class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm"
                        placeholder="Type your response to {{ $message->student->user->name ?? 'the student' }}..."></textarea>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
