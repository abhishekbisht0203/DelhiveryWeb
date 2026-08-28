<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'business_name'          => $this->business_name,
            'owner_name'             => $this->owner_name,
            'phone'                  => $this->phone,
            'email'                  => $this->email,
            'gst_number'             => $this->gst_number,
            'pan_number'             => $this->pan_number,
            'billing_address'        => $this->billing_address,
            'billing_city'           => $this->billing_city,
            'billing_state'          => $this->billing_state,
            'billing_pincode'        => $this->billing_pincode,
            'cod_enabled'            => $this->cod_enabled,
            'cod_fee_percent'        => $this->cod_fee_percent,
            'max_cod_amount'         => $this->max_cod_amount,
            'monthly_shipment_limit' => $this->monthly_shipment_limit,
            'pricing_tier'           => $this->pricing_tier,
            'status'                 => $this->status,
            'notes'                  => $this->notes,
            'total_shipments'        => $this->whenCounted('shipments'),
            'created_at'             => $this->created_at?->toISOString(),
            'updated_at'             => $this->updated_at?->toISOString(),
        ];
    }
}
