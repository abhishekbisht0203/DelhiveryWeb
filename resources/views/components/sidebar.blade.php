{{-- Sidebar --}}
<aside class="fixed top-0 left-0 z-50 h-full w-64 bg-sidebar text-white transition-all duration-300"
       :class="{
           'translate-x-0': mobileSidebar,
           '-translate-x-full lg:translate-x-0': !mobileSidebar,
           'lg:w-20': !sidebarOpen && window.innerWidth >= 1024,
           'lg:w-64': sidebarOpen || window.innerWidth < 1024
       }">

    {{-- Logo --}}
    <div class="flex items-center h-16 px-4 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center font-bold text-sm">D</div>
            <span class="text-lg font-semibold tracking-tight" x-show="sidebarOpen" x-cloak>Delhivery</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="h-[calc(100vh-4rem)] overflow-y-auto py-4 px-3">
        <ul class="space-y-1">

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs('dashboard') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                </a>
            </li>

            {{-- Operations --}}
            <li x-data="{ open: {{ request()->is('shipments*') || request()->is('pickups*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Operations</span>
                    </span>
                    <svg x-show="sidebarOpen" x-cloak class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul x-show="open" x-cloak x-collapse class="ml-8 mt-1 space-y-1">
                    <li><a href="{{ route('shipments.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('shipments.index') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Shipments</a></li>
                    <li><a href="{{ route('shipments.create') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('shipments.create') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Create Shipment</a></li>
                    <li><a href="{{ route('shipments.bulk-upload') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('shipments.bulk-upload') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Bulk Upload</a></li>
                    <li><a href="{{ route('pickups.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('pickups.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Pickups</a></li>
                </ul>
            </li>

            {{-- Logistics --}}
            <li x-data="{ open: {{ request()->is('hubs*') || request()->is('warehouses*') || request()->is('service-areas*') || request()->is('delivery-partners*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Logistics</span>
                    </span>
                    <svg x-show="sidebarOpen" x-cloak class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul x-show="open" x-cloak x-collapse class="ml-8 mt-1 space-y-1">
                    <li><a href="{{ route('hubs.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('hubs.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Hubs</a></li>
                    <li><a href="{{ route('warehouses.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('warehouses.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Warehouses</a></li>
                    <li><a href="{{ route('delivery-partners.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('delivery-partners.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Delivery Partners</a></li>
                </ul>
            </li>

            {{-- Business --}}
            <li x-data="{ open: {{ request()->is('merchants*') || request()->is('customers*') || request()->is('payments*') || request()->is('invoices*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Business</span>
                    </span>
                    <svg x-show="sidebarOpen" x-cloak class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul x-show="open" x-cloak x-collapse class="ml-8 mt-1 space-y-1">
                    <li><a href="{{ route('merchants.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('merchants.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Merchants</a></li>
                    <li><a href="{{ route('payments.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('payments.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">COD Collection</a></li>
                    <li><a href="{{ route('invoices.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('invoices.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Invoices</a></li>
                </ul>
            </li>

            {{-- NDR & RTO --}}
            <li x-data="{ open: {{ request()->is('ndr*') || request()->is('rto*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>NDR & RTO</span>
                    </span>
                    <svg x-show="sidebarOpen" x-cloak class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul x-show="open" x-cloak x-collapse class="ml-8 mt-1 space-y-1">
                    <li><a href="{{ route('ndr.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('ndr.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">NDR Cases</a></li>
                    <li><a href="{{ route('rto.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('rto.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}>RTO Shipments</a></li>
                </ul>
            </li>

            {{-- Reports --}}
            <li x-data="{ open: {{ request()->is('reports*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Reports</span>
                    </span>
                    <svg x-show="sidebarOpen" x-cloak class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul x-show="open" x-cloak x-collapse class="ml-8 mt-1 space-y-1">
                    <li><a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.index') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">All Reports</a></li>
                    <li><a href="{{ route('reports.shipment') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.shipment') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Shipment Reports</a></li>
                    <li><a href="{{ route('reports.delivery') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.delivery') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Delivery Reports</a></li>
                    <li><a href="{{ route('reports.cod') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reports.cod') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">COD Reports</a></li>
                </ul>
            </li>

            {{-- Administration --}}
            <li x-data="{ open: {{ request()->is('users*') || request()->is('roles*') || request()->is('settings*') || request()->is('audit-logs*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Administration</span>
                    </span>
                    <svg x-show="sidebarOpen" x-cloak class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul x-show="open" x-cloak x-collapse class="ml-8 mt-1 space-y-1">
                    <li><a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('users.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Users</a></li>
                    <li><a href="{{ route('roles.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('roles.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Roles & Permissions</a></li>
                    <li><a href="{{ route('settings.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('settings.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Settings</a></li>
                    <li><a href="{{ route('audit-logs.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('audit-logs.*') ? 'text-primary-400 bg-white/5' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Audit Logs</a></li>
                </ul>
            </li>

            {{-- Tracking --}}
            <li>
                <a href="{{ route('tracking.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="sidebarOpen" x-cloak>Public Tracking</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
