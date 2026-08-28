<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'awb_number'    => $this->awb_number,
            'order_id'      => $this->order_id,
            'status'        => $this->status?->slug,
            'status_label'  => $this->status?->name,
            'status_color'  => $this->status?->color,
            'receiver' => [
                'city'    => $this->receiver_city,
                'state'   => $this->receiver_state,
            ],
            'current_hub' => [
                'name' => $this->currentHub?->name,
                'city' => $this->currentHub?->city,
            ],
            'expected_delivery_date' => $this->expected_delivery_date?->toDateString(),
            'pickup_scheduled_at'    => $this->pickup_scheduled_at?->toIso8601String(),
            'actual_delivery_date'   => $this->actual_delivery_date?->toIso8601String(),
            'events' => ShipmentEventResource::collection($this->whenLoaded('events')),
        ];
    }
}
