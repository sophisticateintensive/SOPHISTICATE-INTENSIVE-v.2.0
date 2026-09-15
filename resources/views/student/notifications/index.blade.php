@extends('layouts.student')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-500 border border-blue-500/20 font-mono">
                    <i class="fas fa-bell mr-1"></i> BULLETINS & CIRCULARS
                </span>
                <span class="text-xs text-zinc-400 font-mono">{{ $notifications->total() }} Announcements</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                Academic Alerts
            </h1>
        </div>

        @if($notifications->count() > 0)
            <form action="{{ route('student.notifications.clear-all') }}" method="POST" onsubmit="return confirm('Clear all notifications?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 rounded-full bg-rose-500/10 text-rose-500 border border-rose-500/20 text-xs font-mono font-bold hover:bg-rose-500/20 transition flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fas fa-trash-alt text-xs"></i>
                    <span>Clear All</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="cyber-card p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
            <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Announcements Stream</span>
            <span class="text-xs text-zinc-500 font-mono">{{ $notifications->total() }} Updates</span>
        </div>

        <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
            @forelse($notifications as $notif)
                <div class="py-4 flex items-start justify-between gap-4 hover:opacity-80 transition group">
                    <a href="{{ route('student.notifications.show', $notif) }}" class="flex items-start space-x-3.5 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-blue-500 flex items-center justify-center text-sm font-mono flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="space-y-1 min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase font-mono {{ $notif->is_sent_to_all ? 'bg-emerald-500/10 text-emerald-500' : 'bg-blue-500/10 text-blue-500' }}">
                                    {{ $notif->is_sent_to_all ? 'Public' : 'Direct' }}
                                </span>
                                <span class="text-[10px] text-zinc-400 font-mono">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-blue-500 transition-colors truncate">
                                {{ $notif->title }}
                            </h4>
                            <p class="text-xs text-zinc-400 line-clamp-2 leading-relaxed">
                                {{ $notif->message }}
                            </p>
                        </div>
                    </a>

                    <!-- Delete button -->
                    <form action="{{ route('student.notifications.destroy', $notif) }}" method="POST" onsubmit="return confirm('Delete this notification?');" class="flex-shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl text-zinc-400 hover:text-rose-500 hover:bg-rose-500/10 transition" title="Delete">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="py-16 text-center text-zinc-400 space-y-2">
                    <i class="fas fa-bell-slash text-3xl opacity-40"></i>
                    <p class="text-xs font-bold font-mono">No announcements at this time.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
