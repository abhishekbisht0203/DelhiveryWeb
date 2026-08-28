@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Welcome back. Here's your operations overview.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="date" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-primary-500" value="{{ date('Y-m-d') }}">
            <a href="{{ route('shipments.create') }}" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">+ New Shipment</a>
        </div>
    </div>

    {{-- Stats Row 1 --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @include('components.stat-card', ['title' => 'Total Shipments', 'value' => '12,458', 'color' => 'primary', 'icon' => '&#128230;', 'change' => '+12.5% this week', 'changeType' => 'up'])
        @include('components.stat-card', ['title' => "Today's Shipments", 'value' => '342', 'color' => 'blue', 'icon' => '&#128197;', 'change' => '+8.2% vs yesterday', 'changeType' => 'up'])
        @include('components.stat-card', ['title' => 'In Transit', 'value' => '1,856', 'color' => 'primary', 'icon' => '&#128666;', 'change' => '68.5% of total', 'changeType' => ''])
        @include('components.stat-card', ['title' => 'Out for Delivery', 'value' => '423', 'color' => 'purple', 'icon' => '&#128722;', 'change' => '3.4% of total', 'changeType' => ''])
        @include('components.stat-card', ['title' => 'Delivered Today', 'value' => '298', 'color' => 'success', 'icon' => '&#9989;', 'change' => '+15.3% vs yesterday', 'changeType' => 'up'])
    </div>

    {{-- Stats Row 2 --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @include('components.stat-card', ['title' => 'Pending Pickup', 'value' => '87', 'color' => 'warning', 'icon' => '&#128205;'])
        @include('components.stat-card', ['title' => 'Pickup Failed', 'value' => '12', 'color' => 'danger', 'icon' => '&#10060;'])
        @include('components.stat-card', ['title' => 'NDR Cases', 'value' => '64', 'color' => 'warning', 'icon' => '&#9888;'])
        @include('components.stat-card', ['title' => 'RTO', 'value' => '23', 'color' => 'purple', 'icon' => '&#128260;'])
        @include('components.stat-card', ['title' => 'Cancelled', 'value' => '18', 'color' => 'danger', 'icon' => '&#128683;'])
    </div>

    {{-- Stats Row 3 --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @include('components.stat-card', ['title' => 'COD Total', 'value' => '₹4,52,800', 'color' => 'primary', 'icon' => '&#128176;'])
        @include('components.stat-card', ['title' => 'COD Pending', 'value' => '₹1,25,400', 'color' => 'warning', 'icon' => '&#9203;'])
        @include('components.stat-card', ['title' => 'Revenue', 'value' => '₹8,34,200', 'color' => 'success', 'icon' => '&#128200;', 'change' => '+18.7% this month', 'changeType' => 'up'])
        @include('components.stat-card', ['title' => 'Delivery Rate', 'value' => '94.2%', 'color' => 'success', 'icon' => '&#128202;'])
        @include('components.stat-card', ['title' => 'Failed Rate', 'value' => '3.8%', 'color' => 'danger', 'icon' => '&#128201;', 'change' => '-0.5% vs last week', 'changeType' => 'down'])
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Daily Shipments Bar Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Daily Shipments</h3>
            <canvas id="dailyShipmentsChart" height="120"></canvas>
        </div>

        {{-- Status Distribution Pie Chart --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Status Distribution</h3>
            <canvas id="statusChart" height="200"></canvas>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between text-xs"><span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Delivered</span><span class="font-medium">45%</span></div>
                <div class="flex items-center justify-between text-xs"><span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> In Transit</span><span class="font-medium">28%</span></div>
                <div class="flex items-center justify-between text-xs"><span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Pending</span><span class="font-medium">12%</span></div>
                <div class="flex items-center justify-between text-xs"><span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span> NDR</span><span class="font-medium">8%</span></div>
                <div class="flex items-center justify-between text-xs"><span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Failed</span><span class="font-medium">7%</span></div>
            </div>
        </div>
    </div>

    {{-- COD Collection Line Chart --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">COD Collection Trend</h3>
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary-500"></span> Collected</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Pending</span>
            </div>
        </div>
        <canvas id="codCollectionChart" height="80"></canvas>
    </div>

    {{-- Recent Shipments --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Recent Shipments</h3>
            <a href="{{ route('shipments.index') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AWB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consignee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">COD Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $recentShipments = [
                            ['awb' => 'DLV0012345', 'order' => 'ORD-78901', 'consignee' => 'Rahul Sharma', 'status' => 'delivered', 'payment' => 'COD', 'cod' => '₹1,250', 'created' => '2 min ago'],
                            ['awb' => 'DLV0012344', 'order' => 'ORD-78900', 'consignee' => 'Priya Patel', 'status' => 'in_transit', 'payment' => 'Prepaid', 'cod' => '-', 'created' => '5 min ago'],
                            ['awb' => 'DLV0012343', 'order' => 'ORD-78899', 'consignee' => 'Amit Kumar', 'status' => 'out_for_delivery', 'payment' => 'COD', 'cod' => '₹890', 'created' => '12 min ago'],
                            ['awb' => 'DLV0012342', 'order' => 'ORD-78898', 'consignee' => 'Neha Gupta', 'status' => 'ndr', 'payment' => 'COD', 'cod' => '₹2,100', 'created' => '18 min ago'],
                            ['awb' => 'DLV0012341', 'order' => 'ORD-78897', 'consignee' => 'Vikram Singh', 'status' => 'pending', 'payment' => 'Prepaid', 'cod' => '-', 'created' => '25 min ago'],
                        ];
                    @endphp
                    @foreach($recentShipments as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $s['awb'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $s['order'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $s['consignee'] }}</td>
                            <td class="px-6 py-3.5">
                                @include('components.status-badge', ['status' => $s['status']])
                            </td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $s['payment'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $s['cod'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $s['created'] }}</td>
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
    // Daily Shipments Bar Chart
    new Chart(document.getElementById('dailyShipmentsChart'), {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Shipments',
                data: [320, 445, 380, 520, 490, 380, 342],
                backgroundColor: '#3b82f6',
                borderRadius: 6,
                barThickness: 28,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });

    // Status Distribution Pie Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Delivered', 'In Transit', 'Pending', 'NDR', 'Failed'],
            datasets: [{
                data: [45, 28, 12, 8, 7],
                backgroundColor: ['#16a34a', '#3b82f6', '#eab308', '#f97316', '#dc2626'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    // COD Collection Line Chart
    new Chart(document.getElementById('codCollectionChart'), {
        type: 'line',
        data: {
            labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14'],
            datasets: [{
                label: 'Collected',
                data: [42000, 38000, 55000, 47000, 61000, 52000, 48000, 63000, 57000, 71000, 65000, 58000, 72000, 68000],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
            }, {
                label: 'Pending',
                data: [12000, 15000, 8000, 11000, 9000, 14000, 16000, 10000, 13000, 7000, 11000, 15000, 9000, 12000],
                borderColor: '#d97706',
                backgroundColor: 'rgba(217,119,6,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                borderDash: [5, 5],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { callback: v => '₹' + (v/1000) + 'k' } }, x: { grid: { display: false } } }
        }
    });
</script>
@endsection
