<?php

namespace App\Http\Controllers;

use App\Models\RtoRecord;
use App\Models\ShipmentStatus;
use App\Models\ShipmentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RtoController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', RtoRecord::class);

        $query = DB::table('rto_records')
            ->join('shipments', 'rto_records.shipment_id', '=', 'shipments.id')
            ->leftJoin('ndr_records', 'rto_records.ndr_record_id', '=', 'ndr_records.id')
            ->select(
                'rto_records.*',
                'shipments.awb_number',
                'shipments.receiver_name',
                'shipments.receiver_city',
                'ndr_records.reason as ndr_reason'
            );

        if ($request->filled('status')) {
            $query->where('rto_records.status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('shipments.awb_number', 'like', "%{$search}%")
                  ->orWhere('rto_records.rto_awb', 'like', "%{$search}%");
            });
        }

        $rtoRecords = $query->orderBy('rto_records.initiated_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return view('rto.index', compact('rtoRecords'));
    }

    public function show(RtoRecord $rto)
    {
        $this->authorize('view', $rto);

        $rto->load([
            'shipment' => fn ($q) => $q->with(['status', 'merchant', 'currentHub', 'originHub', 'destinationHub']),
            'ndrRecord',
        ]);

        $shipmentEvents = $rto->shipment->events()
            ->latest()
            ->limit(10)
            ->get();

        return view('rto.show', compact('rto', 'shipmentEvents'));
    }

    public function updateStatus(RtoRecord $rto, Request $request)
    {
        $this->authorize('updateStatus', RtoRecord::class);

        $validated = $request->validate([
            'status'  => 'required|in:initiated,in_transit,at_hub,delivered,completed',
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($rto, $validated) {
            $rto->update([
                'status'    => $validated['status'],
                'remarks'   => $validated['remarks'] ?? $rto->remarks,
            ]);

            if ($validated['status'] === 'completed') {
                $rto->update(['completed_at' => now()]);

                $status = ShipmentStatus::where('slug', 'rto_delivered')->first();
                $rto->shipment->update(['status_id' => $status?->id, 'is_returned' => true]);

                ShipmentEvent::create([
                    'shipment_id' => $rto->shipment_id,
                    'status_id'   => $status?->id,
                    'status_slug' => 'rto_delivered',
                    'event_type'  => 'status_update',
                    'description' => 'RTO completed and returned to origin',
                    'metadata'    => ['rto_record_id' => $rto->id],
                ]);
            }

            if ($validated['status'] === 'in_transit') {
                $status = ShipmentStatus::where('slug', 'rto_in_transit')->first();
                $rto->shipment->update(['status_id' => $status?->id]);

                ShipmentEvent::create([
                    'shipment_id' => $rto->shipment_id,
                    'status_id'   => $status?->id,
                    'status_slug' => 'rto_in_transit',
                    'event_type'  => 'status_update',
                    'description' => 'RTO shipment in transit',
                ]);
            }

            if ($validated['status'] === 'at_hub') {
                $status = ShipmentStatus::where('slug', 'rto_at_hub')->first();
                $rto->shipment->update(['status_id' => $status?->id]);

                ShipmentEvent::create([
                    'shipment_id' => $rto->shipment_id,
                    'status_id'   => $status?->id,
                    'status_slug' => 'rto_at_hub',
                    'event_type'  => 'status_update',
                    'description' => 'RTO shipment arrived at hub',
                ]);
            }
        });

        return back()->with('success', 'RTO status updated successfully.');
    }
}
