@extends('layouts.student')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between gap-4 pt-2">
        <div>
            <span class="text-xs font-mono uppercase font-bold text-zinc-400">Academic Bulletin</span>
            <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Circular Details</h1>
        </div>
        <a href="{{ route('student.notifications.index') }}"
            class="px-3.5 py-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold hover:scale-105 transition">
            &larr; Alerts
        </a>
    </div>

    <!-- Bulletin Card -->
    <div class="cyber-card p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-2xl bg-zinc-900 text-[#ccff00] dark:bg-white dark:text-zinc-950 flex items-center justify-center text-base font-mono font-bold">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-zinc-900 dark:text-white font-sans">{{ $notification->title }}</h2>
                    <p class="text-[10px] text-zinc-400 font-mono">{{ $notification->created_at->format('l, F j, Y \a\t g:i A') }}</p>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ $notification->is_sent_to_all ? 'bg-emerald-500/10 text-emerald-500' : 'bg-blue-500/10 text-blue-500' }}">
                {{ $notification->is_sent_to_all ? 'Public' : 'Direct' }}
            </span>
        </div>

        <div class="p-6 rounded-3xl bg-zinc-50 dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 text-xs sm:text-sm text-zinc-800 dark:text-zinc-200 leading-relaxed whitespace-pre-wrap font-sans">
            {{ $notification->message }}
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-zinc-200 dark:border-zinc-800 text-xs text-zinc-400 font-mono">
            <span>Posted {{ $notification->created_at->diffForHumans() }}</span>
            <a href="{{ route('student.notifications.index') }}" class="text-blue-500 font-bold hover:underline">
                All Announcements &rarr;
            </a>
        </div>
    </div>

</div>
@endsection
