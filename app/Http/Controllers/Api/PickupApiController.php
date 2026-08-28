<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PickupRequestResource;
use App\Models\PickupRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickupApiController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('pickup_requests')
            ->join('shipments', 'pickup_requests.shipment_id', '=', 'shipments.id')
            ->leftJoin('merchants', 'pickup_requests.merchant_id', '=', 'merchants.id')
            ->leftJoin('hubs', 'pickup_requests.hub_id', '=', 'hubs.id')
            ->select('pickup_requests.*', 'shipments.awb_number')
            ->latest();

        if ($request->filled('status')) {
            $query->where('pickup_requests.status', $request->input('status'));
        }

        $perPage = min($request->input('per_page', 20), 100);
        $pickups = $query->paginate($perPage);

        return response()->json([
            'data' => PickupRequestResource::collection($pickups)->response()->getData(true)['data'],
            'meta' => [
                'current_page' => $pickups->currentPage(),
                'last_page'    => $pickups->lastPage(),
                'per_page'     => $pickups->perPage(),
                'total'        => $pickups->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id'          => 'required|exists:shipments,id',
            'pickup_address'       => 'required|string|max:500',
            'pickup_city'          => 'required|string|max:100',
            'pickup_pincode'       => 'required|string|max:10',
            'pickup_phone'         => 'required|string|max:15',
            'pickup_contact_name'  => 'required|string|max:255',
            'requested_date'       => 'required|date',
            'requested_time_slot'  => 'nullable|string|max:50',
        ]);

        $shipment = \App\Models\Shipment::findOrFail($validated['shipment_id']);

        $pickup = PickupRequest::create([
            ...$validated,
            'merchant_id'  => $shipment->merchant_id,
            'hub_id'       => $shipment->origin_hub_id,
            'status'       => 'pending',
            'max_attempts' => 3,
        ]);

        return (new PickupRequestResource($pickup->load(['shipment', 'merchant', 'hub'])))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(PickupRequest $pickup)
    {
        $pickup->load(['shipment.status', 'merchant', 'hub', 'assignedPartner', 'attempts']);

        return new PickupRequestResource($pickup);
    }

    public function update(Request $request, PickupRequest $pickup)
    {
        $validated = $request->validate([
            'status'       => 'sometimes|in:pending,assigned,scheduled,picked_up,failed,cancelled',
            'assigned_to'  => 'nullable|exists:delivery_partners,id',
            'scheduled_at' => 'nullable|date',
            'remarks'      => 'nullable|string|max:500',
        ]);

        $pickup->update($validated);

        return new PickupRequestResource($pickup->fresh(['shipment', 'merchant', 'hub', 'assignedPartner']));
    }

    public function destroy(PickupRequest $pickup)
    {
        $pickup->delete();

        return response()->json(['message' => 'Pickup request deleted.'], 200);
    }
}
