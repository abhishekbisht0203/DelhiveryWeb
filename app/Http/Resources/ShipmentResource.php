<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'awb_number'            => $this->awb_number,
            'order_id'              => $this->order_id,
            'invoice_number'        => $this->invoice_number,
            'status'                => new ShipmentEventResource($this->whenLoaded('status')),
            'status_slug'           => $this->status?->slug,
            'status_color'          => $this->status_color,
            'payment_mode'          => $this->payment_mode,
            'merchant'              => new MerchantResource($this->whenLoaded('merchant')),
            'origin_hub'            => new HubResource($this->whenLoaded('originHub')),
            'destination_hub'       => new HubResource($this->whenLoaded('destinationHub')),
            'current_hub'           => new HubResource($this->whenLoaded('currentHub')),
            'delivery_partner'      => new DeliveryPartnerResource($this->whenLoaded('deliveryPartner')),
            'sender' => [
                'name'      => $this->sender_name,
                'phone'     => $this->sender_phone,
                'email'     => $this->sender_email,
                'address'   => $this->sender_address,
                'city'      => $this->sender_city,
                'state'     => $this->sender_state,
                'pincode'   => $this->sender_pincode,
            ],
            'receiver' => [
                'name'      => $this->receiver_name,
                'phone'     => $this->receiver_phone,
                'email'     => $this->receiver_email,
                'address'   => $this->receiver_address,
                'city'      => $this->receiver_city,
                'state'     => $this->receiver_state,
                'pincode'   => $this->receiver_pincode,
                'landmark'  => $this->receiver_landmark,
            ],
            'package' => [
                'description'       => $this->description,
                'quantity'          => $this->quantity,
                'weight'            => $this->weight,
                'length'            => $this->length,
                'width'             => $this->width,
                'height'            => $this->height,
                'volumetric_weight' => $this->volumetric_weight,
            ],
            'financials' => [
                'declared_value'   => $this->declared_value,
                'cod_amount'       => $this->cod_amount,
                'collected_amount' => $this->collected_amount,
                'invoice_amount'   => $this->invoice_amount,
                'freight_charges'  => $this->freight_charges,
                'other_charges'    => $this->other_charges,
                'total_charges'    => $this->total_charges,
            ],
            'dates' => [
                'pickup_scheduled_at'    => $this->pickup_scheduled_at?->toISOString(),
                'pickup_completed_at'    => $this->pickup_completed_at?->toISOString(),
                'expected_delivery_date' => $this->expected_delivery_date?->toDateString(),
                'actual_delivery_date'   => $this->actual_delivery_date?->toISOString(),
                'cancelled_at'           => $this->cancelled_at?->toISOString(),
            ],
            'is_rto'                 => $this->is_rto,
            'is_returned'            => $this->is_returned,
            'events'                 => ShipmentEventResource::collection($this->whenLoaded('events')),
            'created_at'             => $this->created_at?->toISOString(),
            'updated_at'             => $this->updated_at?->toISOString(),
        ];
    }
}
