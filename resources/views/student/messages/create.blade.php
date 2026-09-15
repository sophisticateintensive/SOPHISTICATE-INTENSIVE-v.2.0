@extends('layouts.student')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between gap-4 pt-2">
        <div>
            <span class="text-xs font-mono uppercase font-bold text-zinc-400">Direct Advisory</span>
            <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">New Inquiry</h1>
        </div>
        <a href="{{ route('student.messages.index') }}"
            class="px-3.5 py-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold hover:scale-105 transition">
            &larr; Inbox
        </a>
    </div>

    <!-- Form Cyber Card -->
    <div class="cyber-card p-6 sm:p-8 space-y-6">
        <form action="{{ route('student.messages.store') }}" method="POST" class="space-y-5 font-mono">
            @csrf

            <!-- Subject Input -->
            <div class="space-y-1.5">
                <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-zinc-400">
                    Topic / Subject <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                    class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-2xl text-xs font-semibold text-zinc-900 dark:text-white focus:ring-2 focus:ring-[#ccff00] focus:outline-none"
                    placeholder="e.g. Physics Assignment 2 Clarification" required>
                @error('subject')
                    <p class="text-rose-500 text-xs font-sans mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message Textarea -->
            <div class="space-y-1.5">
                <label for="message" class="block text-xs font-bold uppercase tracking-wider text-zinc-400">
                    Inquiry Details <span class="text-rose-500">*</span>
                </label>
                <textarea name="message" id="message" rows="6"
                    class="w-full p-4 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-2xl text-xs text-zinc-900 dark:text-white focus:ring-2 focus:ring-[#ccff00] focus:outline-none leading-relaxed font-sans"
                    placeholder="Type your message clearly to your tutor or administrator..." required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-rose-500 text-xs font-sans mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-zinc-200 dark:border-zinc-800 font-sans">
                <a href="{{ route('student.messages.index') }}"
                    class="px-4 py-2 rounded-full border border-zinc-200 dark:border-zinc-700 text-xs font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-zinc-900 text-white dark:bg-[#ccff00] dark:text-zinc-950 text-xs font-black rounded-full shadow-lg hover:scale-105 active:scale-95 transition flex items-center gap-2 font-mono">
                    <i class="fas fa-paper-plane text-xs"></i>
                    <span>Send Inquiry</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
