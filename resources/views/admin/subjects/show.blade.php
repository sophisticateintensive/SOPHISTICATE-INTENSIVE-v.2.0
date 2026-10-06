@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.subjects.index') }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Courses
                </a>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide mt-1">
                {{ $subject->name }} ({{ $subject->code }})
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-0.5">Course overview, enrolled students and uploaded learning resources</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.subjects.edit', $subject) }}"
               class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-md transition">
                Edit Course
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Course Overview Card -->
    <div class="bg-[var(--glass-bg)] backdrop-blur-sm rounded-2xl p-6 border border-[var(--border-color)] shadow-lg mb-8">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-[var(--border-color)]">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-black text-[var(--text-primary)]">{{ $subject->name }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-blue-500 text-white font-mono">{{ $subject->code }}</span>
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] mt-1 font-mono">
                        Credit Weight: {{ $subject->credit_hours }} Credit Hours
                    </p>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl px-4 py-2.5 text-center">
                    <div class="text-lg font-black text-blue-600 dark:text-blue-400">{{ $subject->students->count() }}</div>
                    <div class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Students</div>
                </div>
                <div class="bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-800 rounded-xl px-4 py-2.5 text-center">
                    <div class="text-lg font-black text-cyan-600 dark:text-cyan-400">{{ $subject->resources->count() }}</div>
                    <div class="text-[10px] font-bold text-cyan-500 uppercase tracking-wider">Resources</div>
                </div>
            </div>
        </div>

        <!-- Associated Learning Resources -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-base font-bold text-[var(--text-primary)] flex items-center gap-2">
                    <i class="fas fa-folder-open text-cyan-500"></i>
                    Study Resources & Lecture Files
                </h4>
                <a href="{{ route('admin.resources.create') }}" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                    + Upload New Resource
                </a>
            </div>

            @if($subject->resources->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-[var(--border-color)]">
                    <table class="min-w-full divide-y divide-[var(--border-color)]">
                        <thead>
                            <tr class="bg-[var(--bg-card)]">
                                <th class="px-4 py-3 text-left text-xs font-bold text-[var(--text-secondary)] uppercase">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-[var(--text-secondary)] uppercase">File Name</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-[var(--text-secondary)] uppercase">Size</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-[var(--text-secondary)] uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border-color)] text-xs">
                            @foreach($subject->resources as $resource)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[var(--text-primary)]">{{ $resource->title }}</td>
                                    <td class="px-4 py-3 font-mono text-[var(--text-secondary)]">{{ $resource->file_name }}</td>
                                    <td class="px-4 py-3 font-mono text-[var(--text-secondary)]">{{ round($resource->file_size / 1024, 1) }} KB</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.resources.download', $resource) }}" class="text-blue-600 hover:underline font-bold">Download</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-[var(--text-muted)] py-4 text-center">No study materials uploaded for this course yet.</p>
            @endif
        </div>
    </div>
@endsection