<section class="space-y-6">
    <!-- Header with Icon -->
    <div class="flex items-start space-x-4">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">
                {{ __('Update Password') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </p>
        </div>
    </div>

    <!-- Password Form -->
    <div class="ml-16">
        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            <!-- Current Password -->
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Current Password') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="update_password_current_password" name="current_password" type="password"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('current_password', 'updatePassword') border-red-300 @enderror"
                        placeholder="Enter your current password" autocomplete="current-password" />
                </div>
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('New Password') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="update_password_password" name="password" type="password"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('password', 'updatePassword') border-red-300 @enderror"
                        placeholder="Enter new password" autocomplete="new-password" />
                </div>

                <!-- Password strength indicator -->
                <div class="mt-3" x-data="{ password: '' }">
                    <input type="hidden" x-model="password" x-ref="passwordInput">
                    <div class="flex items-center space-x-1 mb-2">
                        <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300" :class="{
                                     'bg-red-500 w-1/4': password.length > 0 && password.length < 6,
                                     'bg-yellow-500 w-2/4': password.length >= 6 && password.length < 8,
                                     'bg-blue-500 w-3/4': password.length >= 8 && !/(?=.*[A-Z])(?=.*[0-9])/.test(password),
                                     'bg-green-500 w-full': password.length >= 8 && /(?=.*[A-Z])(?=.*[0-9])/.test(password)
                                 }">
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 flex items-center">
                        <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Use at least 8 characters with uppercase letters and numbers
                    </p>
                </div>

                @error('password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Confirm New Password') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="Confirm your new password" autocomplete="new-password" />
                </div>
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-4">
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-xl hover:scale-105 transition-all duration-200 group">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    {{ __('Update Password') }}
                </button>

                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                        class="text-sm text-green-600 bg-green-50 px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Password updated successfully!') }}
                    </p>
                @endif
            </div>
        </form>
    </div>

    <!-- Password Tips -->
    <div class="ml-16 mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-blue-800">Password Tips:</h4>
                <ul class="mt-2 text-xs text-blue-600 space-y-1">
                    <li class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Use at least 8 characters
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="right" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Include uppercase and lowercase letters
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Add numbers and special characters
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Password strength script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('update_password_password');
            const passwordStrength = document.querySelector('[x-data]');

            if (passwordInput && passwordStrength) {
                passwordInput.addEventListener('input', function () {
                    passwordStrength.__x.$data.password = this.value;
                });
            }
        });
    </script>
</section>
