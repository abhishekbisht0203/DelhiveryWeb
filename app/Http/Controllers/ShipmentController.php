<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Models\ShipmentEvent;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShipmentsExport;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Shipment::class);

        $query = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->leftJoin('hubs as origin', 'shipments.origin_hub_id', '=', 'origin.id')
            ->leftJoin('hubs as destination', 'shipments.destination_hub_id', '=', 'destination.id')
            ->leftJoin('delivery_partners', 'shipments.delivery_partner_id', '=', 'delivery_partners.id')
            ->select(
                'shipments.*',
                'shipment_statuses.slug as status_slug',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'merchants.business_name as merchant_name',
                'origin.name as origin_hub_name',
                'destination.name as destination_hub_name',
                'delivery_partners.name as delivery_partner_name'
            );

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('shipments.awb_number', 'like', "%{$search}%")
                  ->orWhere('shipments.order_id', 'like', "%{$search}%")
                  ->orWhere('shipments.receiver_name', 'like', "%{$search}%")
                  ->orWhere('shipments.receiver_phone', 'like', "%{$search}%")
                  ->orWhere('shipments.sender_name', 'like', "%{$search}%");
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

        if ($request->filled('sender_pincode')) {
            $query->where('shipments.sender_pincode', $request->input('sender_pincode'));
        }

        if ($request->filled('receiver_pincode')) {
            $query->where('shipments.receiver_pincode', $request->input('receiver_pincode'));
        }

        if ($request->filled('current_hub_id')) {
            $query->where('shipments.current_hub_id', $request->input('current_hub_id'));
        }

        if ($request->filled('is_rto')) {
            $query->where('shipments.is_rto', $request->boolean('is_rto'));
        }

        $sortBy = $request->input('sort_by', 'shipments.created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $shipments = $query->paginate($request->input('per_page', 20))->withQueryString();

        return view('shipments.index', compact('shipments'));
    }

    public function create()
    {
        $this->authorize('create', Shipment::class);

        $merchants = DB::table('merchants')
            ->where('status', 'active')
            ->orderBy('business_name')
            ->get(['id', 'business_name']);

        $hubs = DB::table('hubs')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $statuses = ShipmentStatus::active()->normal()->orderBy('sort_order')->get();

        return view('shipments.create', compact('merchants', 'hubs', 'statuses'));
    }

    public function store(StoreShipmentRequest $request)
    {
        $this->authorize('create', Shipment::class);

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

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('success', "Shipment {$shipment->awb_number} created successfully.");
    }

    public function show(Shipment $shipment)
    {
        $this->authorize('view', $shipment);

        $shipment->load([
            'status', 'merchant', 'originHub', 'destinationHub', 'currentHub',
            'deliveryPartner', 'events' => fn ($q) => $q->latest(),
            'items', 'ndrRecords', 'rtoRecord',
        ]);

        $validTransitions = $this->shipmentService->getValidTransitions(
            $shipment->status?->slug ?? 'created'
        );

        return view('shipments.show', compact('shipment', 'validTransitions'));
    }

    public function edit(Shipment $shipment)
    {
        $this->authorize('update', $shipment);

        $merchants = DB::table('merchants')
            ->where('status', 'active')
            ->orderBy('business_name')
            ->get(['id', 'business_name']);

        $hubs = DB::table('hubs')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('shipments.edit', compact('shipment', 'merchants', 'hubs'));
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        $this->authorize('update', $shipment);

        $shipment->update($request->validated());

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('success', 'Shipment updated successfully.');
    }

    public function destroy(Shipment $shipment)
    {
        $this->authorize('delete', $shipment);

        $shipment->delete();

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $this->authorize('updateStatus', $shipment);

        $request->validate([
            'status'   => 'required|string|exists:shipment_statuses,slug',
            'remarks'  => 'nullable|string|max:500',
        ]);

        $this->shipmentService->updateStatus(
            $shipment,
            $request->input('status'),
            $request->input('remarks'),
            $request->user()
        );

        return back()->with('success', 'Shipment status updated successfully.');
    }

    public function track(Request $request)
    {
        $awb = $request->input('awb');

        if (!$awb) {
            return view('shipments.track');
        }

        return $this->trackByAwb($request);
    }

    public function trackByAwb(Request $request)
    {
        $request->validate([
            'awb' => 'required|string',
        ]);

        $shipment = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('hubs as current_hub', 'shipments.current_hub_id', '=', 'current_hub.id')
            ->select(
                'shipments.awb_number',
                'shipments.order_id',
                'shipments.receiver_city',
                'shipments.receiver_state',
                'shipment_statuses.slug as status',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'current_hub.name as current_hub_name',
                'current_hub.city as current_hub_city',
                'shipments.expected_delivery_date',
                'shipments.pickup_scheduled_at',
                'shipments.actual_delivery_date',
                'shipments.created_at'
            )
            ->where('shipments.awb_number', $request->input('awb'))
            ->first();

        if (!$shipment) {
            return back()->withErrors(['awb' => 'Shipment not found with this AWB number.']);
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
            ->where('shipment_events.awb_number', $request->input('awb'))
            ->orderBy('shipment_events.created_at', 'desc')
            ->get();

        return view('shipments.track', compact('shipment', 'events'));
    }

    public function bulkUpload()
    {
        $this->authorize('bulkUpload', Shipment::class);

        return view('shipments.bulk-upload');
    }

    public function processBulkUpload(Request $request)
    {
        $this->authorize('bulkUpload', Shipment::class);

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx|max:10240',
        ]);

        $file = $request->file('file');
        $data = Excel::toArray([], $file);
        $rows = $data[0] ?? [];

        if (count($rows) < 2) {
            return back()->withErrors(['file' => 'The file must contain a header row and at least one data row.']);
        }

        $headers = array_map('strtolower', array_map('trim', $rows[0]));
        $shipmentsData = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $shipmentsData[] = array_combine($headers, $row);
        }

        $result = $this->shipmentService->bulkCreateShipments($shipmentsData, $request->user());

        return back()->with([
            'bulk_result' => $result,
            'success'     => "{$result['success']} shipments created successfully.",
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('export', Shipment::class);

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
            'status'    => 'nullable|string',
        ]);

        $filename = 'shipments_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new ShipmentsExport($request->only(['date_from', 'date_to', 'status', 'merchant_id'])),
            $filename
        );
    }

    public function getStats(Request $request)
    {
        $this->authorize('viewStats', Shipment::class);

        $stats = $this->shipmentService->getShipmentStats(
            merchantId: $request->user()->merchant_id,
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
        );

        return response()->json($stats);
    }
}
