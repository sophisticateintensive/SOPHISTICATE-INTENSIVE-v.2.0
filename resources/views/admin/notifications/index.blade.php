@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Campus Announcements & Notifications
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Broadcast general circulars or send target alerts to specific students</p>
        </div>

        <div class="flex items-center space-x-3">
            <button onclick="showClearAllModal()"
                class="inline-flex items-center px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 border border-red-500/20 text-sm font-semibold rounded-xl transition">
                <i class="fas fa-trash-alt mr-2 text-xs"></i>
                Clear All
            </button>
            <a href="{{ route('admin.notifications.create') }}"
                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
                <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create Notification
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $totalBroadcasts = $notifications->count();
        $allStudentsCount = $notifications->where('is_sent_to_all', true)->count();
        $individualCount = $notifications->where('is_sent_to_all', false)->count();
        $thisMonthCount = $notifications->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
    @endphp

    <!-- ========== HERO / STATS SECTION (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 sm:p-8 mb-8 shadow-2xl">
        <!-- Animated blobs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4"
             x-data="{ counters: { total: 0, broadcasts: 0, direct: 0, thisMonth: 0 }, init() {
                const targets = { total: {{ $totalBroadcasts }}, broadcasts: {{ $allStudentsCount }}, direct: {{ $individualCount }}, thisMonth: {{ $thisMonthCount }} };
                Object.keys(targets).forEach(key => {
                    const interval = setInterval(() => {
                        if (this.counters[key] < targets[key]) {
                            this.counters[key] += Math.ceil(targets[key] / 30);
                            if (this.counters[key] > targets[key]) this.counters[key] = targets[key];
                        } else {
                            clearInterval(interval);
                        }
                    }, 40);
                });
            }}">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20 text-sm font-medium text-white mb-3">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    {{ $totalBroadcasts }} Dispatches Logged · {{ $allStudentsCount }} Campus-wide Broadcasts
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Campus Circulars & Telemetry</h1>
                <p class="text-blue-100/80 mt-1">Broadcast official announcements, fee reminders, and urgent examination notices</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.total"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Total Notices</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-emerald-300" x-text="counters.broadcasts"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Broadcasts</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-purple-300" x-text="counters.direct"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Direct Alerts</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-amber-300" x-text="counters.thisMonth"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">This Month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== SEARCH & FILTER (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-[var(--border-color)] shadow-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Input -->
            <div class="lg:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="searchInput" onkeyup="filterNotifications()"
                           placeholder="Search notification title, message, student..."
                           class="w-full pl-10 pr-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm hover:shadow-md transition-all duration-200 text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                </div>
            </div>

            <!-- Audience Filter -->
            <div class="relative">
                <select id="recipientFilter" onchange="filterNotifications()"
                        class="w-full px-3 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200 appearance-none cursor-pointer text-[var(--text-primary)]">
                    <option value="">All Audience Types</option>
                    <option value="all">Broadcast (All Students)</option>
                    <option value="individual">Individual Direct</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
                <button type="button" onclick="filterNotifications()" class="flex-1 px-3 py-3 text-sm bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <button type="button" onclick="resetNotificationFilters()" class="px-3 py-3 text-sm bg-[var(--bg-card)] text-[var(--text-primary)] rounded-xl border border-[var(--border-color)] hover:bg-[var(--glass-bg)] hover:scale-105 transition-all duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- ========== NOTIFICATIONS TABLE (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-wide">All Broadcast Dispatches</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Manage notifications, notices, and direct student dispatches</p>
                </div>
            </div>
            <span class="text-xs text-white/80 bg-white/10 px-3 py-1 rounded-full font-mono">
                {{ $notifications->count() }} Notices
            </span>
        </div>

        <div class="p-6">
            @if($notifications->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Announcement</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Target Audience</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Sent Date</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)]">
                            @foreach($notifications as $item)
                                <tr class="notification-row hover:bg-[var(--accent-soft)] transition-colors duration-200"
                                    data-search="{{ strtolower($item->title . ' ' . $item->message . ' ' . ($item->student->user->name ?? '')) }}"
                                    data-audience="{{ $item->is_sent_to_all ? 'all' : 'individual' }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-sm flex-shrink-0 mt-0.5 shadow-sm">
                                                <i class="fas fa-bullhorn"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-[var(--text-primary)] hover:text-blue-600 transition">
                                                    {{ $item->title }}
                                                </p>
                                                <p class="text-xs text-[var(--text-secondary)] mt-0.5 line-clamp-1 max-w-lg">
                                                    {{ $item->message }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->is_sent_to_all)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold font-mono bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                                <i class="fas fa-globe mr-1.5 text-[10px]"></i> All Students
                                            </span>
                                        @elseif($item->student)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold font-mono bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                                                <i class="fas fa-user mr-1.5 text-[10px]"></i> {{ $item->student->user->name ?? 'Student' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                                Direct Alert
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                        {{ $item->created_at->format('M d, Y · h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.notifications.show', $item) }}" class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all" title="View Full Dispatch">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.notifications.edit', $item) }}" class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.notifications.destroy', $item) }}" method="POST" id="delete-notif-{{ $item->id }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="openDeleteModal(
                                                    'Delete Announcement?',
                                                    'You are about to delete {{ addslashes($item->title) }}. This action cannot be undone.',
                                                    document.getElementById('delete-notif-{{ $item->id }}')
                                                )" class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-all" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="bg-[var(--glass-bg)] rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center border border-[var(--border-color)]">
                        <svg class="w-12 h-12 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Announcements Found</h4>
                    <p class="text-[var(--text-secondary)] mb-4">Get started by creating your first broadcast announcement</p>
                    <a href="{{ route('admin.notifications.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add First Announcement
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal for Clear All -->
    <div id="clearAllModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl p-6 sm:p-8 max-w-md w-full mx-4 shadow-2xl">
            <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center text-2xl mb-4 mx-auto">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="text-xl font-bold text-[var(--text-primary)] text-center mb-2">Clear All Notifications?</h3>
            <p class="text-xs text-[var(--text-secondary)] text-center mb-6">
                Are you sure you want to permanently delete all {{ $notifications->count() }} announcements and notifications? This operation cannot be reversed.
            </p>
            <div class="flex space-x-3">
                <button type="button" onclick="hideClearAllModal()" class="flex-1 py-3 px-4 rounded-xl bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-primary)] text-xs font-bold hover:scale-102 transition">
                    Cancel
                </button>
                <form action="{{ route('admin.notifications.clear-all') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs font-bold hover:shadow-lg hover:scale-102 transition">
                        Confirm Clear All
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function filterNotifications() {
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        const audience = document.getElementById('recipientFilter').value;
        const rows = document.querySelectorAll('.notification-row');

        rows.forEach(row => {
            const rowSearch = row.getAttribute('data-search');
            const rowAudience = row.getAttribute('data-audience');

            const matchesSearch = !query || rowSearch.includes(query);
            const matchesAudience = !audience || rowAudience === audience;

            if (matchesSearch && matchesAudience) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function resetNotificationFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('recipientFilter').value = '';
        filterNotifications();
    }

    function showClearAllModal() {
        document.getElementById('clearAllModal').classList.remove('hidden');
    }

    function hideClearAllModal() {
        document.getElementById('clearAllModal').classList.add('hidden');
    }
</script>
@endpush
