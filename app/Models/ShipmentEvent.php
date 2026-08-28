<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'status_id',
        'status_slug',
        'event_type',
        'description',
        'location',
        'hub_id',
        'actor_type',
        'actor_id',
        'metadata',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ShipmentStatus::class, 'status_id');
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeForShipment(Builder $query, int $shipmentId): Builder
    {
        return $query->where('shipment_id', $shipmentId);
    }
}
