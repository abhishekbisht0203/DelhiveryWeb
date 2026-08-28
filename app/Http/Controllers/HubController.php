<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHubRequest;
use App\Http\Requests\UpdateHubRequest;
use App\Models\Hub;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\ShipmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HubController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Hub::class);

        $query = DB::table('hubs')
            ->select('hubs.*')
            ->selectRaw('(SELECT COUNT(*) FROM shipments WHERE shipments.current_hub_id = hubs.id) as active_shipments');

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

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        $hubs = $query->orderBy('name')->paginate($request->input('per_page', 20));

        return view('hubs.index', compact('hubs'));
    }

    public function create()
    {
        $this->authorize('create', Hub::class);

        return view('hubs.create');
    }

    public function store(StoreHubRequest $request)
    {
        $this->authorize('create', Hub::class);

        $hub = Hub::create([
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
        ]);

        return redirect()
            ->route('hubs.show', $hub)
            ->with('success', "Hub {$hub->name} created successfully.");
    }

    public function show(Hub $hub)
    {
        $this->authorize('view', $hub);

        $hub->loadCount(['shipments', 'warehouses', 'deliveryPartners']);

        $activeShipments = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->select('shipments.awb_number', 'shipments.receiver_name', 'shipment_statuses.name as status_name')
            ->where('shipments.current_hub_id', $hub->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('hubs.show', compact('hub', 'activeShipments'));
    }

    public function edit(Hub $hub)
    {
        $this->authorize('update', $hub);

        return view('hubs.edit', compact('hub'));
    }

    public function update(UpdateHubRequest $request, Hub $hub)
    {
        $this->authorize('update', $hub);

        $hub->update($request->validated());

        return redirect()
            ->route('hubs.show', $hub)
            ->with('success', 'Hub updated successfully.');
    }

    public function destroy(Hub $hub)
    {
        $this->authorize('delete', $hub);

        $hub->delete();

        return redirect()
            ->route('hubs.index')
            ->with('success', 'Hub deleted successfully.');
    }

    public function shipments(Hub $hub)
    {
        $this->authorize('view', $hub);

        $shipments = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->select(
                'shipments.awb_number',
                'shipments.receiver_name',
                'shipments.receiver_city',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'merchants.business_name as merchant_name',
                'shipments.created_at'
            )
            ->where('shipments.current_hub_id', $hub->id)
            ->latest()
            ->paginate(20);

        return view('hubs.shipments', compact('hub', 'shipments'));
    }

    public function receive(Request $request, Hub $hub, Shipment $shipment)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        $status = ShipmentStatus::where('slug', 'at_origin_hub')->first();

        $shipment->update([
            'current_hub_id' => $hub->id,
            'status_id'      => $status?->id,
        ]);

        ShipmentEvent::create([
            'shipment_id' => $shipment->id,
            'status_id'   => $status?->id,
            'status_slug' => 'at_origin_hub',
            'event_type'  => 'hub_scan',
            'description' => "Received at hub: {$hub->name}",
            'hub_id'      => $hub->id,
            'actor_type'  => 'system',
        ]);

        return back()->with('success', "Shipment received at {$hub->name}.");
    }

    public function dispatch(Request $request, Hub $hub)
    {
        $request->validate([
            'shipment_ids'     => 'required|array',
            'shipment_ids.*'   => 'exists:shipments,id',
            'destination_hub_id' => 'required|exists:hubs,id',
            'remarks'          => 'nullable|string|max:500',
        ]);

        $status = ShipmentStatus::where('slug', 'in_transit')->first();

        foreach ($request->input('shipment_ids') as $shipmentId) {
            $shipment = Shipment::find($shipmentId);
            if ($shipment && $shipment->current_hub_id === $hub->id) {
                $shipment->update([
                    'current_hub_id'    => $request->input('destination_hub_id'),
                    'status_id'         => $status?->id,
                    'destination_hub_id' => $request->input('destination_hub_id'),
                ]);

                ShipmentEvent::create([
                    'shipment_id' => $shipment->id,
                    'status_id'   => $status?->id,
                    'status_slug' => 'in_transit',
                    'event_type'  => 'dispatch',
                    'description' => "Dispatched from {$hub->name}",
                    'hub_id'      => $hub->id,
                    'actor_type'  => 'system',
                ]);
            }
        }

        return back()->with('success', count($request->input('shipment_ids')) . ' shipments dispatched.');
    }
}
