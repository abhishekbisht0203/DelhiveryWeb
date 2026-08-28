<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RtoRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'shipment'       => new ShipmentResource($this->whenLoaded('shipment')),
            'ndr_record'     => new NdrRecordResource($this->whenLoaded('ndrRecord')),
            'reason'         => $this->reason,
            'initiated_by'   => $this->initiated_by,
            'rto_awb'        => $this->rto_awb,
            'status'         => $this->status,
            'initiated_at'   => $this->initiated_at?->toISOString(),
            'completed_at'   => $this->completed_at?->toISOString(),
            'remarks'        => $this->remarks,
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
