@extends('layouts.app')
@section('title', 'Pickups')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pickups</h1>
            <p class="text-sm text-gray-500 mt-1">Manage pickup schedules and tracking.</p>
        </div>
        <button class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">+ Schedule Pickup</button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" placeholder="Pickup ID..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All</option>
                        <option>Pending</option>
                        <option>Scheduled</option>
                        <option>In Progress</option>
                        <option>Completed</option>
                        <option>Failed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                    <input type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pickup ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merchant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shipment Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheduled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $pickups = [
                            ['id' => 'PKP-001', 'merchant' => 'Fashion Hub Pvt Ltd', 'address' => 'Andheri East, Mumbai', 'count' => 45, 'scheduled' => '28 Aug, 2:00 PM', 'status' => 'completed'],
                            ['id' => 'PKP-002', 'merchant' => 'Tech Gadgets Online', 'address' => 'Sector 5, Noida', 'count' => 32, 'scheduled' => '28 Aug, 4:00 PM', 'status' => 'in_transit'],
                            ['id' => 'PKP-003', 'merchant' => 'Organic Store', 'address' => 'Koramangala, Bangalore', 'count' => 18, 'scheduled' => '29 Aug, 10:00 AM', 'status' => 'pending'],
                            ['id' => 'PKP-004', 'merchant' => 'Fashion Hub Pvt Ltd', 'address' => 'Andheri East, Mumbai', 'count' => 28, 'scheduled' => '27 Aug, 3:00 PM', 'status' => 'completed'],
                            ['id' => 'PKP-005', 'merchant' => 'Gift Gallery', 'address' => 'Salt Lake, Kolkata', 'count' => 12, 'scheduled' => '27 Aug, 11:00 AM', 'status' => 'failed'],
                        ];
                    @endphp
                    @foreach($pickups as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $p['id'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $p['merchant'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $p['address'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $p['count'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $p['scheduled'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $p['status']])</td>
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
