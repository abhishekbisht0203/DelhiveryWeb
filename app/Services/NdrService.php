<?php

namespace App\Services;

use App\Models\DeliveryPartner;
use App\Models\NdrRecord;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class NdrService
{
    protected ShipmentService $shipmentService;
    protected NotificationService $notificationService;

    public function __construct(ShipmentService $shipmentService, NotificationService $notificationService)
    {
        $this->shipmentService = $shipmentService;
        $this->notificationService = $notificationService;
    }

    public function createNdr(
        Shipment $shipment,
        string $reason,
        string $remarks,
        ?DeliveryPartner $partner = null
    ): NdrRecord {
        $ndr = NdrRecord::create([
            'shipment_id'         => $shipment->id,
            'delivery_partner_id' => $partner?->id,
            'reason'              => $reason,
            'remarks'             => $remarks,
            'status'              => 'open',
            'attempt_number'      => ($shipment->events()->where('status', 'delivery_failed')->count()) + 1,
        ]);

        $this->shipmentService->updateStatus($shipment, 'ndr', $reason, null);
        $this->notificationService->deliveryFailed($shipment, $reason);

        return $ndr;
    }

    public function resolveNdr(NdrRecord $ndr, string $action, ?string $remarks = null): NdrRecord
    {
        $ndr->update([
            'status'       => 'resolved',
            'resolution'   => $action,
            'resolved_at'  => now(),
            'resolved_remarks' => $remarks,
        ]);

        return $ndr->fresh();
    }

    public function reattemptNdr(NdrRecord $ndr, \DateTime $reattemptDate): NdrRecord
    {
        DB::transaction(function () use ($ndr, $reattemptDate) {
            $ndr->update([
                'status'           => 'reattempt_scheduled',
                'reattempt_date'   => $reattemptDate,
            ]);

            $this->shipmentService->updateStatus(
                $ndr->shipment,
                'reattempt',
                'Reattempt scheduled for ' . $reattemptDate->format('Y-m-d H:i'),
                null
            );
        });

        return $ndr->fresh();
    }

    public function initiateRto(NdrRecord $ndr, ?string $reason = null): NdrRecord
    {
        DB::transaction(function () use ($ndr, $reason) {
            $ndr->update([
                'status'       => 'rto_initiated',
                'resolution'   => 'rto',
                'resolved_at'  => now(),
            ]);

            $this->shipmentService->updateStatus(
                $ndr->shipment,
                'rto_initiated',
                $reason ?? 'RTO initiated from NDR',
                null
            );
        });

        return $ndr->fresh();
    }

    public function getOpenNdrs(?int $hubId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = NdrRecord::with(['shipment', 'deliveryPartner'])
            ->where('status', 'open');

        if ($hubId) {
            $query->whereHas('shipment', function ($q) use ($hubId) {
                $q->where('origin_hub_id', $hubId)
                  ->orWhere('destination_hub_id', $hubId);
            });
        }

        return $query->orderByDesc('created_at')->get();
    }
}
