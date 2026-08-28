<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HubResource;
use App\Models\Hub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HubApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Hub::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min($request->input('per_page', 20), 100);
        $hubs = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => HubResource::collection($hubs)->response()->getData(true)['data'],
            'meta' => [
                'current_page' => $hubs->currentPage(),
                'last_page'    => $hubs->lastPage(),
                'per_page'     => $hubs->perPage(),
                'total'        => $hubs->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|max:20|unique:hubs,code',
            'address'         => 'required|string|max:500',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'pincode'         => 'required|string|max:10',
            'phone'           => 'nullable|string|max:15',
            'email'           => 'nullable|email|max:255',
            'manager_name'    => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'capacity'        => 'nullable|integer|min:0',
            'status'          => 'nullable|in:active,inactive',
            'operating_hours' => 'nullable|array',
        ]);

        $validated['organization_id'] = $request->user()->organization_id;

        $hub = Hub::create($validated);

        return (new HubResource($hub))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(Hub $hub)
    {
        $hub->loadCount(['shipments', 'warehouses', 'deliveryPartners']);

        return new HubResource($hub);
    }

    public function update(Request $request, Hub $hub)
    {
        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'code'            => 'sometimes|string|max:20|unique:hubs,code,' . $hub->id,
            'address'         => 'sometimes|string|max:500',
            'city'            => 'sometimes|string|max:100',
            'state'           => 'sometimes|string|max:100',
            'pincode'         => 'sometimes|string|max:10',
            'phone'           => 'nullable|string|max:15',
            'email'           => 'nullable|email|max:255',
            'manager_name'    => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'capacity'        => 'nullable|integer|min:0',
            'status'          => 'nullable|in:active,inactive',
            'operating_hours' => 'nullable|array',
        ]);

        $hub->update($validated);

        return new HubResource($hub->fresh());
    }

    public function destroy(Hub $hub)
    {
        $hub->delete();

        return response()->json(['message' => 'Hub deleted.'], 200);
    }
}
