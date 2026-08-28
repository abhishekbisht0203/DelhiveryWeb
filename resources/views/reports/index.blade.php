@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
        <p class="text-sm text-gray-500 mt-1">Comprehensive analytics and reports for your logistics operations.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $reportCards = [
                ['title' => 'Shipment Reports', 'description' => 'Detailed shipment analytics, status distribution, and trends.', 'icon' => '&#128230;', 'color' => 'primary', 'url' => route('reports.shipment')],
                ['title' => 'Delivery Reports', 'description' => 'Delivery performance, success rates, and SLA compliance.', 'icon' => '&#128666;', 'color' => 'success', 'url' => route('reports.delivery')],
                ['title' => 'COD Reports', 'description' => 'Cash on Delivery collection, reconciliation, and remittance.', 'icon' => '&#128176;', 'color' => 'warning', 'url' => route('reports.cod')],
                ['title' => 'NDR Reports', 'description' => 'Non-Delivery analysis, reasons breakdown, and resolution rates.', 'icon' => '&#9888;', 'color' => 'danger', 'url' => '#'],
                ['title' => 'RTO Reports', 'description' => 'Return to Origin analytics and merchant-wise breakdown.', 'icon' => '&#128260;', 'color' => 'purple', 'url' => '#'],
                ['title' => 'Merchant Reports', 'description' => 'Merchant-wise performance, shipment volumes, and revenue.', 'icon' => '&#127970;', 'color' => 'blue', 'url' => '#'],
            ];
        @endphp
        @foreach($reportCards as $r)
            <a href="{{ $r['url'] }}" class="block bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start gap-4">
                    <div class="text-3xl">{!! $r['icon'] !!}</div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $r['title'] }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $r['description'] }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Quick Stats --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Overview - Last 30 Days</h3>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="text-center"><p class="text-2xl font-bold text-gray-900">12,458</p><p class="text-xs text-gray-500 mt-1">Total Shipments</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-green-600">94.2%</p><p class="text-xs text-gray-500 mt-1">Delivery Rate</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-blue-600">2.8 days</p><p class="text-xs text-gray-500 mt-1">Avg TAT</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-amber-600">₹4.5L</p><p class="text-xs text-gray-500 mt-1">COD Collected</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-purple-600">3.8%</p><p class="text-xs text-gray-500 mt-1">NDR Rate</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-red-600">1.2%</p><p class="text-xs text-gray-500 mt-1">RTO Rate</p></div>
        </div>
    </div>
</div>
@endsection
