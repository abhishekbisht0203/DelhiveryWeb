<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NdrRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'shipment'          => new ShipmentResource($this->whenLoaded('shipment')),
            'delivery_partner'  => new DeliveryPartnerResource($this->whenLoaded('deliveryPartner')),
            'hub'               => new HubResource($this->whenLoaded('hub')),
            'attempt_number'    => $this->attempt_number,
            'reason'            => $this->reason,
            'remarks'           => $this->remarks,
            'customer_response' => $this->customer_response,
            'next_action'       => $this->next_action,
            'reattempt_date'    => $this->reattempt_date?->toDateString(),
            'status'            => $this->status,
            'resolved_at'       => $this->resolved_at?->toISOString(),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }
}
