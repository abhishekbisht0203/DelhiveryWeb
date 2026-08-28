@extends('layouts.app')
@section('title', 'Delivery Reports')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('reports.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Delivery Reports</h1>
            <p class="text-sm text-gray-500 mt-1">Track delivery performance, success rates, and failure reasons.</p>
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
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Hub</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All Hubs</option>
                    <option>HUB-Mumbai-01</option>
                    <option>HUB-Delhi-02</option>
                    <option>HUB-Bangalore-03</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Delivery Partner</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All Partners</option>
                    <option>BlueDart</option>
                    <option>DTDC</option>
                    <option>Shadowfax</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">City</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All Cities</option>
                    <option>Mumbai</option>
                    <option>Delhi</option>
                    <option>Bangalore</option>
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
        @include('components.stat-card', ['title' => 'First Attempt', 'value' => '89%', 'color' => 'success'])
        @include('components.stat-card', ['title' => 'Reattempts', 'value' => '1,042', 'color' => 'blue'])
        @include('components.stat-card', ['title' => 'Avg Delivery Time', 'value' => '3.2 days', 'color' => 'primary'])
        @include('components.stat-card', ['title' => 'Failed Attempts', 'value' => '486', 'color' => 'danger'])
        @include('components.stat-card', ['title' => 'Success Rate', 'value' => '94.2%', 'color' => 'success'])
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Delivery Trend (Daily)</h3>
            <canvas id="deliveryTrendChart" height="150"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Failure Reasons</h3>
            <canvas id="failureReasonsChart" height="150"></canvas>
        </div>
    </div>

    {{-- Hub-wise Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Hub-wise Performance</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hub</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deliveries</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Success</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Failed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg TAT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $hubData = [
                            ['hub' => 'HUB-Mumbai-01', 'deliveries' => 3840, 'success' => 3620, 'failed' => 120, 'tat' => '2.8 days', 'rating' => 4.5],
                            ['hub' => 'HUB-Delhi-02', 'deliveries' => 3560, 'success' => 3340, 'failed' => 140, 'tat' => '3.0 days', 'rating' => 4.3],
                            ['hub' => 'HUB-Bangalore-03', 'deliveries' => 2914, 'success' => 2750, 'failed' => 96, 'tat' => '3.2 days', 'rating' => 4.6],
                            ['hub' => 'HUB-Chennai-04', 'deliveries' => 2144, 'success' => 2010, 'failed' => 130, 'tat' => '3.5 days', 'rating' => 4.1],
                        ];
                    @endphp
                    @foreach($hubData as $h)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $h['hub'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ number_format($h['deliveries']) }}</td>
                            <td class="px-6 py-3.5 text-sm text-green-600 font-medium">{{ number_format($h['success']) }}</td>
                            <td class="px-6 py-3.5 text-sm text-red-600">{{ number_format($h['failed']) }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $h['tat'] }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    <span class="text-sm font-medium text-gray-900">{{ $h['rating'] }}</span>
                                </div>
                            </td>
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
    new Chart(document.getElementById('deliveryTrendChart'), {
        type: 'line',
        data: {
            labels: ['1 Aug', '5 Aug', '10 Aug', '15 Aug', '20 Aug', '25 Aug', '28 Aug'],
            datasets: [
                { label: 'Delivered', data: [310, 420, 495, 465, 385, 358, 320], borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.1)', fill: true, tension: 0.4, borderWidth: 2 },
                { label: 'Failed', data: [12, 18, 22, 15, 20, 14, 16], borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.1)', fill: true, tension: 0.4, borderWidth: 2 }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('failureReasonsChart'), {
        type: 'doughnut',
        data: {
            labels: ['Address Issue', 'No Response', 'Refused', 'Wrong Address', 'Others'],
            datasets: [{
                data: [145, 120, 98, 78, 45],
                backgroundColor: ['#3b82f6', '#eab308', '#dc2626', '#9333ea', '#6b7280'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right', labels: { padding: 16, usePointStyle: true } } }
        }
    });
</script>
@endsection
