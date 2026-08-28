@extends('layouts.app')
@section('title', 'COD Reports')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('reports.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">COD Reports</h1>
            <p class="text-sm text-gray-500 mt-1">Cash on Delivery collection analytics and reconciliation.</p>
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
                <label class="block text-xs font-medium text-gray-500 mb-1">Merchant</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All Merchants</option>
                    <option>Fashion Hub</option>
                    <option>Tech Gadgets</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Payment Status</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All</option>
                    <option>Collected</option>
                    <option>Pending</option>
                    <option>Overdue</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Remittance</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                    <option value="">All</option>
                    <option>Remitted</option>
                    <option>Pending</option>
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
        @include('components.stat-card', ['title' => 'Total COD', 'value' => '₹24,56,800', 'color' => 'primary'])
        @include('components.stat-card', ['title' => 'Collected', 'value' => '₹21,23,500', 'color' => 'success'])
        @include('components.stat-card', ['title' => 'Pending', 'value' => '₹2,18,300', 'color' => 'warning'])
        @include('components.stat-card', ['title' => 'Overdue', 'value' => '₹1,15,000', 'color' => 'danger'])
        @include('components.stat-card', ['title' => 'Remitted', 'value' => '₹18,90,000', 'color' => 'blue'])
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">COD Collection Trend</h3>
            <canvas id="codCollectionTrend" height="150"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Collection by City</h3>
            <canvas id="codByCityChart" height="150"></canvas>
        </div>
    </div>

    {{-- Collection Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Pending COD Collections</h3>
            <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-full">87 shipments pending</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AWB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merchant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consignee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivered On</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Pending</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $codData = [
                            ['awb' => 'DLV0028456', 'merchant' => 'Fashion Hub', 'consignee' => 'Amit Sharma', 'amount' => 1850, 'delivered' => '22 Aug', 'status' => 'overdue', 'days' => 6],
                            ['awb' => 'DLV0028321', 'merchant' => 'Tech Gadgets', 'consignee' => 'Priya Patel', 'amount' => 3200, 'delivered' => '25 Aug', 'status' => 'pending', 'days' => 3],
                            ['awb' => 'DLV0028290', 'merchant' => 'Fashion Hub', 'consignee' => 'Raj Kumar', 'amount' => 890, 'delivered' => '26 Aug', 'status' => 'pending', 'days' => 2],
                            ['awb' => 'DLV0028155', 'merchant' => 'Organic Store', 'consignee' => 'Neha Gupta', 'amount' => 2450, 'delivered' => '24 Aug', 'status' => 'overdue', 'days' => 4],
                            ['awb' => 'DLV0028080', 'merchant' => 'Tech Gadgets', 'consignee' => 'Suresh Reddy', 'amount' => 5600, 'delivered' => '27 Aug', 'status' => 'pending', 'days' => 1],
                        ];
                    @endphp
                    @foreach($codData as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $c['awb'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $c['merchant'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $c['consignee'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">₹{{ number_format($c['amount']) }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $c['delivered'] }}</td>
                            <td class="px-6 py-3.5">
                                @include('components.status-badge', ['status' => $c['status']])
                            </td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $c['days'] }} days</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">@include('components.pagination')</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Chart(document.getElementById('codCollectionTrend'), {
        type: 'line',
        data: {
            labels: ['1 Aug', '5 Aug', '10 Aug', '15 Aug', '20 Aug', '25 Aug', '28 Aug'],
            datasets: [
                { label: 'Collected', data: [780000, 920000, 1100000, 980000, 850000, 780000, 720000], borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.1)', fill: true, tension: 0.4, borderWidth: 2 },
                { label: 'Pending', data: [120000, 85000, 95000, 110000, 130000, 98000, 88000], borderColor: '#d97706', backgroundColor: 'rgba(217,119,6,0.1)', fill: true, tension: 0.4, borderWidth: 2 }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { callback: v => '₹' + (v/100000).toFixed(1) + 'L' } },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('codByCityChart'), {
        type: 'bar',
        data: {
            labels: ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Hyderabad', 'Pune'],
            datasets: [
                { label: 'Collected', data: [520000, 480000, 390000, 310000, 240000, 180000], backgroundColor: '#16a34a', borderRadius: 6 },
                { label: 'Pending', data: [48000, 42000, 38000, 32000, 28000, 30000], backgroundColor: '#d97706', borderRadius: 6 }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true, stacked: true, grid: { color: '#f3f4f6' }, ticks: { callback: v => '₹' + (v/100000).toFixed(1) + 'L' } },
                x: { stacked: true, grid: { display: false } }
            }
        }
    });
</script>
@endsection
