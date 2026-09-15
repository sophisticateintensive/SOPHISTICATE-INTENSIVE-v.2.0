@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Announcement Details
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Review full circular text and audience scope</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.notifications.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Back to List
            </a>
            <a href="{{ route('admin.notifications.edit', $notification) }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs"></i>
                Edit
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Main Card -->
    <div class="bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-[var(--border-color)]">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-xl font-bold shadow-md">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">{{ $notification->title }}</h3>
                    <p class="text-xs text-blue-100 mt-0.5">Published on {{ $notification->created_at ? $notification->created_at->format('F d, Y - h:i A') : 'N/A' }}</p>
                </div>
            </div>

            @if($notification->is_sent_to_all)
                <span class="px-3.5 py-1.5 bg-emerald-500/20 text-emerald-100 border border-emerald-400/40 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fas fa-globe"></i> Broadcast to All
                </span>
            @else
                <span class="px-3.5 py-1.5 bg-blue-500/20 text-blue-100 border border-blue-400/40 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fas fa-user"></i> Direct to Student
                </span>
            @endif
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Audience Details -->
            @if(!$notification->is_sent_to_all && $notification->student)
                <div class="p-4 bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-2xl flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                        {{ substr($notification->student->user->name ?? 'S', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[var(--text-primary)]">Recipient: {{ $notification->student->user->name ?? 'Student' }}</p>
                        <p class="text-xs text-[var(--text-muted)]">{{ $notification->student->reg_number ?? '' }} &bull; {{ $notification->student->programme ?? '' }}</p>
                    </div>
                </div>
            @endif

            <!-- Full Announcement Body -->
            <div class="p-6 bg-[var(--bg-card-solid)] rounded-2xl border border-[var(--border-color)] shadow-sm">
                <h4 class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-3">Announcement Content</h4>
                <p class="text-sm text-[var(--text-primary)] leading-relaxed whitespace-pre-wrap">{{ $notification->message }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
