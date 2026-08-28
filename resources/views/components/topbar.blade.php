{{-- Top Bar --}}
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 h-16 flex items-center px-4 md:px-6 lg:px-8 gap-4">
    {{-- Mobile menu button --}}
    <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    {{-- Desktop sidebar toggle --}}
    <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex p-2 rounded-lg text-gray-500 hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    {{-- Search --}}
    <div class="flex-1 max-w-lg">
        <form action="{{ route('shipments.index') }}" method="GET" class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search AWB, Order ID, Customer..."
                   class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
        </form>
    </div>

    {{-- Right actions --}}
    <div class="flex items-center gap-2 md:gap-4">
        {{-- Notifications --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition
                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                    <span class="font-semibold text-sm text-gray-900">Notifications</span>
                    <span class="text-xs text-primary-600 cursor-pointer">Mark all read</span>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-l-2 border-primary-500">
                        <p class="text-sm text-gray-900">Shipment AWB-001 marked as NDR</p>
                        <p class="text-xs text-gray-500 mt-0.5">2 minutes ago</p>
                    </div>
                    <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-l-2 border-success">
                        <p class="text-sm text-gray-900">Bulk upload completed: 145/150 success</p>
                        <p class="text-xs text-gray-500 mt-0.5">15 minutes ago</p>
                    </div>
                    <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-l-2 border-warning">
                        <p class="text-sm text-gray-900">COD collection pending: ₹12,500</p>
                        <p class="text-xs text-gray-500 mt-0.5">1 hour ago</p>
                    </div>
                </div>
                <div class="px-4 py-2 border-t border-gray-100 text-center">
                    <a href="#" class="text-xs font-medium text-primary-600 hover:text-primary-700">View all notifications</a>
                </div>
            </div>
        </div>

        {{-- User dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100">
                <div class="w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-semibold text-sm">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'Admin' }}</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </a>
                <hr class="my-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-danger hover:bg-danger-light">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
