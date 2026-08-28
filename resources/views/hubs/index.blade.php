@extends('layouts.app')
@section('title', 'Hubs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hubs</h1>
            <p class="text-sm text-gray-500 mt-1">Manage sorting hubs and distribution centers.</p>
        </div>
        <button class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">+ Add Hub</button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hub Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">State</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $hubs = [
                            ['code' => 'MUM-H1', 'name' => 'Mumbai Main Hub', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'type' => 'Sorting', 'capacity' => '10,000/day', 'status' => 'active'],
                            ['code' => 'DEL-H1', 'name' => 'Delhi NCR Hub', 'city' => 'Delhi', 'state' => 'Delhi', 'type' => 'Sorting', 'capacity' => '15,000/day', 'status' => 'active'],
                            ['code' => 'BLR-H1', 'name' => 'Bangalore Hub', 'city' => 'Bangalore', 'state' => 'Karnataka', 'type' => 'Sorting', 'capacity' => '8,000/day', 'status' => 'active'],
                            ['code' => 'CHN-H1', 'name' => 'Chennai Hub', 'city' => 'Chennai', 'state' => 'Tamil Nadu', 'type' => 'Distribution', 'capacity' => '5,000/day', 'status' => 'active'],
                            ['code' => 'HYD-H1', 'name' => 'Hyderabad Hub', 'city' => 'Hyderabad', 'state' => 'Telangana', 'type' => 'Distribution', 'capacity' => '4,000/day', 'status' => 'inactive'],
                        ];
                    @endphp
                    @foreach($hubs as $h)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-medium text-primary-600">{{ $h['code'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $h['name'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $h['city'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $h['state'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $h['type'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $h['capacity'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $h['status']])</td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    <button class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
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
