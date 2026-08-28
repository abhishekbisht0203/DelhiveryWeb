<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryPartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'phone'           => $this->phone,
            'email'           => $this->email,
            'vehicle_type'    => $this->vehicle_type,
            'vehicle_number'  => $this->vehicle_number,
            'license_number'  => $this->license_number,
            'assigned_areas'  => $this->assigned_areas,
            'status'          => $this->status,
            'current_lat'     => $this->current_lat,
            'current_lng'     => $this->current_lng,
            'last_active_at'  => $this->last_active_at?->toISOString(),
            'hub'             => new HubResource($this->whenLoaded('hub')),
            'total_shipments' => $this->whenCounted('shipments'),
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
