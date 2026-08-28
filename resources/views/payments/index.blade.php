@extends('layouts.app')
@section('title', 'COD Collection')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">COD Collection</h1>
            <p class="text-sm text-gray-500 mt-1">Cash on Delivery collection and reconciliation.</p>
        </div>
        <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Export</button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @include('components.stat-card', ['title' => 'Total COD', 'value' => '₹4,52,800', 'color' => 'primary'])
        @include('components.stat-card', ['title' => 'Collected', 'value' => '₹3,27,400', 'color' => 'success'])
        @include('components.stat-card', ['title' => 'Pending', 'value' => '₹1,25,400', 'color' => 'warning'])
        @include('components.stat-card', ['title' => 'Remittance Due', 'value' => '₹89,200', 'color' => 'purple'])
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AWB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merchant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">COD Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collected On</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collected By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $payments = [
                            ['awb' => 'DLV0012345', 'order' => 'ORD-78901', 'merchant' => 'Fashion Hub', 'amount' => '₹1,250', 'date' => '28 Aug', 'by' => 'Ramesh Verma', 'status' => 'delivered'],
                            ['awb' => 'DLV0012340', 'order' => 'ORD-78896', 'merchant' => 'Fashion Hub', 'amount' => '₹3,500', 'date' => '27 Aug', 'by' => 'Suresh Kumar', 'status' => 'delivered'],
                            ['awb' => 'DLV0012343', 'order' => 'ORD-78899', 'merchant' => 'Tech Gadgets', 'amount' => '₹890', 'date' => '', 'by' => '-', 'status' => 'pending'],
                            ['awb' => 'DLV0012342', 'order' => 'ORD-78898', 'merchant' => 'Organic Store', 'amount' => '₹2,100', 'date' => '', 'by' => '-', 'status' => 'pending'],
                        ];
                    @endphp
                    @foreach($payments as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $p['awb'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $p['order'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $p['merchant'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $p['amount'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $p['date'] ?: '-' }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $p['by'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $p['status']])</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">@include('components.pagination')</div>
    </div>
</div>
@endsection
