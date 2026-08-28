<?php

namespace App\Services;

use App\Models\NdrRecord;
use App\Models\RtoRecord;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class RtoService
{
    protected ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function initiateRto(
        Shipment $shipment,
        ?NdrRecord $ndr = null,
        ?string $reason = null
    ): RtoRecord {
        return DB::transaction(function () use ($shipment, $ndr, $reason) {
            $rto = RtoRecord::create([
                'shipment_id'  => $shipment->id,
                'ndr_id'       => $ndr?->id,
                'reason'       => $reason ?? ($ndr?->reason ?? 'Return to origin'),
                'status'       => 'initiated',
                'initiated_at' => now(),
            ]);

            $this->shipmentService->updateStatus(
                $shipment,
                'rto_initiated',
                $reason ?? 'RTO initiated',
                null
            );

            return $rto;
        });
    }

    public function updateRtoStatus(RtoRecord $rto, string $newStatus): RtoRecord
    {
        $validTransitions = [
            'initiated'     => ['in_transit'],
            'in_transit'    => ['at_hub'],
            'at_hub'        => ['completed'],
        ];

        $current = $rto->status;

        if (!isset($validTransitions[$current]) || !in_array($newStatus, $validTransitions[$current])) {
            throw new \InvalidArgumentException(
                "Invalid RTO transition from '{$current}' to '{$newStatus}'"
            );
        }

        $rto->update([
            'status'     => $newStatus,
            'updated_at' => now(),
        ]);

        return $rto->fresh();
    }

    public function completeRto(RtoRecord $rto): RtoRecord
    {
        return DB::transaction(function () use ($rto) {
            $rto->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            $this->shipmentService->updateStatus(
                $rto->shipment,
                'rto_delivered',
                'RTO completed',
                null
            );

            return $rto->fresh();
        });
    }
}
