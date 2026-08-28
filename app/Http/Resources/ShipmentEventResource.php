<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status_slug' => $this->status_slug,
            'event_type'  => $this->event_type,
            'description' => $this->description,
            'location'    => $this->location,
            'hub'         => new HubResource($this->whenLoaded('hub')),
            'actor_type'  => $this->actor_type,
            'actor_id'    => $this->actor_id,
            'metadata'    => $this->metadata,
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
