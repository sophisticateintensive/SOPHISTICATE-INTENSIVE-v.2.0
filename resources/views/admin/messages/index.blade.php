@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Student Advisory & Communications
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Direct inquiries, feedback threads, and student consultations</p>
        </div>

        <a href="{{ route('admin.messages.create') }}"
            class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
            <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Compose Message
        </a>
    </div>
@endsection

@section('content')
    @php
        $totalMessagesCount = $messages->total();
        $unreadMessagesCount = \App\Models\Message::where('is_read', false)->count();
        $readMessagesCount = \App\Models\Message::where('is_read', true)->count();
        $activeCorrespondents = \App\Models\Message::distinct('student_id')->count('student_id');
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
             x-data="{ counters: { total: 0, unread: 0, read: 0, students: 0 }, init() {
                const targets = { total: {{ $totalMessagesCount }}, unread: {{ $unreadMessagesCount }}, read: {{ $readMessagesCount }}, students: {{ $activeCorrespondents }} };
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
                    <span class="w-2 h-2 {{ $unreadMessagesCount > 0 ? 'bg-amber-400' : 'bg-emerald-400' }} rounded-full animate-pulse"></span>
                    {{ $unreadMessagesCount }} pending responses · {{ $activeCorrespondents }} active student threads
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Advisory Inbox & Consultations</h1>
                <p class="text-blue-100/80 mt-1">Direct communication threads, inquiries, and student pastoral support</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.total"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Total Threads</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-rose-300" x-text="counters.unread"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Unread</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-emerald-300" x-text="counters.read"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Cleared</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-purple-300" x-text="counters.students"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Students</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== SEARCH & FILTER (STUDENT MANAGEMENT DESIGN) ========== -->
    <form method="GET" action="{{ route('admin.messages.index') }}" id="filterForm" class="mb-8">
        <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-[var(--border-color)] shadow-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                               placeholder="Search student name, message subject or content..."
                               class="w-full pl-10 pr-4 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm hover:shadow-md transition-all duration-200 text-[var(--text-primary)] placeholder:text-[var(--text-muted)]">
                        @if(request('search'))
                            <a href="{{ route('admin.messages.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="relative">
                    <select name="status" id="statusFilter"
                            class="w-full px-3 py-3 text-sm bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200 appearance-none cursor-pointer text-[var(--text-primary)]">
                        <option value="">All Statuses</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread Inquiries</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read / Cleared</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-3 py-3 text-sm bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.messages.index') }}" class="px-3 py-3 text-sm bg-[var(--bg-card)] text-[var(--text-primary)] rounded-xl border border-[var(--border-color)] hover:bg-[var(--glass-bg)] hover:scale-105 transition-all duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- ========== MESSAGES TABLE (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-wide">All Student Threads</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Manage advisory discussions and student inquiries</p>
                </div>
            </div>
            <span class="text-xs text-white/80 bg-white/10 px-3 py-1 rounded-full font-mono">
                {{ $messages->total() }} Inquiries
            </span>
        </div>

        <div class="p-6">
            @if($messages->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Inquiry Subject</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Received</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)]">
                            @foreach($messages as $msg)
                                <tr class="hover:bg-[var(--accent-soft)] transition-colors duration-200 {{ !$msg->is_read ? 'bg-blue-500/5' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                {{ substr($msg->student->user->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.students.show', $msg->student) }}" class="text-sm font-bold text-[var(--text-primary)] hover:text-blue-600 transition">
                                                    {{ $msg->student->user->name ?? 'Unknown Student' }}
                                                </a>
                                                <p class="text-xs text-[var(--text-secondary)] font-mono">{{ $msg->student->reg_number ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <p class="text-sm font-bold text-[var(--text-primary)] {{ !$msg->is_read ? 'font-black' : '' }}">
                                                {{ $msg->subject }}
                                            </p>
                                            <p class="text-xs text-[var(--text-secondary)] mt-0.5 line-clamp-1">
                                                {{ $msg->message }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(!$msg->is_read)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping mr-1.5"></span>
                                                Unread
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                                <i class="fas fa-check-double mr-1.5 text-[10px]"></i>
                                                Read
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                        {{ $msg->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.messages.show', $msg) }}" class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all" title="View & Reply">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.messages.destroy', $msg) }}" method="POST" id="delete-msg-{{ $msg->id }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="openDeleteModal(
                                                    'Delete Message?',
                                                    'You are about to delete message from {{ addslashes($msg->student->user->name ?? 'Student') }}. This action cannot be undone.',
                                                    document.getElementById('delete-msg-{{ $msg->id }}')
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

                <div class="mt-8">
                    {{ $messages->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="bg-[var(--glass-bg)] rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center border border-[var(--border-color)]">
                        <svg class="w-12 h-12 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Student Messages Found</h4>
                    <p class="text-[var(--text-secondary)] mb-4">You have no active message threads from students at this time.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
