<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryPartner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'hub_id',
        'user_id',
        'name',
        'phone',
        'email',
        'vehicle_type',
        'vehicle_number',
        'license_number',
        'aadhar_number',
        'assigned_areas',
        'status',
        'current_lat',
        'current_lng',
        'last_active_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_areas' => 'array',
            'last_active_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function pickupRequests(): HasMany
    {
        return $this->hasMany(PickupRequest::class, 'assigned_to');
    }

    public function ndrRecords(): HasMany
    {
        return $this->hasMany(NdrRecord::class);
    }
}
