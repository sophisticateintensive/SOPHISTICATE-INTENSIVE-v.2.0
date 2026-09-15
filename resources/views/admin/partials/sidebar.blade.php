<div class="space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('admin.dashboard') }}"
        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-blue-700' : 'text-gray-400 group-hover:text-blue-700' }}"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            </path>
        </svg>
        Dashboard
    </a>

    <!-- Students -->
    <a href="{{ route('admin.students.index') }}"
        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.students.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.students.*') ? 'text-blue-700' : 'text-gray-400 group-hover:text-blue-700' }}"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
        Students
    </a>

    <!-- Add your other navigation items here -->
</div>

<!-- System Info -->
<div class="mt-8 pt-6 border-t border-gray-200">
    <div class="px-4 py-3 bg-blue-50 rounded-lg">
        <p class="text-xs font-medium text-blue-800 uppercase tracking-wider">System Info</p>
        <p class="text-sm text-blue-600 mt-1">Laravel v{{ app()->version() }}</p>
        <p class="text-xs text-blue-500 mt-1">{{ now()->format('F j, Y') }}</p>
    </div>
</div>
