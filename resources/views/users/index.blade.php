@extends('layouts.app')
@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Users</h1>
            <p class="text-sm text-gray-500 mt-1">Manage system users and their access.</p>
        </div>
        <button class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">+ Add User</button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $users = [
                            ['name' => 'Admin User', 'email' => 'admin@delhivery.com', 'role' => 'Admin', 'status' => 'active', 'login' => '28 Aug, 09:15 AM'],
                            ['name' => 'Rajesh Kumar', 'email' => 'rajesh@delhivery.com', 'role' => 'Operations Manager', 'status' => 'active', 'login' => '28 Aug, 08:30 AM'],
                            ['name' => 'Priya Sharma', 'email' => 'priya@delhivery.com', 'role' => 'Support Agent', 'status' => 'active', 'login' => '27 Aug, 05:45 PM'],
                            ['name' => 'Amit Patel', 'email' => 'amit@delhivery.com', 'role' => 'Finance', 'status' => 'active', 'login' => '27 Aug, 02:00 PM'],
                            ['name' => 'Neha Gupta', 'email' => 'neha@delhivery.com', 'role' => 'Support Agent', 'status' => 'inactive', 'login' => '20 Aug, 10:00 AM'],
                        ];
                    @endphp
                    @foreach($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-sm font-semibold">{{ substr($u['name'], 0, 1) }}</div>
                                    <span class="text-sm font-medium text-gray-900">{{ $u['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $u['email'] }}</td>
                            <td class="px-6 py-3.5 text-sm text-gray-600">{{ $u['role'] }}</td>
                            <td class="px-6 py-3.5">@include('components.status-badge', ['status' => $u['status']])</td>
                            <td class="px-6 py-3.5 text-sm text-gray-500">{{ $u['login'] }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></button>
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
