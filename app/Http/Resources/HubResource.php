<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HubResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'code'             => $this->code,
            'address'          => $this->address,
            'city'             => $this->city,
            'state'            => $this->state,
            'pincode'          => $this->pincode,
            'phone'            => $this->phone,
            'email'            => $this->email,
            'manager_name'     => $this->manager_name,
            'latitude'         => $this->latitude,
            'longitude'        => $this->longitude,
            'capacity'         => $this->capacity,
            'status'           => $this->status,
            'operating_hours'  => $this->operating_hours,
            'total_shipments'  => $this->whenCounted('shipments'),
            'created_at'       => $this->created_at?->toISOString(),
            'updated_at'       => $this->updated_at?->toISOString(),
        ];
    }
}
