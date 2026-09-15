@props(['active' => null])

@php
    $navigation = [
        [
            'name' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
            'active' => request()->routeIs('admin.dashboard')
        ],
        [
            'name' => 'Students',
            'route' => 'admin.students.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
            'active' => request()->routeIs('admin.students.*') && !request()->routeIs('admin.students.subjects.*')
        ],
        [
            'name' => 'Subject Assignment',
            'route' => 'admin.students.subjects.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
            'active' => request()->routeIs('admin.students.assign*'),
        ],
        [
            'name' => 'Subjects',
            'route' => 'admin.subjects.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
            'active' => request()->routeIs('admin.subjects.*')
        ],
        [
            'name' => 'Academic Years',
            'route' => 'admin.academic-years.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
            'active' => request()->routeIs('admin.academic-years.*')
        ],
        [
            'name' => 'Terms',
            'route' => 'admin.terms.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />',
            'active' => request()->routeIs('admin.terms.*')
        ],
        [
            'name' => 'Results',
            'route' => 'admin.results.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
            'active' => request()->routeIs('admin.results.*')
        ],
        [
            'name' => 'Fees',
            'route' => 'admin.fees.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'active' => request()->routeIs('admin.fees.*')
        ],
        [
            'name' => 'Reports',
            'route' => 'admin.reports.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
            'active' => request()->routeIs('admin.reports.*'),
            'highlight' => true
        ],
        [
            'name' => 'Notifications',
            'route' => 'admin.notifications.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
            'active' => request()->routeIs('admin.notifications.*')
        ],
        [
            'name' => 'Messages',
            'route' => 'admin.messages.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />',
            'active' => request()->routeIs('admin.messages.*')
        ],
        [
            'name' => 'Resources',
            'route' => 'admin.resources.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
            'active' => request()->routeIs('admin.resources.*')
        ],
    ];
@endphp

<nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
    @foreach($navigation as $item)
        @if(isset($item['highlight']) && $item['highlight'])
            <!-- Separator before highlighted items -->
            <div class="pt-2 mt-2 border-t border-gray-200"></div>

            <!-- Highlighted Link with special styling -->
            <a href="{{ route($item['route']) }}"
                class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200
                                    {{ $item['active']
                    ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg border-l-4 border-white'
                    : 'bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700 hover:from-purple-100 hover:to-pink-100 hover:shadow-md' }}">

                <!-- Icon -->
                <span class="mr-3">
                    <svg class="w-5 h-5 {{ $item['active'] ? 'text-white' : 'text-purple-600' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                </span>

                <!-- Name -->
                <span class="flex-1 font-semibold">{{ $item['name'] }}</span>

                <!-- Sparkle Icon for extra appeal (only for non-active) -->
                @if(!$item['active'])
                    <svg class="w-4 h-4 text-purple-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                @endif

                <!-- Active Indicator -->
                @if($item['active'])
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                @endif
            </a>

            <!-- Separator after highlighted items (except for last one) -->
            @if(!$loop->last && isset($navigation[$loop->index + 1]['highlight']))
                <!-- Keep the separator -->
            @else
                <div class="pb-2 mb-2 border-b border-gray-200"></div>
            @endif
        @else
            <!-- Regular navigation items -->
            <a href="{{ route($item['route']) }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200
                                {{ $item['active']
                    ? 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 shadow-sm border-l-4 border-blue-500'
                    : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">

                <!-- Icon -->
                <span class="mr-3">
                    <svg class="w-5 h-5 {{ $item['active'] ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                </span>

                <!-- Name -->
                <span class="flex-1">{{ $item['name'] }}</span>

                <!-- Active Indicator -->
                @if($item['active'])
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                @endif
            </a>
        @endif
    @endforeach

    <!-- System Info at Bottom -->
    <div class="pt-6 mt-6 border-t border-gray-200">
        <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
            <p class="text-xs font-medium text-blue-800 uppercase tracking-wider">System Info</p>
            <p class="text-sm text-blue-600 mt-1">Laravel v{{ app()->version() }}</p>
            <p class="text-xs text-blue-500 mt-1">{{ now()->format('F j, Y') }}</p>
        </div>
    </div>
</nav>
