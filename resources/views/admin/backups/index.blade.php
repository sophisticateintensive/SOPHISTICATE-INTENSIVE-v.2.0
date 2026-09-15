@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Database Backup & Supabase Cloud Sync
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Manage database snapshots, automated backups, and cloud storage redundancy</p>
        </div>

        <form action="{{ route('admin.backups.test-connection') }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-xs font-bold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 hover:scale-105 transition shadow-sm">
                <i class="fas fa-plug text-blue-500 mr-2"></i>
                Test Supabase Ping
            </button>
        </form>
    </div>
@endsection

@section('content')
    @php
        $backupsCount = count($backups);
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
             x-data="{ counters: { total: 0 }, init() {
                const targets = { total: {{ $backupsCount }} };
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
                    <span class="w-2 h-2 {{ $connectionStatus['connected'] ? 'bg-emerald-400' : 'bg-amber-400' }} rounded-full animate-pulse"></span>
                    Supabase Cloud: {{ $connectionStatus['connected'] ? 'Online & Synchronized' : 'Standalone / Local' }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Cloud Backup & Recovery Suite</h1>
                <p class="text-blue-100/80 mt-1">Disaster recovery, automated cron scheduling and encrypted offsite storage</p>
            </div>

            <!-- Floating stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-2xl font-bold text-white" x-text="counters.total"></div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Snapshots</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-sm sm:text-base font-bold text-emerald-300">{{ $connectionStatus['connected'] ? 'Connected' : 'Offline' }}</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Cloud State</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-sm sm:text-base font-bold text-purple-300 truncate">{{ !empty($backups) ? $backups[0]['created_at']->diffForHumans(null, true) : 'N/A' }}</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Latest</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center border border-white/10 hover:scale-105 transition-transform">
                    <div class="text-sm sm:text-base font-bold text-amber-300">02:00 AM</div>
                    <div class="text-xs text-blue-200 uppercase tracking-wider">Daily Cron</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== TAKE BACKUP ACTION CARD (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-lg mb-8">
        <form action="{{ route('admin.backups.create') }}" method="POST" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            @csrf
            <div>
                <h3 class="text-base font-bold text-[var(--text-primary)]">Execute Manual System Snapshot</h3>
                <p class="text-xs text-[var(--text-secondary)] mt-0.5">Generate a complete database dump with full student transcripts, grades, and accounting ledgers</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <select name="type" class="px-4 py-2.5 text-xs font-bold bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-[var(--text-primary)] cursor-pointer">
                        <option value="json">JSON Snapshot (Universal & Fast)</option>
                        <option value="sql">SQL Dump (.sql Schema & Inserts)</option>
                    </select>
                </div>

                <label class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-[var(--text-primary)] bg-[var(--bg-card)] rounded-xl border border-[var(--border-color)] cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <input type="checkbox" name="upload_cloud" value="1" checked class="rounded text-blue-600">
                    <span>Sync to Supabase Storage</span>
                </label>

                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Take Backup Now</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========== BACKUPS TABLE (STUDENT MANAGEMENT DESIGN) ========== -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl shadow-lg border border-[var(--border-color)] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-wide">Available Backup Archives</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Manage local dumps, download files, and sync with cloud</p>
                </div>
            </div>
            <span class="text-xs text-white/80 bg-white/10 px-3 py-1 rounded-full font-mono">
                {{ count($backups) }} Files
            </span>
        </div>

        <div class="p-6">
            @if(count($backups) > 0)
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Backup Filename</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Format</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Size</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Created Timestamp</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)]">
                            @foreach($backups as $b)
                                <tr class="hover:bg-[var(--accent-soft)] transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-base flex-shrink-0 shadow-sm">
                                                <i class="fas {{ $b['type'] === 'SQL Dump' ? 'fa-database text-indigo-500' : 'fa-file-code text-emerald-500' }}"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-xs font-mono text-[var(--text-primary)]">
                                                    {{ $b['filename'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold font-mono {{ $b['type'] === 'SQL Dump' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' }}">
                                            {{ $b['type'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                        {{ number_format($b['size'] / 1024, 1) }} KB
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--text-secondary)] font-mono">
                                        {{ $b['created_at']->format('M d, Y - H:i:s') }}
                                        <span class="text-[10px] text-[var(--text-muted)] block">({{ $b['created_at']->diffForHumans() }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Download Button -->
                                            <a href="{{ route('admin.backups.download', $b['filename']) }}"
                                                class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all"
                                                title="Download to Local Disk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>

                                            <!-- Push to Cloud Button -->
                                            <form action="{{ route('admin.backups.upload-cloud', $b['filename']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-all"
                                                    title="Push Snapshot to Supabase Cloud Storage">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                    </svg>
                                                </button>
                                            </form>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.backups.destroy', $b['filename']) }}" method="POST" id="delete-backup-{{ md5($b['filename']) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="openDeleteModal(
                                                    'Delete Backup Snapshot?',
                                                    'You are about to delete {{ addslashes($b['filename']) }}. This action cannot be undone.',
                                                    document.getElementById('delete-backup-{{ md5($b['filename']) }}')
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Backups Generated Yet</h4>
                    <p class="text-[var(--text-secondary)] mb-4">Click "Take Backup Now" above to generate your first complete database snapshot</p>
                </div>
            @endif
        </div>
    </div>
@endsection
