@extends('layouts.app')
@section('title', 'Shipment Reports')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('reports.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Shipment Reports</h1>
            <p class="text-sm text-gray-500 mt-1">Detailed shipment analytics and trends.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date Range</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option>Last 7 Days</option>
                    <option selected>Last 30 Days</option>
                    <option>Last 90 Days</option>
                    <option>This Month</option>
                    <option>Last Month</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Merchant</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All Merchants</option>
                    <option>Fashion Hub</option>
                    <option>Tech Gadgets</option>
                    <option>Organic Store</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All</option>
                    <option>Delivered</option>
                    <option>In Transit</option>
                    <option>Failed</option>
                    <option>RTO</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Payment</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All</option>
                    <option>COD</option>
                    <option>Prepaid</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">Apply</button>
                <button class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Export</button>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @include('components.stat-card', ['title' => 'Total', 'value' => '12,458', 'color' => 'primary'])
        @include('components.stat-card', ['title' => 'Delivered', 'value' => '11,314', 'color' => 'success'])
        @include('components.stat-card', ['title' => 'In Transit', 'value' => '856', 'color' => 'blue'])
        @include('components.stat-card', ['title' => 'Failed', 'value' => '156', 'color' => 'danger'])
        @include('components.stat-card', ['title' => 'RTO', 'value' => '132', 'color' => 'purple'])
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Daily Shipment Volume</h3>
            <canvas id="shipmentVolumeChart" height="150"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Status Breakdown</h3>
            <canvas id="statusBreakdownChart" height="150"></canvas>
        </div>
    </div>

    {{-- Merchant-wise Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Merchant-wise Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merchant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">In Transit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Failed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Success %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $merchantData = [
                            ['name' => 'Fashion Hub Pvt Ltd', 'total' => 4520, 'delivered' => 4290, 'transit' => 180, 'failed' => 50, 'rate' => '94.9%'],
                            ['name' => 'Tech Gadgets Online', 'total' => 3210, 'delivered' => 2980, 'transit' => 150, 'failed' => 80, 'rate' => '92.8%'],
                            ['name' => 'Organic Store', 'total' => 1890, 'delivered' => 1810, 'transit' => 56, 'failed' => 24, 'rate' => '95.8%'],
                            ['name' => 'Gift Gallery', 'total' => 560, 'delivered' => 520, 'transit' => 28, 'failed' => 12, 'rate' => '92.9%'],
                        ];
                    @endphp
                    @foreach($merchantData as $m)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $m['name'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ number_format($m['total']) }}</td>
                            <td class="px-6 py-3.5 text-sm text-green-600 font-medium">{{ number_format($m['delivered']) }}</td>
                            <td class="px-6 py-3.5 text-sm text-blue-600">{{ number_format($m['transit']) }}</td>
                            <td class="px-6 py-3.5 text-sm text-red-600">{{ number_format($m['failed']) }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $m['rate'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Chart(document.getElementById('shipmentVolumeChart'), {
        type: 'line',
        data: {
            labels: ['1 Aug', '5 Aug', '10 Aug', '15 Aug', '20 Aug', '25 Aug', '28 Aug'],
            datasets: [{
                label: 'Shipments',
                data: [320, 445, 520, 490, 410, 380, 342],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('statusBreakdownChart'), {
        type: 'bar',
        data: {
            labels: ['Delivered', 'In Transit', 'Pending', 'Failed', 'RTO'],
            datasets: [{
                data: [11314, 856, 320, 156, 132],
                backgroundColor: ['#16a34a', '#3b82f6', '#eab308', '#dc2626', '#9333ea'],
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#f3f4f6' } }, y: { grid: { display: false } } }
        }
    });
</script>
@endsection
