@extends('layouts.student')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-pink-500/10 text-pink-500 border border-pink-500/20 font-mono">
                    <i class="fas fa-comment-dots mr-1"></i> DIRECT ADVISORY
                </span>
                <span class="text-xs text-zinc-400 font-mono">{{ $messages->total() }} Conversations</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                Tutor Advisory
            </h1>
        </div>

        <a href="{{ route('student.messages.create') }}"
            class="px-5 py-2.5 rounded-full bg-zinc-900 text-white dark:bg-[#ccff00] dark:text-zinc-950 text-xs font-mono font-black shadow-lg hover:scale-105 active:scale-95 transition flex items-center justify-center gap-2 self-start sm:self-auto">
            <i class="fas fa-paper-plane text-xs"></i>
            <span>New Advisory Inquiry</span>
        </a>
    </div>

    <!-- Messages List Cyber Card -->
    <div class="cyber-card p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
            <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Advisory Inbox</span>
            <span class="text-xs text-zinc-500 font-mono">{{ $messages->total() }} Total</span>
        </div>

        <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
            @forelse($messages as $msg)
                <a href="{{ route('student.messages.show', $msg) }}"
                    class="py-4 flex items-start justify-between gap-4 hover:opacity-80 transition block group">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-pink-500 flex items-center justify-center text-sm font-mono flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas fa-comment-alt"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-pink-500 transition-colors">
                                    {{ $msg->subject }}
                                </h4>
                                @if(!$msg->is_read)
                                    <span class="w-2 h-2 rounded-full bg-pink-500 animate-pulse"></span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-400 line-clamp-1 font-mono">
                                {{ $msg->message }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right flex-shrink-0 font-mono space-y-1">
                        <span class="text-[10px] text-zinc-500 block">
                            {{ $msg->created_at->diffForHumans() }}
                        </span>
                        <span class="text-xs font-bold text-pink-500 group-hover:underline">
                            Open &rarr;
                        </span>
                    </div>
                </a>
            @empty
                <div class="py-16 text-center text-zinc-400 space-y-3">
                    <div class="w-16 h-16 rounded-full bg-pink-500/10 text-pink-500 flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-comment-slash"></i>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">No Consultations Yet</h3>
                    <p class="text-xs font-mono max-w-sm mx-auto">Reach out to your assigned faculty tutor with academic inquiries or feedback.</p>
                    <div class="pt-2">
                        <a href="{{ route('student.messages.create') }}" class="px-4 py-2 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-mono font-bold text-xs inline-block">
                            Start First Inquiry &rarr;
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($messages->hasPages())
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $messages->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
