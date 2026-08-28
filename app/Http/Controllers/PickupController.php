<?php

namespace App\Http\Controllers;

use App\Models\PickupRequest;
use App\Models\DeliveryPartner;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickupController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PickupRequest::class);

        $query = DB::table('pickup_requests')
            ->join('shipments', 'pickup_requests.shipment_id', '=', 'shipments.id')
            ->leftJoin('merchants', 'pickup_requests.merchant_id', '=', 'merchants.id')
            ->leftJoin('hubs', 'pickup_requests.hub_id', '=', 'hubs.id')
            ->leftJoin('delivery_partners', 'pickup_requests.assigned_to', '=', 'delivery_partners.id')
            ->select(
                'pickup_requests.*',
                'shipments.awb_number',
                'merchants.business_name as merchant_name',
                'hubs.name as hub_name',
                'delivery_partners.name as partner_name'
            );

        if ($request->filled('status')) {
            $query->where('pickup_requests.status', $request->input('status'));
        }

        if ($request->filled('merchant_id')) {
            $query->where('pickup_requests.merchant_id', $request->input('merchant_id'));
        }

        if ($request->filled('hub_id')) {
            $query->where('pickup_requests.hub_id', $request->input('hub_id'));
        }

        $query->latest('pickup_requests.created_at');

        $pickups = $query->paginate($request->input('per_page', 20));

        return view('pickups.index', compact('pickups'));
    }

    public function show(PickupRequest $pickup)
    {
        $this->authorize('view', $pickup);

        $pickup->load([
            'shipment' => fn ($q) => $q->with(['status', 'merchant', 'currentHub']),
            'merchant', 'hub', 'assignedPartner', 'attempts',
        ]);

        return view('pickups.show', compact('pickup'));
    }

    public function assign(Request $request, PickupRequest $pickup)
    {
        $this->authorize('assign', PickupRequest::class);

        $request->validate([
            'delivery_partner_id' => 'required|exists:delivery_partners,id',
        ]);

        $partner = DeliveryPartner::findOrFail($request->input('delivery_partner_id'));

        $pickup->update([
            'assigned_to'  => $partner->id,
            'status'       => 'assigned',
            'scheduled_at' => $pickup->scheduled_at ?? now()->addDay(),
        ]);

        return back()->with('success', "Pickup assigned to {$partner->name}.");
    }

    public function schedule(Request $request, PickupRequest $pickup)
    {
        $this->authorize('update', $pickup);

        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $pickup->update([
            'scheduled_at' => $request->input('scheduled_at'),
            'status'       => 'scheduled',
        ]);

        return back()->with('success', 'Pickup scheduled successfully.');
    }

    public function markPickedUp(PickupRequest $pickup)
    {
        $this->authorize('update', $pickup);

        $pickup->update([
            'status'      => 'picked_up',
            'picked_up_at' => now(),
        ]);

        if ($pickup->shipment) {
            $status = DB::table('shipment_statuses')->where('slug', 'picked_up')->first();
            $pickup->shipment->update(['status_id' => $status?->id]);
        }

        return back()->with('success', 'Pickup marked as completed.');
    }

    public function markFailed(PickupRequest $pickup, Request $request)
    {
        $this->authorize('update', $pickup);

        $request->validate([
            'failure_reason' => 'required|string|max:500',
        ]);

        $pickup->update([
            'status'         => 'failed',
            'failure_reason' => $request->input('failure_reason'),
            'attempt_count'  => $pickup->attempt_count + 1,
        ]);

        return back()->with('success', 'Pickup marked as failed.');
    }

    public function createForShipment(Request $request, Shipment $shipment)
    {
        $this->authorize('create', PickupRequest::class);

        PickupRequest::create([
            'shipment_id'         => $shipment->id,
            'merchant_id'         => $shipment->merchant_id,
            'hub_id'              => $shipment->origin_hub_id,
            'pickup_address'      => $shipment->sender_address,
            'pickup_city'         => $shipment->sender_city,
            'pickup_state'        => $shipment->sender_state,
            'pickup_pincode'      => $shipment->sender_pincode,
            'pickup_phone'        => $shipment->sender_phone,
            'pickup_contact_name' => $shipment->sender_name,
            'requested_date'      => $shipment->pickup_scheduled_at?->toDateString() ?? now()->toDateString(),
            'status'              => 'pending',
            'max_attempts'        => 3,
        ]);

        return back()->with('success', 'Pickup request created for shipment.');
    }
}
