<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickupRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'shipment'           => new ShipmentResource($this->whenLoaded('shipment')),
            'merchant'           => new MerchantResource($this->whenLoaded('merchant')),
            'hub'                => new HubResource($this->whenLoaded('hub')),
            'assigned_partner'   => new DeliveryPartnerResource($this->whenLoaded('assignedPartner')),
            'pickup_address'     => $this->pickup_address,
            'pickup_city'        => $this->pickup_city,
            'pickup_state'       => $this->pickup_state,
            'pickup_pincode'     => $this->pickup_pincode,
            'pickup_phone'       => $this->pickup_phone,
            'pickup_contact_name'=> $this->pickup_contact_name,
            'requested_date'     => $this->requested_date?->toDateString(),
            'requested_time_slot'=> $this->requested_time_slot,
            'status'             => $this->status,
            'scheduled_at'       => $this->scheduled_at?->toISOString(),
            'picked_up_at'       => $this->picked_up_at?->toISOString(),
            'attempt_count'      => $this->attempt_count,
            'max_attempts'       => $this->max_attempts,
            'failure_reason'     => $this->failure_reason,
            'remarks'            => $this->remarks,
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
