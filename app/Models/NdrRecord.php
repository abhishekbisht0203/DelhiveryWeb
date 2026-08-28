<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NdrRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'delivery_partner_id',
        'hub_id',
        'attempt_number',
        'reason',
        'remarks',
        'customer_response',
        'next_action',
        'reattempt_date',
        'status',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'reattempt_date' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function deliveryPartner(): BelongsTo
    {
        return $this->belongsTo(DeliveryPartner::class);
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }
}
