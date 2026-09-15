@extends('layouts.student')

@section('content')
@php
    $user    = $user ?? Auth::user();
    $student = $user->student ?? null;
    $profilePic = $student?->profile_picture;
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between gap-4 pt-2">
        <div>
            <span class="text-xs font-mono uppercase font-bold text-zinc-400">Account &amp; Security</span>
            <h1 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Student Identity</h1>
        </div>
        <a href="{{ route('student.dashboard') }}"
            class="px-3.5 py-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold hover:scale-105 transition">
            &larr; Hub
        </a>
    </div>

    <!-- ======== OVERVIEW CARD ======== -->
    <div class="cyber-pass-card rounded-4xl p-6 sm:p-8 space-y-6"
         style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 2rem;">
        <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6 text-center sm:text-left">

            <!-- Avatar display -->
            @if($profilePic)
                <img src="{{ Storage::url($profilePic) }}" alt="Profile Photo"
                     class="w-20 h-20 rounded-3xl object-cover ring-4 ring-blue-500/30 shadow-xl flex-shrink-0">
            @else
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-3xl font-black font-mono shadow-xl flex-shrink-0 select-none">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif

            <div class="flex-1 space-y-2">
                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">{{ $user->name }}</h2>
                <p class="text-xs text-zinc-400 font-mono">{{ $user->email }} &bull; {{ ucfirst($user->role) }}</p>

                @if($student)
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1 font-mono">
                        <span class="px-3 py-1 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-bold border border-blue-500/20">
                            {{ $student->reg_number }}
                        </span>
                        <span class="px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs">
                            {{ $student->programme }}
                        </span>
                        <span class="px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs">
                            <i class="fas fa-hand-holding-usd text-blue-500 mr-1"></i> {{ $student->display_funding_source ?? 'Not Specified' }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ======== PROFILE PICTURE UPLOAD CARD ======== -->
    <div class="cyber-card p-6 sm:p-8 space-y-5"
         x-data="{
             preview: '{{ $profilePic ? Storage::url($profilePic) : '' }}',
             handleFile(e) {
                 const file = e.target.files[0];
                 if (!file) return;
                 const reader = new FileReader();
                 reader.onload = ev => this.preview = ev.target.result;
                 reader.readAsDataURL(file);
             }
         }">
        <div class="flex items-center justify-between">
            <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Profile Photo</span>
            @if($profilePic)
                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold border border-emerald-500/20 font-mono">
                    <i class="fas fa-check mr-1"></i>Photo uploaded
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PATCH')

            <!-- Hidden fields to preserve unchanged values -->
            <input type="hidden" name="name"         value="{{ $user->name }}">
            <input type="hidden" name="email"        value="{{ $user->email }}">
            <input type="hidden" name="phone"        value="{{ $student?->phone }}">
            <input type="hidden" name="parent_phone" value="{{ $student?->parent_phone }}">

            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Preview -->
                <div class="flex-shrink-0">
                    <template x-if="preview">
                        <img :src="preview" alt="Preview"
                             class="w-24 h-24 rounded-3xl object-cover ring-4 ring-blue-500/30 shadow-lg">
                    </template>
                    <template x-if="!preview">
                        <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-blue-600/20 to-indigo-700/20 border-2 border-dashed border-blue-500/30 flex flex-col items-center justify-center text-blue-500/60">
                            <i class="fas fa-user text-2xl mb-1"></i>
                            <span class="text-[9px] font-mono font-bold uppercase">No Photo</span>
                        </div>
                    </template>
                </div>

                <!-- Upload control -->
                <div class="flex-1 space-y-3">
                    <label for="profile_picture"
                           class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl cursor-pointer hover:border-blue-500 hover:bg-blue-500/5 transition group">
                        <i class="fas fa-cloud-upload-alt text-xl text-zinc-400 group-hover:text-blue-500 transition mb-1"></i>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono font-bold group-hover:text-blue-500 transition">Click to choose photo</span>
                        <span class="text-[10px] text-zinc-400 font-mono mt-0.5">JPG, PNG or WEBP · Max 2 MB</span>
                        <input type="file" id="profile_picture" name="profile_picture"
                               accept="image/jpeg,image/png,image/webp"
                               class="hidden"
                               @change="handleFile($event)">
                    </label>

                    @error('profile_picture')
                        <p class="text-rose-500 text-[11px] font-mono">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-xs font-black hover:scale-105 transition shadow-md flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Upload Photo</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ======== FORMS GRID ======== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono">
        <!-- Personal Details -->
        <div class="lg:col-span-2 cyber-card p-6 sm:p-8 space-y-5">
            <span class="text-xs font-black uppercase tracking-wider text-zinc-400">Personal Information</span>

            <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="name" class="block text-[10px] uppercase font-bold text-zinc-400">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-3.5 py-2.5 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-xl text-xs font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        @error('name') <p class="text-rose-500 text-[10px]">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="block text-[10px] uppercase font-bold text-zinc-400">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-3.5 py-2.5 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-xl text-xs font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        @error('email') <p class="text-rose-500 text-[10px]">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="phone" class="block text-[10px] uppercase font-bold text-zinc-400">Phone Contact</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $student?->phone ?? '') }}"
                            class="w-full px-3.5 py-2.5 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-xl text-xs font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="+265 ...">
                    </div>

                    <div class="space-y-1">
                        <label for="parent_phone" class="block text-[10px] uppercase font-bold text-zinc-400">Guardian Contact</label>
                        <input type="text" name="parent_phone" id="parent_phone" value="{{ old('parent_phone', $student?->parent_phone ?? '') }}"
                            class="w-full px-3.5 py-2.5 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-xl text-xs font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="+265 ...">
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-xs font-black hover:scale-105 transition shadow-md">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>

        <div class="cyber-card p-6 sm:p-8 space-y-5">
            <span class="text-xs font-black uppercase tracking-wider text-zinc-400">Update Password</span>

            <form method="POST" action="{{ route('student.profile.update.password') }}" class="space-y-3.5">
                @csrf
                @method('PATCH')

                <div class="space-y-1">
                    <label for="current_password" class="block text-[10px] uppercase font-bold text-zinc-400">Current</label>
                    <input type="password" name="current_password" id="current_password"
                        class="w-full px-3.5 py-2 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-xl text-xs text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>

                <div class="space-y-1">
                    <label for="new_password" class="block text-[10px] uppercase font-bold text-zinc-400">New</label>
                    <input type="password" name="new_password" id="new_password"
                        class="w-full px-3.5 py-2 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-xl text-xs text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>

                <div class="space-y-1">
                    <label for="new_password_confirmation" class="block text-[10px] uppercase font-bold text-zinc-400">Confirm</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                        class="w-full px-3.5 py-2 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 rounded-xl text-xs text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    @error('new_password') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-xs font-black hover:scale-105 transition shadow-md">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sign Out Session Card -->
    <div class="cyber-card p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-rose-500/20 bg-rose-500/5">
        <div class="flex items-center space-x-3.5 text-center sm:text-left">
            <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fas fa-power-off"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Active Student Session</h4>
                <p class="text-xs text-zinc-400">Sign out of this browser session to protect your academic records</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-full text-xs font-bold font-mono hover:scale-105 transition shadow-md flex items-center gap-2">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sign Out Now</span>
            </button>
        </form>
    </div>

</div>
@endsection
