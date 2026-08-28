<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'from_hub_id',
        'to_hub_id',
        'movement_type',
        'status',
        'dispatched_at',
        'arrived_at',
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'dispatched_at' => 'datetime',
            'arrived_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function fromHub(): BelongsTo
    {
        return $this->belongsTo(Hub::class, 'from_hub_id');
    }

    public function toHub(): BelongsTo
    {
        return $this->belongsTo(Hub::class, 'to_hub_id');
    }
}
