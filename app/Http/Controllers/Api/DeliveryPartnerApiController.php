<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryPartnerResource;
use App\Models\DeliveryPartner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryPartnerApiController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryPartner::with('hub');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hub_id')) {
            $query->where('hub_id', $request->input('hub_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min($request->input('per_page', 20), 100);
        $partners = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => DeliveryPartnerResource::collection($partners)->response()->getData(true)['data'],
            'meta' => [
                'current_page' => $partners->currentPage(),
                'last_page'    => $partners->lastPage(),
                'per_page'     => $partners->perPage(),
                'total'        => $partners->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hub_id'         => 'required|exists:hubs,id',
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:15|unique:delivery_partners,phone',
            'email'          => 'nullable|email|max:255|unique:delivery_partners,email',
            'vehicle_type'   => 'required|in:bicycle,motorcycle,van,truck,other',
            'vehicle_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:20',
            'aadhar_number'  => 'nullable|string|max:12',
            'assigned_areas' => 'nullable|array',
            'status'         => 'nullable|in:active,inactive,on_leave',
        ]);

        $validated['organization_id'] = $request->user()->organization_id;

        $partner = DeliveryPartner::create($validated);

        return (new DeliveryPartnerResource($partner->load('hub')))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(DeliveryPartner $partner)
    {
        $partner->load('hub');
        $partner->loadCount('shipments');

        return new DeliveryPartnerResource($partner);
    }

    public function update(Request $request, DeliveryPartner $partner)
    {
        $validated = $request->validate([
            'hub_id'         => 'sometimes|exists:hubs,id',
            'name'           => 'sometimes|string|max:255',
            'phone'          => 'sometimes|string|max:15|unique:delivery_partners,phone,' . $partner->id,
            'email'          => 'nullable|email|max:255|unique:delivery_partners,email,' . $partner->id,
            'vehicle_type'   => 'sometimes|in:bicycle,motorcycle,van,truck,other',
            'vehicle_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:20',
            'assigned_areas' => 'nullable|array',
            'status'         => 'nullable|in:active,inactive,on_leave',
        ]);

        $partner->update($validated);

        return new DeliveryPartnerResource($partner->fresh('hub'));
    }

    public function destroy(DeliveryPartner $partner)
    {
        $partner->delete();

        return response()->json(['message' => 'Delivery partner deleted.'], 200);
    }
}
