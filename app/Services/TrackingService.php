<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentEvent;
use Illuminate\Support\Facades\DB;

class TrackingService
{
    public function trackByAwb(string $awb): ?Shipment
    {
        return Shipment::with(['events' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->where('awb', $awb)->first();
    }

    public function trackByOrderId(string $orderId): \Illuminate\Database\Eloquent\Collection
    {
        return Shipment::with(['events' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->where('order_id', $orderId)->get();
    }

    public function getTrackingTimeline(Shipment $shipment): array
    {
        $events = ShipmentEvent::where('shipment_id', $shipment->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($event) {
                return [
                    'status'      => $event->status,
                    'description' => $event->description,
                    'location'    => $event->location,
                    'hub_id'      => $event->hub_id,
                    'actor_type'  => $event->actor_type,
                    'actor_id'    => $event->actor_id,
                    'timestamp'   => $event->created_at->toIso8601String(),
                ];
            })
            ->toArray();

        return [
            'awb'     => $shipment->awb,
            'status'  => $shipment->current_status,
            'events'  => $events,
        ];
    }

    public function addTrackingEvent(
        Shipment $shipment,
        string $status,
        string $description,
        ?string $location = null,
        ?int $hubId = null,
        ?string $actorType = null,
        ?int $actorId = null
    ): ShipmentEvent {
        return ShipmentEvent::create([
            'shipment_id' => $shipment->id,
            'status'      => $status,
            'description' => $description,
            'location'    => $location,
            'hub_id'      => $hubId,
            'actor_type'  => $actorType,
            'actor_id'    => $actorId,
        ]);
    }
}
