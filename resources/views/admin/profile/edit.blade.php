@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-[var(--text-primary)] tracking-wide">
                Profile & Account Settings
            </h2>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Manage administrator profile details, credentials, and preferences</p>
        </div>
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center px-4 py-2 bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-primary)] text-sm font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
            <i class="fas fa-arrow-left mr-2 text-xs"></i>
            Back to Dashboard
        </a>
    </div>
@endsection

@section('content')
    @php
        $user = Auth::user();
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Modern Hero Banner (Glass) -->
        <div class="relative bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-[var(--border-color)]">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col md:flex-row items-center gap-6 sm:gap-8">
                    <!-- Avatar with Glow -->
                    <div class="relative">
                        <div class="h-28 w-28 sm:h-32 sm:w-32 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-3xl flex items-center justify-center shadow-2xl text-white text-5xl font-black">
                            {{ substr($user->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 h-5 w-5 bg-emerald-500 rounded-full border-4 border-[var(--bg-card-solid)] shadow-md"></div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1 text-center md:text-left space-y-2">
                        <div class="flex flex-wrap items-center gap-2 justify-center md:justify-start">
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-[var(--text-primary)]">{{ $user->name ?? 'Admin User' }}</h2>
                            <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-full shadow-sm">
                                {{ ucfirst($user->role ?? 'Administrator') }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-[var(--text-secondary)] justify-center md:justify-start">
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-envelope text-blue-500 text-xs"></i>
                                <span>{{ $user->email }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-calendar-alt text-purple-500 text-xs"></i>
                                <span>Member since {{ $user->created_at ? $user->created_at->format('M d, Y') : now()->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Badges -->
                    <div class="flex gap-3">
                        <div class="text-center px-4 py-3 bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-2xl min-w-[90px]">
                            <p class="text-xl font-black text-blue-500">{{ \App\Models\Student::count() }}</p>
                            <p class="text-xs text-[var(--text-muted)] font-medium mt-0.5">Students</p>
                        </div>
                        <div class="text-center px-4 py-3 bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-2xl min-w-[90px]">
                            <p class="text-xl font-black text-amber-500">{{ \App\Models\Fee::whereRaw('paid < amount')->count() }}</p>
                            <p class="text-xs text-[var(--text-muted)] font-medium mt-0.5">Pending Fees</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Switcher -->
            <div class="border-t border-[var(--border-color)] px-6 sm:px-8 bg-[var(--glass-bg)]">
                <nav class="flex gap-6">
                    <button id="profile-tab" onclick="switchTab('profile')" class="py-3.5 text-blue-500 border-b-2 border-blue-500 font-bold text-sm transition-all flex items-center gap-2">
                        <i class="fas fa-user-circle"></i>
                        Personal Information
                    </button>
                    <button id="security-tab" onclick="switchTab('security')" class="py-3.5 text-[var(--text-secondary)] hover:text-[var(--text-primary)] font-semibold text-sm transition-all flex items-center gap-2">
                        <i class="fas fa-shield-alt"></i>
                        Security & Password
                    </button>
                </nav>
            </div>
        </div>

        <!-- Success & Error Alerts -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center gap-3 text-sm font-semibold">
                <i class="fas fa-check-circle text-lg"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-500/10 border-l-4 border-red-500 text-red-600 dark:text-red-400 rounded-xl space-y-1 text-sm font-medium">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <i class="fas fa-exclamation-triangle"></i>
                    Please resolve the following issues:
                </div>
                <ul class="list-disc list-inside ml-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- TAB 1: Personal Information -->
        <div id="profile-content" class="block">
            <div class="bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl border border-[var(--border-color)] overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 sm:px-8 py-4 text-white flex items-center gap-3">
                    <i class="fas fa-id-card text-lg"></i>
                    <div>
                        <h3 class="text-base font-bold">Personal Information</h3>
                        <p class="text-xs text-blue-100">Update your account name and email address</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="name" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                        class="w-full pl-10 pr-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm font-medium"
                                        required>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                                        <i class="fas fa-envelope text-sm"></i>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                        class="w-full pl-10 pr-4 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-[var(--text-primary)] text-sm font-medium"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-[var(--border-color)]">
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                Save Personal Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TAB 2: Security & Password -->
        <div id="security-content" class="hidden">
            <div class="bg-[var(--bg-card)] backdrop-blur-sm rounded-3xl shadow-xl border border-[var(--border-color)] overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 px-6 sm:px-8 py-4 text-white flex items-center gap-3">
                    <i class="fas fa-lock text-lg"></i>
                    <div>
                        <h3 class="text-base font-bold">Security Settings</h3>
                        <p class="text-xs text-purple-100">Update your account password with enhanced security</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('profile.update.password') }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-6 max-w-xl">
                            <!-- Current Password -->
                            <div class="space-y-2">
                                <label for="current_password" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">Current Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                                        <i class="fas fa-key text-sm"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" name="current_password" id="current_password"
                                        class="w-full pl-10 pr-10 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-purple-500 focus:outline-none text-[var(--text-primary)] text-sm"
                                        placeholder="Enter current password" required>
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-muted)] hover:text-[var(--text-primary)]">
                                        <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="space-y-2">
                                <label for="new_password" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">New Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                                        <i class="fas fa-lock text-sm"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" name="new_password" id="new_password"
                                        class="w-full pl-10 pr-10 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-purple-500 focus:outline-none text-[var(--text-primary)] text-sm"
                                        placeholder="Enter new password" required>
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-muted)] hover:text-[var(--text-primary)]">
                                        <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-xs"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-[var(--text-muted)]">Minimum 8 characters with numbers & symbols recommended.</p>
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-2">
                                <label for="new_password_confirmation" class="block text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">Confirm New Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                                        <i class="fas fa-shield-alt text-sm"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" name="new_password_confirmation" id="new_password_confirmation"
                                        class="w-full pl-10 pr-10 py-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-purple-500 focus:outline-none text-[var(--text-primary)] text-sm"
                                        placeholder="Confirm new password" required>
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--text-muted)] hover:text-[var(--text-primary)]">
                                        <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-[var(--border-color)]">
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                                <i class="fas fa-shield-alt"></i>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tab) {
            const profileContent = document.getElementById('profile-content');
            const securityContent = document.getElementById('security-content');
            const profileTab = document.getElementById('profile-tab');
            const securityTab = document.getElementById('security-tab');

            if (tab === 'profile') {
                profileContent.classList.remove('hidden');
                profileContent.classList.add('block');
                securityContent.classList.remove('block');
                securityContent.classList.add('hidden');
                profileTab.className = 'py-3.5 text-blue-500 border-b-2 border-blue-500 font-bold text-sm transition-all flex items-center gap-2';
                securityTab.className = 'py-3.5 text-[var(--text-secondary)] hover:text-[var(--text-primary)] font-semibold text-sm transition-all flex items-center gap-2';
            } else {
                securityContent.classList.remove('hidden');
                securityContent.classList.add('block');
                profileContent.classList.remove('block');
                profileContent.classList.add('hidden');
                securityTab.className = 'py-3.5 text-purple-500 border-b-2 border-purple-500 font-bold text-sm transition-all flex items-center gap-2';
                profileTab.className = 'py-3.5 text-[var(--text-secondary)] hover:text-[var(--text-primary)] font-semibold text-sm transition-all flex items-center gap-2';
            }
        }
    </script>
    @endpush
@endsection
