<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentApiRequest;
use App\Http\Requests\UpdateShipmentApiRequest;
use App\Http\Resources\ShipmentResource;
use App\Http\Resources\ShipmentCollection;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\ShipmentStatus;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentApiController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
    ) {}

    public function index(Request $request)
    {
        $query = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->leftJoin('hubs as current_hub', 'shipments.current_hub_id', '=', 'current_hub.id')
            ->select('shipments.*', 'shipment_statuses.slug as status_slug');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('shipments.awb_number', 'like', "%{$search}%")
                  ->orWhere('shipments.order_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('shipment_statuses.slug', $request->input('status'));
        }

        if ($request->filled('merchant_id')) {
            $query->where('shipments.merchant_id', $request->input('merchant_id'));
        }

        if ($request->filled('payment_mode')) {
            $query->where('shipments.payment_mode', $request->input('payment_mode'));
        }

        if ($request->filled('date_from')) {
            $query->where('shipments.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('shipments.created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $sortBy = $request->input('sort_by', 'shipments.created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = min($request->input('per_page', 20), 100);

        $shipments = $query->paginate($perPage);

        $resource = ShipmentResource::collection($shipments);

        return response()->json([
            'data'  => $resource->response()->getData(true)['data'],
            'meta'  => [
                'current_page' => $shipments->currentPage(),
                'last_page'    => $shipments->lastPage(),
                'per_page'     => $shipments->perPage(),
                'total'        => $shipments->total(),
            ],
        ]);
    }

    public function store(StoreShipmentApiRequest $request)
    {
        $data = $request->validated();
        $data['organization_id'] = $request->user()->organization_id;
        $data['user_id'] = $request->user()->id;

        $shipment = DB::transaction(function () use ($data) {
            $status = ShipmentStatus::where('slug', 'created')->first();

            $shipment = Shipment::create([
                ...$data,
                'status_id' => $status?->id,
                'awb_number' => app(\App\Services\AwbService::class)->generateAwb(),
            ]);

            ShipmentEvent::create([
                'shipment_id' => $shipment->id,
                'status_id'   => $status?->id,
                'status_slug' => 'created',
                'event_type'  => 'system',
                'description' => 'Shipment created',
                'actor_type'  => 'user',
                'actor_id'    => $data['user_id'],
            ]);

            return $shipment;
        });

        $shipment->load(['status', 'merchant', 'originHub', 'destinationHub']);

        return (new ShipmentResource($shipment))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(Shipment $shipment)
    {
        $shipment->load([
            'status', 'merchant', 'originHub', 'destinationHub', 'currentHub',
            'deliveryPartner', 'events' => fn ($q) => $q->latest(),
            'items',
        ]);

        return new ShipmentResource($shipment);
    }

    public function update(UpdateShipmentApiRequest $request, Shipment $shipment)
    {
        $shipment->update($request->validated());
        $shipment->load(['status', 'merchant', 'originHub', 'destinationHub']);

        return new ShipmentResource($shipment);
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status'  => 'required|string|exists:shipment_statuses,slug',
            'remarks' => 'nullable|string|max:500',
        ]);

        $shipment = $this->shipmentService->updateStatus(
            $shipment,
            $request->input('status'),
            $request->input('remarks'),
            $request->user()
        );

        $shipment->load(['status', 'events' => fn ($q) => $q->latest()]);

        return new ShipmentResource($shipment);
    }

    public function tracking(string $awb)
    {
        $shipment = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('hubs as current_hub', 'shipments.current_hub_id', '=', 'current_hub.id')
            ->select(
                'shipments.awb_number',
                'shipments.order_id',
                'shipments.receiver_city',
                'shipment_statuses.slug as status_slug',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'current_hub.name as current_hub_name',
                'shipments.expected_delivery_date',
                'shipments.actual_delivery_date'
            )
            ->where('shipments.awb_number', $awb)
            ->first();

        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        $events = DB::table('shipment_events')
            ->leftJoin('shipment_statuses', 'shipment_events.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('hubs', 'shipment_events.hub_id', '=', 'hubs.id')
            ->select(
                'shipment_events.status_slug',
                'shipment_events.description',
                'shipment_events.location',
                'shipment_events.event_type',
                'shipment_events.created_at',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'hubs.name as hub_name'
            )
            ->where('shipment_events.shipment_id', DB::table('shipments')->select('id')->where('awb_number', $awb))
            ->orderBy('shipment_events.created_at', 'desc')
            ->get();

        return response()->json([
            'data' => [
                'awb_number'           => $shipment->awb_number,
                'order_id'             => $shipment->order_id,
                'status'               => $shipment->status_slug,
                'status_label'         => $shipment->status_name,
                'status_color'         => $shipment->status_color,
                'current_hub'          => $shipment->current_hub_name,
                'expected_delivery'    => $shipment->expected_delivery_date,
                'actual_delivery'      => $shipment->actual_delivery_date,
                'events'               => $events,
            ],
        ]);
    }

    public function events(Shipment $shipment)
    {
        $events = $shipment->events()
            ->with('hub')
            ->latest()
            ->get();

        return response()->json(['data' => $events]);
    }
}
