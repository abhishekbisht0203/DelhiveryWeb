<?php

namespace App\Services;

use App\Models\PickupRequest;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\Event;

class NotificationService
{
    public function shipmentCreated(Shipment $shipment): void
    {
        Event::dispatch('shipment.created', [$shipment]);

        if ($shipment->merchant) {
            Event::dispatch('notification.merchant.shipment_created', [
                'merchant'  => $shipment->merchant,
                'shipment'  => $shipment,
            ]);
        }

        Event::dispatch('notification.customer.shipment_created', [
            'customer_phone' => $shipment->customer_phone,
            'customer_email' => $shipment->customer_email,
            'shipment'       => $shipment,
        ]);
    }

    public function shipmentStatusChanged(Shipment $shipment, string $oldStatus, string $newStatus): void
    {
        Event::dispatch('shipment.status_changed', [
            'shipment'  => $shipment,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        if ($shipment->merchant) {
            Event::dispatch('notification.merchant.status_changed', [
                'merchant'   => $shipment->merchant,
                'shipment'   => $shipment,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
        }

        Event::dispatch('notification.customer.status_changed', [
            'customer_phone' => $shipment->customer_phone,
            'customer_email' => $shipment->customer_email,
            'shipment'       => $shipment,
            'old_status'     => $oldStatus,
            'new_status'     => $newStatus,
        ]);
    }

    public function pickupScheduled(PickupRequest $pickup): void
    {
        Event::dispatch('pickup.scheduled', [$pickup]);

        if ($pickup->merchant) {
            Event::dispatch('notification.merchant.pickup_scheduled', [
                'merchant' => $pickup->merchant,
                'pickup'   => $pickup,
            ]);
        }
    }

    public function deliveryFailed(Shipment $shipment, string $reason): void
    {
        Event::dispatch('shipment.delivery_failed', [
            'shipment' => $shipment,
            'reason'   => $reason,
        ]);

        if ($shipment->merchant) {
            Event::dispatch('notification.merchant.delivery_failed', [
                'merchant' => $shipment->merchant,
                'shipment' => $shipment,
                'reason'   => $reason,
            ]);
        }

        Event::dispatch('ndr.created', [
            'shipment' => $shipment,
            'reason'   => $reason,
        ]);
    }
}
