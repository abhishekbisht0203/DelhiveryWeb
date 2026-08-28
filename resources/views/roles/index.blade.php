@extends('layouts.app')
@section('title', 'Roles & Permissions')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Roles & Permissions</h1>
            <p class="text-sm text-gray-500 mt-1">Manage roles and assign permissions to users.</p>
        </div>
        <button class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">+ Create Role</button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permissions</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $roles = [
                            ['name' => 'Admin', 'desc' => 'Full system access', 'users' => 2, 'perms' => 'All'],
                            ['name' => 'Operations Manager', 'desc' => 'Manage shipments, pickups, and hubs', 'users' => 3, 'perms' => '15 permissions'],
                            ['name' => 'Support Agent', 'desc' => 'Handle NDR cases and customer queries', 'users' => 8, 'perms' => '10 permissions'],
                            ['name' => 'Finance', 'desc' => 'Manage invoices, COD, and payments', 'users' => 2, 'perms' => '8 permissions'],
                            ['name' => 'Viewer', 'desc' => 'Read-only access to reports and dashboards', 'users' => 5, 'perms' => '5 permissions'],
                        ];
                    @endphp
                    @foreach($roles as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5 text-sm font-semibold text-gray-900">{{ $r['name'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $r['desc'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-900">{{ $r['users'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $r['perms'] }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    @if($r['name'] !== 'Admin')
                                        <button class="p-1.5 rounded-lg text-red-500 hover:bg-red-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Permissions Matrix --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Permissions Matrix</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Permission</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Admin</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Ops Manager</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Support</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Finance</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Viewer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $perms = [
                            ['name' => 'View Dashboard', 'admin' => true, 'ops' => true, 'support' => true, 'finance' => true, 'viewer' => true],
                            ['name' => 'Manage Shipments', 'admin' => true, 'ops' => true, 'support' => false, 'finance' => false, 'viewer' => false],
                            ['name' => 'Handle NDR/RTO', 'admin' => true, 'ops' => true, 'support' => true, 'finance' => false, 'viewer' => false],
                            ['name' => 'Manage Invoices', 'admin' => true, 'ops' => false, 'support' => false, 'finance' => true, 'viewer' => false],
                            ['name' => 'View Reports', 'admin' => true, 'ops' => true, 'support' => true, 'finance' => true, 'viewer' => true],
                            ['name' => 'Manage Users', 'admin' => true, 'ops' => false, 'support' => false, 'finance' => false, 'viewer' => false],
                            ['name' => 'System Settings', 'admin' => true, 'ops' => false, 'support' => false, 'finance' => false, 'viewer' => false],
                        ];
                    @endphp
                    @foreach($perms as $p)
                        <tr>
                            <td class="px-4 py-2.5 text-sm font-medium text-gray-900">{{ $p['name'] }}</td>
                            @foreach(['admin', 'ops', 'support', 'finance', 'viewer'] as $role)
                                <td class="px-4 py-2.5 text-center">
                                    @if($p[$role])
                                        <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
