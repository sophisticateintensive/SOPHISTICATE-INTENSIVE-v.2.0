@props([
    'title' => 'Dashboard',
    'subtitle' => null,
    'showDate' => true,
    'showRefresh' => true,
    'actions' => null,
    'badge' => null,
    'icon' => null
])

<header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Left side: Title and metadata -->
            <div class="flex-1">
                <div class="flex items-center gap-3">
                    <!-- Optional Icon -->
                    @if($icon)
                        <div class="flex-shrink-0">
                            {!! $icon !!}
                        </div>
                    @endif

                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                {{ $title }}
                            </h1>

                            <!-- Optional Badge -->
                            @if($badge)
                                <span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                    {{ $badge }}
                                </span>
                            @endif
                        </div>

                        <!-- Subtitle -->
                        @if($subtitle)
                            <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right side: Date, Refresh, Actions -->
            <div class="flex items-center space-x-3">
                <!-- Date Display -->
                @if($showDate)
                    <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">
                            {{ now()->format('l, F j, Y') }}
                        </span>
                    </div>
                @endif

                <!-- Refresh Button -->
                @if($showRefresh)
                    <button onclick="window.location.reload()"
                        class="p-2 bg-white text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors border border-gray-200"
                        title="Refresh">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                @endif

                <!-- Custom Actions Slot -->
                @if($actions)
                    <div class="flex items-center space-x-2">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>
