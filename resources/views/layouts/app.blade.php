<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Delhivery')) - Logistics Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                        sidebar: { DEFAULT:'#1e293b', light:'#334155', dark:'#0f172a' },
                        success: { DEFAULT:'#16a34a', light:'#dcfce7', dark:'#166534' },
                        warning: { DEFAULT:'#d97706', light:'#fef3c7', dark:'#92400e' },
                        danger: { DEFAULT:'#dc2626', light:'#fef2f2', dark:'#991b1b' },
                        ndr: { DEFAULT:'#ea580c', light:'#fff7ed', dark:'#9a3412' },
                        rto: { DEFAULT:'#9333ea', light:'#f5f3ff', dark:'#6b21a8' },
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @yield('head')
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: true, mobileSidebar: false }">

    {{-- Mobile overlay --}}
    <div x-show="mobileSidebar" x-cloak @click="mobileSidebar = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Main content --}}
    <div class="lg:ml-64 min-h-screen transition-all duration-300" :class="{ 'lg:ml-0': !sidebarOpen }">
        @include('components.topbar')

        <main class="p-4 md:p-6 lg:p-8">
            @include('components.flash-messages')
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
