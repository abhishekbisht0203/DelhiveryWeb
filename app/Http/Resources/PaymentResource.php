<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'shipment'               => new ShipmentResource($this->whenLoaded('shipment')),
            'merchant'               => new MerchantResource($this->whenLoaded('merchant')),
            'type'                   => $this->type,
            'amount'                 => $this->amount,
            'currency'               => $this->currency,
            'status'                 => $this->status,
            'payment_method'         => $this->payment_method,
            'transaction_reference'  => $this->transaction_reference,
            'paid_at'                => $this->paid_at?->toISOString(),
            'notes'                  => $this->notes,
            'processed_by'           => $this->processor?->name,
            'created_at'             => $this->created_at?->toISOString(),
            'updated_at'             => $this->updated_at?->toISOString(),
        ];
    }
}
