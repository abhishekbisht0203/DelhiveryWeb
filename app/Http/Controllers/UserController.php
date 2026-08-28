<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = DB::table('users')
            ->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
            ->select('users.*', 'organizations.name as organization_name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('users.status', $request->input('status'));
        }

        $users = $query->orderBy('users.name')->paginate($request->input('per_page', 20));

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $roles = DB::table('roles')->orderBy('name')->get();
        $organizations = DB::table('organizations')->orderBy('name')->get(['id', 'name']);

        return view('users.create', compact('roles', 'organizations'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
            'phone'             => 'nullable|string|max:15',
            'organization_id'   => 'nullable|exists:organizations,id',
            'role'              => 'required|string|exists:roles,name',
            'status'            => 'nullable|in:active,inactive',
        ]);

        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'password'        => Hash::make($validated['password']),
            'phone'           => $validated['phone'] ?? null,
            'organization_id' => $validated['organization_id'] ?? null,
            'status'          => $validated['status'] ?? 'active',
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('users.index')
            ->with('success', "User {$user->name} created successfully.");
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load('organization');
        $roles = $user->getRoleNames();

        return view('users.show', compact('user', 'roles'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = DB::table('roles')->orderBy('name')->get();
        $organizations = DB::table('organizations')->orderBy('name')->get(['id', 'name']);
        $currentRole = $user->getRoleNames()->first();

        return view('users.edit', compact('user', 'roles', 'organizations', 'currentRole'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'email'           => 'sometimes|email|unique:users,email,' . $user->id,
            'password'        => 'nullable|string|min:8|confirmed',
            'phone'           => 'nullable|string|max:15',
            'organization_id' => 'nullable|exists:organizations,id',
            'role'            => 'sometimes|string|exists:roles,name',
            'status'          => 'nullable|in:active,inactive',
        ]);

        $data = collect($validated)->filter()->except(['password', 'role'])->toArray();

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        if (!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
