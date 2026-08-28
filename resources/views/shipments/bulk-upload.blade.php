@extends('layouts.app')
@section('title', 'Bulk Upload Shipments')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('shipments.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bulk Upload Shipments</h1>
            <p class="text-sm text-gray-500 mt-1">Upload multiple shipments using a CSV or Excel file.</p>
        </div>
    </div>

    @include('components.flash-messages')

    {{-- Download Template --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Download Template</h3>
                <p class="text-sm text-gray-500 mt-1">Download the template file and fill in shipment details.</p>
            </div>
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download CSV Template
            </a>
        </div>
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-xs font-medium text-gray-700 mb-2">Required Columns:</p>
            <div class="flex flex-wrap gap-2">
                @foreach(['order_id', 'consignee_name', 'consignee_phone', 'address', 'city', 'state', 'pincode', 'weight', 'payment_type', 'cod_amount'] as $col)
                    <span class="px-2.5 py-1 bg-white border border-gray-200 rounded text-xs text-gray-600">{{ $col }}</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Upload --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Upload File</h3>
        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary-400 transition-colors" x-data="{ dragover: false }"
             @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="dragover = false; $refs.fileInput.files = $event.dataTransfer.files"
             :class="{ 'border-primary-400 bg-primary-50': dragover }">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <p class="mt-3 text-sm text-gray-600">Drag and drop your CSV or Excel file here, or <span class="text-primary-600 font-medium cursor-pointer" onclick="document.getElementById('fileInput').click()">browse</span></p>
            <p class="mt-1 text-xs text-gray-400">Supports .csv, .xlsx (Max 10MB, up to 1000 rows)</p>
            <input type="file" id="fileInput" x-ref="fileInput" name="file" accept=".csv,.xlsx,.xls" class="hidden">
        </div>
        <div class="mt-4 flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Upload & Process</button>
        </div>
    </div>

    {{-- Recent Uploads --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Recent Uploads</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rows</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Success</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Failed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $uploads = [
                            ['file' => 'bulk_shipments_28Aug.csv', 'user' => 'Admin', 'rows' => 150, 'success' => 145, 'failed' => 5, 'status' => 'completed', 'date' => '28 Aug 2026, 2:30 PM'],
                            ['file' => 'orders_batch_27Aug.xlsx', 'user' => 'Rajesh', 'rows' => 89, 'success' => 89, 'failed' => 0, 'status' => 'completed', 'date' => '27 Aug 2026, 11:00 AM'],
                            ['file' => 'festive_sale_data.csv', 'user' => 'Priya', 'rows' => 320, 'success' => 310, 'failed' => 10, 'status' => 'completed', 'date' => '26 Aug 2026, 4:15 PM'],
                        ];
                    @endphp
                    @foreach($uploads as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $u['file'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $u['user'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $u['rows'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-green-600 font-medium">{{ $u['success'] }}</td>
                            <td class="px-6 py-3.5 text-sm {{ $u['failed'] > 0 ? 'text-red-600 font-medium' : 'text-gray-600' }}">{{ $u['failed'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $u['status']])</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $u['date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
