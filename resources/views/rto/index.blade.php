@extends('layouts.app')
@section('title', 'RTO Shipments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">RTO Shipments</h1>
            <p class="text-sm text-gray-500 mt-1">Return to Origin shipments tracking.</p>
        </div>
        <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Export</button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @include('components.stat-card', ['title' => 'Total RTO', 'value' => '23', 'color' => 'purple'])
        @include('components.stat-card', ['title' => 'In Transit Back', 'value' => '12', 'color' => 'blue'])
        @include('components.stat-card', ['title' => 'Returned', 'value' => '8', 'color' => 'success'])
        @include('components.stat-card', ['title' => 'Pending', 'value' => '3', 'color' => 'warning'])
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AWB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consignee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RTO Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">COD Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RTO Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $rto = [
                            ['awb' => 'DLV0012310', 'order' => 'ORD-78870', 'name' => 'Kiran Rao', 'reason' => 'Customer refused', 'cod' => '₹1,800', 'date' => '26 Aug', 'status' => 'in_transit'],
                            ['awb' => 'DLV0012295', 'order' => 'ORD-78855', 'name' => 'Pooja Desai', 'reason' => 'NDR - Unreachable', 'cod' => '₹2,400', 'date' => '25 Aug', 'status' => 'returned'],
                            ['awb' => 'DLV0012280', 'order' => 'ORD-78840', 'name' => 'Vikram Joshi', 'reason' => 'Incomplete address', 'cod' => '₹950', 'date' => '25 Aug', 'status' => 'returned'],
                            ['awb' => 'DLV0012265', 'order' => 'ORD-78825', 'name' => 'Meera Iyer', 'reason' => 'Customer refused', 'cod' => '₹3,200', 'date' => '24 Aug', 'status' => 'pending'],
                        ];
                    @endphp
                    @foreach($rto as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $r['awb'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $r['order'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $r['name'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $r['reason'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-amber-600">{{ $r['cod'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $r['date'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $r['status']])</td>
                            <td class="px-6 py-3.5 text-right">
                                <button class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">@include('components.pagination')</div>
    </div>
</div>
@endsection
