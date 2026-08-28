@extends('layouts.app')
@section('title', 'Shipments')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Shipments</h1>
            <p class="text-sm text-gray-500 mt-1">Manage all your shipments in one place.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.bulk-upload') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Bulk Upload</a>
            <a href="{{ route('shipments.create') }}" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">+ New Shipment</a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('shipments.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="AWB, Order ID..."
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="ndr" {{ request('status') === 'ndr' ? 'selected' : '' }}>NDR</option>
                        <option value="rto" {{ request('status') === 'rto' ? 'selected' : '' }}>RTO</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Payment</label>
                    <select name="payment_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All</option>
                        <option value="cod" {{ request('payment_type') === 'cod' ? 'selected' : '' }}>COD</option>
                        <option value="prepaid" {{ request('payment_type') === 'prepaid' ? 'selected' : '' }}>Prepaid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Filter</button>
                    <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AWB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consignee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">COD</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $shipments = [
                            ['awb' => 'DLV0012345', 'order' => 'ORD-78901', 'name' => 'Rahul Sharma', 'dest' => 'Mumbai, MH', 'status' => 'delivered', 'payment' => 'COD', 'cod' => '₹1,250', 'date' => '28 Aug 2026'],
                            ['awb' => 'DLV0012344', 'order' => 'ORD-78900', 'name' => 'Priya Patel', 'dest' => 'Delhi, DL', 'status' => 'in_transit', 'payment' => 'Prepaid', 'cod' => '-', 'date' => '28 Aug 2026'],
                            ['awb' => 'DLV0012343', 'order' => 'ORD-78899', 'name' => 'Amit Kumar', 'dest' => 'Bangalore, KA', 'status' => 'out_for_delivery', 'payment' => 'COD', 'cod' => '₹890', 'date' => '28 Aug 2026'],
                            ['awb' => 'DLV0012342', 'order' => 'ORD-78898', 'name' => 'Neha Gupta', 'dest' => 'Chennai, TN', 'status' => 'ndr', 'payment' => 'COD', 'cod' => '₹2,100', 'date' => '27 Aug 2026'],
                            ['awb' => 'DLV0012341', 'order' => 'ORD-78897', 'name' => 'Vikram Singh', 'dest' => 'Hyderabad, TS', 'status' => 'pending', 'payment' => 'Prepaid', 'cod' => '-', 'date' => '27 Aug 2026'],
                            ['awb' => 'DLV0012340', 'order' => 'ORD-78896', 'name' => 'Sneha Reddy', 'dest' => 'Pune, MH', 'status' => 'delivered', 'payment' => 'COD', 'cod' => '₹3,500', 'date' => '27 Aug 2026'],
                            ['awb' => 'DLV0012339', 'order' => 'ORD-78895', 'name' => 'Rajesh Nair', 'dest' => 'Kochi, KL', 'status' => 'failed', 'payment' => 'COD', 'cod' => '₹750', 'date' => '26 Aug 2026'],
                        ];
                    @endphp
                    @foreach($shipments as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5">
                                <a href="{{ route('shipments.show', $s['awb']) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ $s['awb'] }}</a>
                            </td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $s['order'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $s['name'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $s['dest'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $s['status']])</td>
                            <td class="px-6 py-3.5 text-sm {{ $s['payment'] === 'COD' ? 'text-amber-600 font-medium' : 'text-gray-600' }}">{{ $s['payment'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $s['cod'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $s['date'] }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('shipments.show', $s['awb']) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('shipments.edit', $s['awb']) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            @include('components.pagination')
        </div>
    </div>
</div>
@endsection
