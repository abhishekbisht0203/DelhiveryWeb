<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShipmentCollection extends ResourceCollection
{
    public $collects = ShipmentResource::class;

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'current_page' => $this->resource->currentPage(),
                'last_page'    => $this->resource->lastPage(),
                'per_page'     => $this->resource->perPage(),
                'total'        => $this->resource->total(),
            ],
            'links' => [
                'self'  => $this->resource->url(1),
                'next'  => $this->resource->nextPageUrl(),
                'prev'  => $this->resource->previousPageUrl(),
                'first' => $this->resource->url(1),
                'last'  => $this->resource->url($this->resource->lastPage()),
            ],
        ];
    }
}
