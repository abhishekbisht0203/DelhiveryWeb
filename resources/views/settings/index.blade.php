@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your account and application preferences.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <nav class="bg-white rounded-xl border border-gray-200 p-2 space-y-1">
                @php
                    $tabs = [
                        ['label' => 'General', 'active' => true],
                        ['label' => 'Notifications', 'active' => false],
                        ['label' => 'API Keys', 'active' => false],
                        ['label' => 'Integrations', 'active' => false],
                        ['label' => 'Branding', 'active' => false],
                    ];
                @endphp
                @foreach($tabs as $tab)
                    <button class="w-full text-left px-3 py-2 text-sm rounded-lg {{ $tab['active'] ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">{{ $tab['label'] }}</button>
                @endforeach
            </nav>
        </div>

        {{-- Content --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">General Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Company Name</label>
                        <input type="text" value="Delhivery Logistics" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Contact Email</label>
                        <input type="email" value="admin@delhivery.com" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Phone</label>
                        <input type="tel" value="+91 22 1234 5678" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Timezone</label>
                        <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                            <option selected>Asia/Kolkata (IST)</option>
                            <option>Asia/Dubai (GST)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                        <textarea rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">Delhivery Logistics Pvt Ltd, Plot No. 25, Sector 24, Gurugram, Haryana - 122002</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Notification Preferences</h3>
                <div class="space-y-4">
                    @foreach([
                        ['label' => 'Shipment status updates', 'desc' => 'Get notified on status changes', 'enabled' => true],
                        ['label' => 'NDR alerts', 'desc' => 'Get notified when NDR cases are created', 'enabled' => true],
                        ['label' => 'COD collection reminders', 'desc' => 'Daily reminders for pending COD', 'enabled' => false],
                        ['label' => 'System maintenance alerts', 'desc' => 'Scheduled maintenance notifications', 'enabled' => true],
                    ] as $notif)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $notif['label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $notif['desc'] }}</p>
                            </div>
                            <button class="relative inline-flex h-6 w-11 items-center rounded-full {{ $notif['enabled'] ? 'bg-primary-600' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $notif['enabled'] ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end">
                <button class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
