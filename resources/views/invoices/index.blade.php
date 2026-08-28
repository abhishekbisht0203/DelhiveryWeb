@extends('layouts.app')
@section('title', 'Invoices')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
            <p class="text-sm text-gray-500 mt-1">Manage billing invoices and payments.</p>
        </div>
        <button class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">+ Generate Invoice</button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @include('components.stat-card', ['title' => 'Total Invoices', 'value' => '89', 'color' => 'primary'])
        @include('components.stat-card', ['title' => 'Paid', 'value' => '72', 'color' => 'success'])
        @include('components.stat-card', ['title' => 'Pending', 'value' => '12', 'color' => 'warning'])
        @include('components.stat-card', ['title' => 'Overdue', 'value' => '5', 'color' => 'danger'])
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merchant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $invoices = [
                            ['num' => 'INV-2026-089', 'merchant' => 'Fashion Hub Pvt Ltd', 'period' => 'Aug 2026', 'amount' => '₹1,24,500', 'tax' => '₹22,410', 'total' => '₹1,46,910', 'status' => 'pending'],
                            ['num' => 'INV-2026-088', 'merchant' => 'Tech Gadgets Online', 'period' => 'Aug 2026', 'amount' => '₹89,200', 'tax' => '₹16,056', 'total' => '₹1,05,256', 'status' => 'paid'],
                            ['num' => 'INV-2026-087', 'merchant' => 'Organic Store', 'period' => 'Jul 2026', 'amount' => '₹67,800', 'tax' => '₹12,204', 'total' => '₹80,004', 'status' => 'paid'],
                            ['num' => 'INV-2026-086', 'merchant' => 'Gift Gallery', 'period' => 'Jul 2026', 'amount' => '₹23,400', 'tax' => '₹4,212', 'total' => '₹27,612', 'status' => 'overdue'],
                        ];
                    @endphp
                    @foreach($invoices as $i)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $i['num'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $i['merchant'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $i['period'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $i['amount'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $i['tax'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-semibold text-gray-900">{{ $i['total'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $i['status']])</td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    <button class="px-2.5 py-1 text-xs font-medium text-primary-600 bg-primary-50 rounded hover:bg-primary-100">Download</button>
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
