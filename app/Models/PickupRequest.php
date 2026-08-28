<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PickupRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_id',
        'merchant_id',
        'hub_id',
        'pickup_address',
        'pickup_city',
        'pickup_state',
        'pickup_pincode',
        'pickup_phone',
        'pickup_contact_name',
        'requested_date',
        'requested_time_slot',
        'assigned_to',
        'status',
        'scheduled_at',
        'picked_up_at',
        'attempt_count',
        'max_attempts',
        'failure_reason',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'requested_date' => 'date',
            'scheduled_at' => 'datetime',
            'picked_up_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

    public function assignedPartner(): BelongsTo
    {
        return $this->belongsTo(DeliveryPartner::class, 'assigned_to');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(PickupAttempt::class);
    }
}
