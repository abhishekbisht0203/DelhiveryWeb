@extends('layouts.app')
@section('title', 'NDR Cases')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">NDR Cases</h1>
            <p class="text-sm text-gray-500 mt-1">Non-Delivery Report cases requiring action.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Export</button>
            <button class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">Bulk RTO</button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @include('components.stat-card', ['title' => 'Open NDR', 'value' => '64', 'color' => 'warning'])
        @include('components.stat-card', ['title' => 'Reattempted', 'value' => '28', 'color' => 'blue'])
        @include('components.stat-card', ['title' => 'RTO Initiated', 'value' => '18', 'color' => 'purple'])
        @include('components.stat-card', ['title' => 'Resolved', 'value' => '18', 'color' => 'success'])
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" class="w-4 h-4 text-primary-600 rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AWB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consignee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attempts</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $ndrCases = [
                            ['awb' => 'DLV0012342', 'name' => 'Neha Gupta', 'reason' => 'Customer not available', 'attempts' => 2, 'date' => '27 Aug', 'status' => 'open'],
                            ['awb' => 'DLV0012335', 'name' => 'Arun Menon', 'reason' => 'Address incomplete', 'attempts' => 1, 'date' => '27 Aug', 'status' => 'open'],
                            ['awb' => 'DLV0012320', 'name' => 'Deepa Joshi', 'reason' => 'Phone switched off', 'attempts' => 3, 'date' => '26 Aug', 'status' => 'open'],
                            ['awb' => 'DLV0012310', 'name' => 'Kiran Rao', 'reason' => 'Customer refused delivery', 'attempts' => 1, 'date' => '26 Aug', 'status' => 'rto'],
                            ['awb' => 'DLV0012298', 'name' => 'Sanjay Mishra', 'reason' => 'Wrong pincode', 'attempts' => 2, 'date' => '25 Aug', 'status' => 'resolved'],
                        ];
                    @endphp
                    @foreach($ndrCases as $n)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5"><input type="checkbox" class="w-4 h-4 text-primary-600 rounded"></td>
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $n['awb'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $n['name'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $n['reason'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $n['attempts'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $n['date'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $n['status']])</td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button class="px-2.5 py-1 text-xs font-medium text-primary-600 bg-primary-50 rounded hover:bg-primary-100">Reattempt</button>
                                    <button class="px-2.5 py-1 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100">RTO</button>
                                </div>
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
