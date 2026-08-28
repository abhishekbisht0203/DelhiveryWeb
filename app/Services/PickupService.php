<?php

namespace App\Services;

use App\Models\DeliveryPartner;
use App\Models\PickupRequest;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class PickupService
{
    protected NotificationService $notificationService;
    protected ShipmentService $shipmentService;

    public function __construct(NotificationService $notificationService, ShipmentService $shipmentService)
    {
        $this->notificationService = $notificationService;
        $this->shipmentService = $shipmentService;
    }

    public function createPickup(Shipment $shipment, array $data): PickupRequest
    {
        $pickup = PickupRequest::create([
            'shipment_id'      => $shipment->id,
            'merchant_id'      => $shipment->merchant_id,
            'hub_id'           => $data['hub_id'] ?? $shipment->origin_hub_id,
            'scheduled_at'     => $data['scheduled_at'] ?? null,
            'status'           => 'pending',
            'pickup_address'   => $data['pickup_address'] ?? null,
            'pickup_contact'   => $data['pickup_contact'] ?? null,
            'pickup_notes'     => $data['pickup_notes'] ?? null,
        ]);

        $this->notificationService->pickupScheduled($pickup);

        return $pickup;
    }

    public function assignPickup(PickupRequest $pickup, DeliveryPartner $partner): PickupRequest
    {
        $pickup->update([
            'delivery_partner_id' => $partner->id,
            'status'              => 'assigned',
        ]);

        return $pickup->fresh();
    }

    public function schedulePickup(PickupRequest $pickup, \DateTime $scheduledAt): PickupRequest
    {
        $pickup->update([
            'scheduled_at' => $scheduledAt,
            'status'       => 'scheduled',
        ]);

        return $pickup->fresh();
    }

    public function markPickedUp(PickupRequest $pickup, ?string $remarks = null): PickupRequest
    {
        DB::transaction(function () use ($pickup, $remarks) {
            $pickup->update([
                'status'     => 'picked_up',
                'picked_up_at' => now(),
                'remarks'    => $remarks,
            ]);

            $this->shipmentService->updateStatus(
                $pickup->shipment,
                'picked_up',
                $remarks ?? 'Package picked up',
                null
            );
        });

        return $pickup->fresh();
    }

    public function markFailed(PickupRequest $pickup, string $reason): PickupRequest
    {
        DB::transaction(function () use ($pickup, $reason) {
            $pickup->update([
                'status'  => 'failed',
                'failure_reason' => $reason,
                'failed_at' => now(),
            ]);

            $this->shipmentService->updateStatus(
                $pickup->shipment,
                'pickup_failed',
                $reason,
                null
            );
        });

        return $pickup->fresh();
    }

    public function getPendingPickups(?int $hubId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = PickupRequest::with(['shipment', 'merchant', 'deliveryPartner'])
            ->whereIn('status', ['pending', 'assigned', 'scheduled']);

        if ($hubId) {
            $query->where('hub_id', $hubId);
        }

        return $query->orderBy('scheduled_at')->get();
    }
}
