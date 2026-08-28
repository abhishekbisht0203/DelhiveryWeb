@extends('layouts.app')
@section('title', 'Public Tracking')

@section('content')
<div class="space-y-6">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Track Your Shipment</h1>
            <p class="text-sm text-gray-500 mt-1">Enter your AWB number to track your shipment status.</p>
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="GET" class="flex gap-3">
                <input type="text" name="awb" value="{{ request('awb') }}" placeholder="Enter AWB Number (e.g. DLV0012345)"
                       class="flex-1 px-4 py-3 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">Track</button>
            </form>
        </div>

        {{-- Tracking Result --}}
        @if(request('awb'))
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ request('awb') }}</h2>
                        <p class="text-sm text-gray-500">Order ID: ORD-78901</p>
                    </div>
                    <div class="ml-auto">
                        @include('components.status-badge', ['status' => 'in_transit'])
                    </div>
                </div>

                {{-- Progress --}}
                <div class="flex items-center justify-between mb-8 px-4">
                    @php
                        $steps = ['Created', 'Picked Up', 'In Transit', 'Out for Delivery', 'Delivered'];
                        $currentStep = 2;
                    @endphp
                    @foreach($steps as $i => $step)
                        <div class="text-center flex-1">
                            <div class="w-8 h-8 rounded-full {{ $i <= $currentStep ? 'bg-primary-500 text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center mx-auto text-xs font-medium {{ $i === $currentStep ? 'ring-4 ring-primary-100' : '' }}">
                                @if($i < $currentStep) &#10003; @else {{ $i + 1 }} @endif
                            </div>
                            <p class="text-xs mt-2 {{ $i <= $currentStep ? 'text-primary-600 font-medium' : 'text-gray-400' }}">{{ $step }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Tracking Timeline --}}
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Tracking History</h3>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center"><div class="w-3 h-3 rounded-full bg-primary-500 ring-4 ring-primary-100"></div><div class="w-0.5 flex-1 bg-gray-200 mt-1"></div></div>
                            <div><p class="text-sm font-medium text-primary-600">In Transit</p><p class="text-sm text-gray-500">Shipment dispatched from Delhi hub</p><p class="text-xs text-gray-400 mt-1">28 Aug, 11:20 PM &bull; Delhi Sorting Hub</p></div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center"><div class="w-3 h-3 rounded-full bg-gray-300"></div><div class="w-0.5 flex-1 bg-gray-200 mt-1"></div></div>
                            <div><p class="text-sm font-medium text-gray-900">At Hub</p><p class="text-sm text-gray-500">Shipment received at sorting hub</p><p class="text-xs text-gray-400 mt-1">28 Aug, 06:45 PM &bull; Delhi Sorting Hub</p></div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center"><div class="w-3 h-3 rounded-full bg-gray-300"></div><div class="w-0.5 flex-1 bg-gray-200 mt-1"></div></div>
                            <div><p class="text-sm font-medium text-gray-900">Picked Up</p><p class="text-sm text-gray-500">Package picked up from merchant</p><p class="text-xs text-gray-400 mt-1">28 Aug, 02:15 PM &bull; Delhi</p></div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center"><div class="w-3 h-3 rounded-full bg-gray-300"></div></div>
                            <div><p class="text-sm font-medium text-gray-900">Created</p><p class="text-sm text-gray-500">Shipment created by merchant</p><p class="text-xs text-gray-400 mt-1">28 Aug, 10:30 AM &bull; Online</p></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-500">Expected Delivery:</span> <span class="font-medium text-green-600">30 Aug 2026</span></div>
                        <div><span class="text-gray-500">Current Location:</span> <span class="font-medium text-gray-900">Delhi Sorting Hub</span></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
