<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hub extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'address',
        'city',
        'state',
        'pincode',
        'phone',
        'email',
        'manager_name',
        'latitude',
        'longitude',
        'capacity',
        'status',
        'operating_hours',
    ];

    protected function casts(): array
    {
        return [
            'operating_hours' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function serviceAreas(): HasMany
    {
        return $this->hasMany(ServiceArea::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'current_hub_id');
    }

    public function deliveryPartners(): HasMany
    {
        return $this->hasMany(DeliveryPartner::class);
    }

    public function movementsFrom(): HasMany
    {
        return $this->hasMany(ShipmentMovement::class, 'from_hub_id');
    }

    public function movementsTo(): HasMany
    {
        return $this->hasMany(ShipmentMovement::class, 'to_hub_id');
    }
}
