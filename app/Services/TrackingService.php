<?php

namespace App\Services;

use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class TrackingService
{
    public function trackByAwb(string $awbNumber): ?Shipment
    {
        return Shipment::with(['status', 'currentHub', 'originHub', 'destinationHub', 'merchant', 'deliveryPartner', 'customer'])
            ->where('awb_number', $awbNumber)
            ->first();
    }

    public function getTrackingEvents(Shipment $shipment): array
    {
        return DB::table('shipment_events')
            ->where('shipment_id', $shipment->id)
            ->orderBy('event_time', 'desc')
            ->get()
            ->map(fn ($event) => [
                'id'           => $event->id,
                'status'       => $event->status,
                'status_label' => $this->getStatusLabel($event->status),
                'description'  => $event->description,
                'hub'          => $event->hub_name,
                'location'     => $event->location,
                'timestamp'    => $event->event_time,
                'created_at'   => $event->created_at,
            ])
            ->toArray();
    }

    public function addTrackingEvent(
        Shipment $shipment,
        string $status,
        string $description,
        ?string $hubName = null,
        ?string $location = null,
        string $source = 'system',
        ?int $actorId = null
    ): void {
        DB::table('shipment_events')->insert([
            'shipment_id' => $shipment->id,
            'status'      => $status,
            'description' => $description,
            'hub_name'    => $hubName,
            'location'    => $location,
            'source'      => $source,
            'actor_id'    => $actorId,
            'event_time'  => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function getStatusLabel(string $status): string
    {
        $labels = [
            'order_placed'        => 'Order Placed',
            'created'             => 'Order Placed',
            'pickup_scheduled'    => 'Pickup Scheduled',
            'pickup_assigned'     => 'Pickup Assigned',
            'picked_up'           => 'Picked Up',
            'pickup_failed'       => 'Pickup Failed',
            'at_origin_hub'       => 'Arrived at Origin Hub',
            'in_transit'          => 'In Transit',
            'at_destination_hub'  => 'Arrived at Destination Hub',
            'out_for_delivery'    => 'Out for Delivery',
            'delivered'           => 'Delivered',
            'delivery_failed'     => 'Delivery Failed',
            'ndr'                 => 'Delivery Attempted (NDR)',
            'reattempt'           => 'Reattempt Scheduled',
            'rto_initiated'       => 'RTO Initiated',
            'rto_in_transit'      => 'RTO In Transit',
            'rto_at_hub'          => 'RTO at Hub',
            'rto_delivered'       => 'Returned to Sender',
            'cancelled'           => 'Cancelled',
            'held'                => 'On Hold',
        ];

        return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public function getPublicTrackingData(string $awbNumber): ?array
    {
        $shipment = $this->trackByAwb($awbNumber);

        if (! $shipment) {
            return null;
        }

        $statusSlug = $shipment->current_status ?? ($shipment->status->slug ?? 'unknown');

        return [
            'awb_number'          => $shipment->awb_number ?? $shipment->awb,
            'status'              => $statusSlug,
            'status_label'        => $this->getStatusLabel($statusSlug),
            'current_city'        => $shipment->current_hub_city ?? null,
            'estimated_delivery'  => $shipment->estimated_delivery,
            'events'              => $this->getTrackingEvents($shipment),
        ];
    }
}
