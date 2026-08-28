<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::with('hub');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hub_id')) {
            $query->where('hub_id', $request->input('hub_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $warehouses = $query->orderBy('name')->paginate($request->input('per_page', 20));

        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $hubs = DB::table('hubs')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('warehouses.create', compact('hubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hub_id'        => 'required|exists:hubs,id',
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:20|unique:warehouses,code',
            'address'       => 'nullable|string|max:500',
            'city'          => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:100',
            'pincode'       => 'nullable|string|max:10',
            'type'          => 'nullable|in:main,regional,local',
            'capacity'      => 'nullable|integer|min:0',
            'current_stock' => 'nullable|integer|min:0',
            'manager_name'  => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:15',
            'email'         => 'nullable|email|max:255',
            'status'        => 'nullable|in:active,inactive',
        ]);

        $validated['organization_id'] = $request->user()->organization_id;

        $warehouse = Warehouse::create($validated);

        return redirect()
            ->route('warehouses.show', $warehouse)
            ->with('success', "Warehouse {$warehouse->name} created successfully.");
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load('hub');
        $warehouse->loadCount('shipments');

        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        $hubs = DB::table('hubs')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('warehouses.edit', compact('warehouse', 'hubs'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'hub_id'        => 'sometimes|exists:hubs,id',
            'name'          => 'sometimes|string|max:255',
            'code'          => 'sometimes|string|max:20|unique:warehouses,code,' . $warehouse->id,
            'address'       => 'nullable|string|max:500',
            'city'          => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:100',
            'pincode'       => 'nullable|string|max:10',
            'type'          => 'nullable|in:main,regional,local',
            'capacity'      => 'nullable|integer|min:0',
            'current_stock' => 'nullable|integer|min:0',
            'manager_name'  => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:15',
            'email'         => 'nullable|email|max:255',
            'status'        => 'nullable|in:active,inactive',
        ]);

        $warehouse->update($validated);

        return redirect()
            ->route('warehouses.show', $warehouse)
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }
}
