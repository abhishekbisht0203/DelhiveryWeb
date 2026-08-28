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
                'id'          => $event->id,
                'status'      => $event->status,
                'status_label' => $this->getStatusLabel($event->status),
                'description' => $event->description,
                'hub'         => $event->hub_name,
                'location'    => $event->location,
                'timestamp'   => $event->event_time,
                'created_at'  => $event->created_at,
            ])
            ->toArray();
    }

    public function getStatusLabel(string $status): string
    {
        $labels = [
            'order_placed'     => 'Order Placed',
            'picked_up'        => 'Picked Up',
            'in_transit'       => 'In Transit',
            'arrived_hub'      => 'Arrived at Hub',
            'out_for_delivery' => 'Out for Delivery',
            'delivered'        => 'Delivered',
            'ndr'              => 'Delivery Attempted',
            'rto_initiated'    => 'RTO Initiated',
            'rto_delivered'    => 'Returned to Sender',
            'cancelled'        => 'Cancelled',
            'hold'             => 'On Hold',
        ];

        return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public function getPublicTrackingData(string $awbNumber): ?array
    {
        $shipment = $this->trackByAwb($awbNumber);

        if (! $shipment) {
            return null;
        }

        return [
            'awb_number'      => $shipment->awb_number,
            'status'          => $shipment->status->slug ?? 'unknown',
            'status_label'    => $this->getStatusLabel($shipment->status->slug ?? ''),
            'current_city'    => $shipment->current_hub_city ?? null,
            'estimated_delivery' => $shipment->estimated_delivery,
            'events'          => $this->getTrackingEvents($shipment),
        ];
    }
}
