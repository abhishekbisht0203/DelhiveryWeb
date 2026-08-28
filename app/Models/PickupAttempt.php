<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_request_id',
        'delivery_partner_id',
        'status',
        'attempted_at',
        'failure_reason',
        'remarks',
        'proof_image',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
        ];
    }

    public function pickupRequest(): BelongsTo
    {
        return $this->belongsTo(PickupRequest::class);
    }

    public function deliveryPartner(): BelongsTo
    {
        return $this->belongsTo(DeliveryPartner::class);
    }
}
