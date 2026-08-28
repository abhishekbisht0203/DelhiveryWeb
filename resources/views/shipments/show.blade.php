@extends('layouts.app')
@section('title', 'Shipment Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('shipments.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">DLV0012345</h1>
                @include('components.status-badge', ['status' => 'in_transit'])
            </div>
            <p class="text-sm text-gray-500 mt-1">Order ORD-78901 &bull; Created 28 Aug 2026</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('shipments.edit', 'DLV0012345') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Edit</a>
            <button class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">Print AWB</button>
        </div>
    </div>

    {{-- Status Tracker --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-6">Shipment Progress</h3>
        <div class="flex items-center justify-between relative">
            <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200"></div>
            <div class="absolute top-5 left-0 h-0.5 bg-primary-500" style="width: 50%"></div>
            @php
                $steps = [
                    ['label' => 'Created', 'done' => true, 'time' => '10:30 AM'],
                    ['label' => 'Picked Up', 'done' => true, 'time' => '02:15 PM'],
                    ['label' => 'At Hub', 'done' => true, 'time' => '06:45 PM'],
                    ['label' => 'In Transit', 'done' => true, 'active' => true, 'time' => '11:20 PM'],
                    ['label' => 'Out for Delivery', 'done' => false, 'time' => ''],
                    ['label' => 'Delivered', 'done' => false, 'time' => ''],
                ];
            @endphp
            @foreach($steps as $step)
                <div class="relative z-10 text-center">
                    <div class="w-10 h-10 rounded-full {{ $step['done'] ? 'bg-primary-500 text-white' : 'bg-gray-200 text-gray-500' }} {{ $step['active'] ?? false ? 'ring-4 ring-primary-100' : '' }} flex items-center justify-center mx-auto">
                        @if($step['done'])
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <span class="text-xs font-medium">{{ $loop->iteration }}</span>
                        @endif
                    </div>
                    <p class="text-xs font-medium text-gray-900 mt-2">{{ $step['label'] }}</p>
                    @if($step['time'])
                        <p class="text-xs text-gray-500">{{ $step['time'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Consignee --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Consignee Details</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Name</span><span class="font-medium text-gray-900">Rahul Sharma</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="font-medium text-gray-900">+91 98765 43210</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-medium text-gray-900">rahul@email.com</span></div>
                <hr>
                <div><span class="text-gray-500">Address</span><p class="font-medium text-gray-900 mt-1">123, Green Park Society, Andheri West, Mumbai, Maharashtra - 400053</p></div>
            </div>
        </div>

        {{-- Shipment Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Shipment Info</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">AWB</span><span class="font-medium text-primary-600">DLV0012345</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Order ID</span><span class="font-medium text-gray-900">ORD-78901</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Payment</span><span class="font-medium text-amber-600">COD</span></div>
                <div class="flex justify-between"><span class="text-gray-500">COD Amount</span><span class="font-medium text-gray-900">₹1,250</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Weight</span><span class="font-medium text-gray-900">2.5 kg</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Shipping</span><span class="font-medium text-gray-900">Express</span></div>
            </div>
        </div>

        {{-- Current Status --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Current Status</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Current Hub</span><span class="font-medium text-gray-900">Delhi Sorting Hub</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Destination</span><span class="font-medium text-gray-900">Mumbai, MH</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Expected Delivery</span><span class="font-medium text-green-600">30 Aug 2026</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Delivery Partner</span><span class="font-medium text-gray-900">Delhivery Express</span></div>
            </div>
        </div>
    </div>

    {{-- Tracking History --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Tracking History</h3>
        <div class="space-y-0">
            @php
                $history = [
                    ['status' => 'In Transit', 'location' => 'Delhi Sorting Hub', 'time' => '28 Aug, 11:20 PM', 'detail' => 'Shipment dispatched from Delhi hub', 'current' => true],
                    ['status' => 'At Hub', 'location' => 'Delhi Sorting Hub', 'time' => '28 Aug, 06:45 PM', 'detail' => 'Shipment received at sorting hub'],
                    ['status' => 'Picked Up', 'location' => 'Delhi', 'time' => '28 Aug, 02:15 PM', 'detail' => 'Package picked up from merchant'],
                    ['status' => 'Created', 'location' => 'Online', 'time' => '28 Aug, 10:30 AM', 'detail' => 'Shipment created by merchant'],
                ];
            @endphp
            @foreach($history as $i => $h)
                <div class="flex gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 rounded-full {{ $h['current'] ? 'bg-primary-500 ring-4 ring-primary-100' : 'bg-gray-300' }}"></div>
                        @if(!$loop->last)<div class="w-0.5 flex-1 bg-gray-200 mt-1"></div>@endif
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium {{ $h['current'] ? 'text-primary-600' : 'text-gray-900' }}">{{ $h['status'] }}</p>
                        <p class="text-sm text-gray-500">{{ $h['detail'] }}</p>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                            <span>{{ $h['location'] }}</span>
                            <span>&bull;</span>
                            <span>{{ $h['time'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
