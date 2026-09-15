<footer class="mt-auto border-t border-[var(--border-color)] bg-[var(--glass-bg)] backdrop-blur-sm">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About Section -->
            <div>
                <h3 class="text-sm font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-4">About Us
                </h3>
                <p class="text-sm text-[var(--text-secondary)]">
                    Empowering academic excellence through specialised instruction in Mathematics, General Physics, and
                    General Chemistry since 2021.
                </p>
                <!-- Optional small logo -->
                <div class="mt-3 flex items-center space-x-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Sophisticate Logo"
                        class="h-8 w-8 object-contain rounded-lg shadow-sm">
                    <span class="text-xs font-medium text-[var(--text-secondary)]">Sophisticate Intensive</span>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-4">Quick Links
                </h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="text-sm text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Home</a>
                    </li>
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <li>
                                <a href="{{ route('admin.dashboard') }}"
                                    class="text-sm text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Dashboard</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.students.index') }}"
                                    class="text-sm text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Students</a>
                            </li>
                        @elseif(Auth::user()->role === 'student')
                            <li>
                                <a href="{{ route('student.dashboard') }}"
                                    class="text-sm text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Dashboard</a>
                            </li>
                            <li>
                                <a href="{{ route('student.results.index') }}"
                                    class="text-sm text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">My
                                    Results</a>
                            </li>
                        @endif
                    @else
                        <li>
                            <a href="{{ route('login') }}"
                                class="text-sm text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Login</a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}"
                                class="text-sm text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>

            <!-- Contact Info (updated with real school details) -->
            <div>
                <h3 class="text-sm font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-4">Contact
                </h3>
                <ul class="space-y-3">
                    <li class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm text-[var(--text-secondary)]">Viyere Primary School</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-sm text-[var(--text-secondary)]">+265 888 257 636<br>+265 991 307 343</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm text-[var(--text-secondary)]">recchirwa@gmail.com</span>
                    </li>
                </ul>
            </div>

            <!-- Social Links -->
            <div>
                <h3 class="text-sm font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-4">Follow Us
                </h3>
                <div class="flex space-x-4">
                    <a href="#"
                        class="text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.104c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 0021.614-3.694 13.92 13.92 0 001.422-5.899c0-.21-.005-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 0C5.373 0 0 5.373 0 12c0 5.302 3.438 9.8 8.205 11.387.6.113.82-.26.82-.58 0-.287-.01-1.05-.015-2.06-3.338.726-4.042-1.61-4.042-1.61-.546-1.39-1.335-1.76-1.335-1.76-1.09-.746.082-.73.082-.73 1.205.085 1.84 1.237 1.84 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.776.418-1.306.762-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.468-2.38 1.235-3.22-.123-.3-.535-1.52.117-3.16 0 0 1.008-.322 3.3 1.23.96-.267 1.98-.4 3-.405 1.02.005 2.04.138 3 .405 2.29-1.552 3.297-1.23 3.297-1.23.653 1.64.24 2.86.118 3.16.768.84 1.233 1.91 1.233 3.22 0 4.61-2.804 5.62-5.476 5.92.43.37.824 1.102.824 2.22 0 1.602-.015 2.894-.015 3.287 0 .322.216.698.83.578C20.565 21.795 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="text-[var(--text-secondary)] hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-8 pt-6 border-t border-[var(--border-color)]">
            <p class="text-sm text-[var(--text-muted)] text-center">
                &copy; {{ date('Y') }} Sophisticate Intensive Classes. All rights reserved.
                <span class="block sm:inline mt-1 sm:mt-0">Empowering Academic Excellence.</span>
            </p>
        </div>
    </div>
</footer>
