@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Edit Notification
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Update announcement details and content</p>
        </div>

        <a href="{{ route('admin.notifications.index') }}"
            class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] text-[var(--text-primary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-2 text-xs"></i>
            Back to Notifications
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Main Card -->
        <div class="bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-[var(--border-color)]">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 py-4 text-white">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-xl p-2.5 backdrop-blur-sm">
                        <i class="fas fa-edit text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Edit Announcement</h3>
                        <p class="text-xs text-blue-100 mt-0.5">Modify announcement title or message body</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="p-6 sm:p-8">
                <form action="{{ route('admin.notifications.update', $notification) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Target Audience Display -->
                    <div class="p-4 bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider block">Audience</span>
                            <span class="text-sm font-bold text-[var(--text-primary)]">
                                @if($notification->is_sent_to_all)
                                    Broadcast to All Students
                                @else
                                    Direct: {{ $notification->student->user->name ?? 'Student' }} ({{ $notification->student->reg_number ?? 'N/A' }})
                                @endif
                            </span>
                        </div>
                        <span class="px-3 py-1 bg-blue-500/10 text-blue-500 text-xs font-bold rounded-full border border-blue-500/20">
                            {{ $notification->is_sent_to_all ? 'Public' : 'Targeted' }}
                        </span>
                    </div>

                    <!-- Title -->
                    <div class="space-y-2">
                        <label for="title" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                            Announcement Title <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                                <i class="fas fa-heading text-sm"></i>
                            </div>
                            <input type="text" name="title" id="title" value="{{ old('title', $notification->title) }}" required
                                class="w-full pl-10 pr-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm font-semibold">
                        </div>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message Body -->
                    <div class="space-y-2">
                        <label for="message" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">
                            Message Content <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="6" required
                            class="w-full px-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm leading-relaxed">{{ old('message', $notification->message) }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-[var(--border-color)]">
                        <a href="{{ route('admin.notifications.index') }}"
                            class="px-5 py-2.5 bg-[var(--bg-card)] text-[var(--text-secondary)] text-sm font-semibold rounded-xl border border-[var(--border-color)] hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
