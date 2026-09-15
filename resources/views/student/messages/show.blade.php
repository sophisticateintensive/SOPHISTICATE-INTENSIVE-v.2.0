@extends('layouts.student')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between gap-4 pt-2">
        <div>
            <span class="text-xs font-mono uppercase font-bold text-zinc-400">Advisory Consultation</span>
            <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Conversation Thread</h1>
        </div>
        <a href="{{ route('student.messages.index') }}"
            class="px-3.5 py-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold hover:scale-105 transition">
            &larr; Back
        </a>
    </div>

    <!-- Message Bubble Card -->
    <div class="cyber-card p-6 sm:p-8 space-y-6">
        
        <!-- Header status -->
        <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-zinc-900 text-[#ccff00] dark:bg-white dark:text-zinc-950 flex items-center justify-center font-mono font-bold text-xs">
                    {{ $message->admin_id ? 'TUTOR' : 'YOU' }}
                </div>
                <div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white font-mono">{{ $message->admin_id ? 'Faculty Advisory' : 'Student Inquiry' }}</h3>
                    <p class="text-[10px] text-zinc-400 font-mono">{{ $message->created_at->format('M d, Y &bull; h:i A') }}</p>
                </div>
            </div>

            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-black {{ $message->is_read ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400' : 'bg-emerald-500/10 text-emerald-500' }}">
                {{ $message->is_read ? 'READ' : 'ACTIVE' }}
            </span>
        </div>

        <!-- Topic -->
        @if($message->subject)
            <div class="space-y-1">
                <span class="text-[10px] uppercase font-bold text-zinc-400 font-mono">Topic</span>
                <h4 class="text-base font-black text-zinc-900 dark:text-white font-mono">{{ $message->subject }}</h4>
            </div>
        @endif

        <!-- Message Body -->
        <div class="p-5 rounded-3xl bg-zinc-50 dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 text-xs sm:text-sm text-zinc-800 dark:text-zinc-200 leading-relaxed whitespace-pre-wrap font-sans">
            {{ $message->message }}
        </div>

        <!-- Footer -->
        <div class="pt-2 flex items-center justify-between text-xs text-zinc-400 font-mono border-t border-zinc-200 dark:border-zinc-800">
            <span>Logged: {{ $message->created_at->diffForHumans() }}</span>
            <span>Thread #{{ $message->id }}</span>
        </div>

    </div>

</div>
@endsection
