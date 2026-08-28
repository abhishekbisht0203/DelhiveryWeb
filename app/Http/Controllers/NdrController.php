<?php

namespace App\Http\Controllers;

use App\Models\NdrRecord;
use App\Models\RtoRecord;
use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Models\ShipmentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NdrController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', NdrRecord::class);

        $query = DB::table('ndr_records')
            ->join('shipments', 'ndr_records.shipment_id', '=', 'shipments.id')
            ->leftJoin('delivery_partners', 'ndr_records.delivery_partner_id', '=', 'delivery_partners.id')
            ->leftJoin('hubs', 'ndr_records.hub_id', '=', 'hubs.id')
            ->select(
                'ndr_records.*',
                'shipments.awb_number',
                'shipments.receiver_name',
                'shipments.receiver_phone',
                'shipments.receiver_city',
                'delivery_partners.name as partner_name',
                'hubs.name as hub_name'
            );

        if ($request->filled('status')) {
            $query->where('ndr_records.status', $request->input('status'));
        }

        if ($request->filled('reason')) {
            $query->where('ndr_records.reason', 'like', "%{$request->input('reason')}%");
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('shipments.awb_number', 'like', "%{$search}%")
                  ->orWhere('shipments.receiver_name', 'like', "%{$search}%")
                  ->orWhere('shipments.receiver_phone', 'like', "%{$search}%");
            });
        }

        $ndrRecords = $query->orderBy('ndr_records.created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return view('ndr.index', compact('ndrRecords'));
    }

    public function show(NdrRecord $ndr)
    {
        $this->authorize('view', $ndr);

        $ndr->load([
            'shipment' => fn ($q) => $q->with(['status', 'merchant', 'currentHub']),
            'deliveryPartner', 'hub',
        ]);

        $shipmentEvents = $ndr->shipment->events()
            ->latest()
            ->limit(10)
            ->get();

        return view('ndr.show', compact('ndr', 'shipmentEvents'));
    }

    public function resolve(NdrRecord $ndr, Request $request)
    {
        $this->authorize('resolve', NdrRecord::class);

        $validated = $request->validate([
            'next_action'       => 'required|in:reattempt,rto,cancelled,delivered',
            'customer_response' => 'nullable|string|max:500',
            'remarks'           => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($ndr, $validated) {
            $ndr->update([
                'status'            => 'resolved',
                'resolved_at'       => now(),
                'next_action'       => $validated['next_action'],
                'customer_response' => $validated['customer_response'] ?? $ndr->customer_response,
                'remarks'           => $validated['remarks'] ?? $ndr->remarks,
            ]);

            if ($validated['next_action'] === 'rto') {
                $this->initiateRtoForNdr($ndr);
            }

            if ($validated['next_action'] === 'delivered') {
                $status = ShipmentStatus::where('slug', 'delivered')->first();
                $ndr->shipment->update([
                    'status_id'           => $status?->id,
                    'actual_delivery_date' => now(),
                ]);

                ShipmentEvent::create([
                    'shipment_id' => $ndr->shipment_id,
                    'status_id'   => $status?->id,
                    'status_slug' => 'delivered',
                    'event_type'  => 'status_update',
                    'description' => 'Delivered after NDR resolution',
                ]);
            }
        });

        return back()->with('success', 'NDR resolved successfully.');
    }

    public function reattempt(NdrRecord $ndr, Request $request)
    {
        $this->authorize('reattempt', NdrRecord::class);

        $validated = $request->validate([
            'reattempt_date' => 'required|date|after:today',
            'remarks'        => 'nullable|string|max:500',
        ]);

        $status = ShipmentStatus::where('slug', 'reattempt')->first();

        DB::transaction(function () use ($ndr, $validated, $status) {
            $ndr->update([
                'status'         => 'reattempt_scheduled',
                'reattempt_date' => $validated['reattempt_date'],
                'remarks'        => $validated['remarks'] ?? $ndr->remarks,
            ]);

            $ndr->shipment->update(['status_id' => $status?->id]);

            ShipmentEvent::create([
                'shipment_id' => $ndr->shipment_id,
                'status_id'   => $status?->id,
                'status_slug' => 'reattempt',
                'event_type'  => 'status_update',
                'description' => "Reattempt scheduled for {$validated['reattempt_date']}",
                'metadata'    => ['ndr_record_id' => $ndr->id, 'reattempt_date' => $validated['reattempt_date']],
            ]);
        });

        return back()->with('success', "Reattempt scheduled for {$validated['reattempt_date']}.");
    }

    public function initiateRto(NdrRecord $ndr)
    {
        $this->authorize('initiateRto', NdrRecord::class);

        DB::transaction(function () use ($ndr) {
            $this->initiateRtoForNdr($ndr);
        });

        return back()->with('success', 'RTO initiated successfully.');
    }

    protected function initiateRtoForNdr(NdrRecord $ndr): void
    {
        $status = ShipmentStatus::where('slug', 'rto_initiated')->first();

        $rto = RtoRecord::create([
            'shipment_id'      => $ndr->shipment_id,
            'ndr_record_id'    => $ndr->id,
            'reason'           => $ndr->reason,
            'initiated_by'     => auth()->id(),
            'status'           => 'initiated',
            'initiated_at'     => now(),
            'remarks'          => "RTO initiated due to NDR: {$ndr->reason}",
        ]);

        $ndr->update(['status' => 'rto_initiated']);
        $ndr->shipment->update(['status_id' => $status?->id, 'is_rto' => true]);

        ShipmentEvent::create([
            'shipment_id' => $ndr->shipment_id,
            'status_id'   => $status?->id,
            'status_slug' => 'rto_initiated',
            'event_type'  => 'status_update',
            'description' => 'RTO initiated due to NDR',
            'metadata'    => ['ndr_record_id' => $ndr->id, 'rto_record_id' => $rto->id],
        ]);
    }
}
