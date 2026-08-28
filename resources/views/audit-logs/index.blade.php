@extends('layouts.app')
@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
            <p class="text-sm text-gray-500 mt-1">Track all system activities and user actions.</p>
        </div>
        <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Export Logs</button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                    <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All Users</option>
                        <option>Admin User</option>
                        <option>Rajesh Kumar</option>
                        <option>Priya Sharma</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                    <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All Actions</option>
                        <option>Created</option>
                        <option>Updated</option>
                        <option>Deleted</option>
                        <option>Login</option>
                        <option>Export</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Module</label>
                    <select class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All Modules</option>
                        <option>Shipments</option>
                        <option>Users</option>
                        <option>Settings</option>
                        <option>Bulk Upload</option>
                    </select>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $logs = [
                            ['time' => '28 Aug, 09:15:23', 'user' => 'Admin User', 'action' => 'Login', 'module' => 'Auth', 'details' => 'Successful login', 'ip' => '192.168.1.100'],
                            ['time' => '28 Aug, 09:12:45', 'user' => 'Rajesh Kumar', 'action' => 'Created', 'module' => 'Shipments', 'details' => 'Bulk upload: 150 shipments', 'ip' => '192.168.1.105'],
                            ['time' => '28 Aug, 08:50:10', 'user' => 'Admin User', 'action' => 'Updated', 'module' => 'Settings', 'details' => 'Updated notification preferences', 'ip' => '192.168.1.100'],
                            ['time' => '27 Aug, 17:30:00', 'user' => 'Priya Sharma', 'action' => 'Updated', 'module' => 'NDR', 'details' => 'Reattempt triggered for DLV0012342', 'ip' => '192.168.1.110'],
                            ['time' => '27 Aug, 16:45:22', 'user' => 'Rajesh Kumar', 'action' => 'Created', 'module' => 'Pickups', 'details' => 'Pickup PKP-002 scheduled', 'ip' => '192.168.1.105'],
                            ['time' => '27 Aug, 15:20:11', 'user' => 'Amit Patel', 'action' => 'Exported', 'module' => 'Reports', 'details' => 'COD report exported for Aug 2026', 'ip' => '192.168.1.115'],
                            ['time' => '27 Aug, 14:00:00', 'user' => 'Admin User', 'action' => 'Deleted', 'module' => 'Users', 'details' => 'Deactivated user: Neha Gupta', 'ip' => '192.168.1.100'],
                        ];
                    @endphp
                    @foreach($logs as $l)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm text-gray-500 font-mono">{{ $l['time'] }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $l['user'] }}</td>
                            <td class="px-6 py-3.5">
                                @php
                                    $actionColors = [
                                        'Login' => 'bg-blue-100 text-blue-800',
                                        'Created' => 'bg-green-100 text-green-800',
                                        'Updated' => 'bg-yellow-100 text-yellow-800',
                                        'Deleted' => 'bg-red-100 text-red-800',
                                        'Exported' => 'bg-purple-100 text-purple-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $actionColors[$l['action']] ?? 'bg-gray-100 text-gray-800' }}">{{ $l['action'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $l['module'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600 max-w-xs truncate">{{ $l['details'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500 font-mono">{{ $l['ip'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">@include('components.pagination')</div>
    </div>
</div>
@endsection
