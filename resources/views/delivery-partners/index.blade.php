@extends('layouts.app')
@section('title', 'Delivery Partners')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Delivery Partners</h1>
            <p class="text-sm text-gray-500 mt-1">Manage last-mile delivery partners and fleet.</p>
        </div>
        <button class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">+ Add Partner</button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partner ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deliveries Today</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $partners = [
                            ['id' => 'DP-001', 'name' => 'Ramesh Verma', 'phone' => '+91 98765 11111', 'zone' => 'Andheri, Mumbai', 'deliveries' => 28, 'rating' => '4.8', 'status' => 'active'],
                            ['id' => 'DP-002', 'name' => 'Suresh Kumar', 'phone' => '+91 98765 22222', 'zone' => 'Sector 62, Noida', 'deliveries' => 35, 'rating' => '4.6', 'status' => 'active'],
                            ['id' => 'DP-003', 'name' => 'Ajay Singh', 'phone' => '+91 98765 33333', 'zone' => 'Koramangala, Bangalore', 'deliveries' => 22, 'rating' => '4.9', 'status' => 'active'],
                            ['id' => 'DP-004', 'name' => 'Manoj Patel', 'phone' => '+91 98765 44444', 'zone' => 'T Nagar, Chennai', 'deliveries' => 0, 'rating' => '4.2', 'status' => 'inactive'],
                        ];
                    @endphp
                    @foreach($partners as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $p['id'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $p['name'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $p['phone'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $p['zone'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $p['deliveries'] }}</td>
                            <td class="px-6 py-3.5 text-sm">
                                <span class="flex items-center gap-1 text-amber-600"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> {{ $p['rating'] }}</span>
                            </td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $p['status']])</td>
                            <td class="px-6 py-3.5 text-right">
                                <button class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
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
