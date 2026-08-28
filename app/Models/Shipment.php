<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'merchant_id',
        'user_id',
        'awb_number',
        'order_id',
        'invoice_number',
        'status_id',
        'current_hub_id',
        'origin_hub_id',
        'destination_hub_id',
        'delivery_partner_id',
        'pickup_request_id',
        'sender_name',
        'sender_phone',
        'sender_email',
        'sender_address',
        'sender_city',
        'sender_state',
        'sender_pincode',
        'receiver_name',
        'receiver_phone',
        'receiver_email',
        'receiver_address',
        'receiver_city',
        'receiver_state',
        'receiver_pincode',
        'receiver_landmark',
        'description',
        'quantity',
        'weight',
        'length',
        'width',
        'height',
        'volumetric_weight',
        'declared_value',
        'cod_amount',
        'collected_amount',
        'invoice_amount',
        'freight_charges',
        'other_charges',
        'total_charges',
        'payment_mode',
        'pickup_scheduled_at',
        'pickup_completed_at',
        'expected_delivery_date',
        'actual_delivery_date',
        'delivered_to',
        'delivery_proof',
        'metadata',
        'is_rto',
        'is_returned',
        'rto_reason',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'volumetric_weight' => 'decimal:2',
            'cod_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'invoice_amount' => 'decimal:2',
            'freight_charges' => 'decimal:2',
            'other_charges' => 'decimal:2',
            'total_charges' => 'decimal:2',
            'declared_value' => 'decimal:2',
            'pickup_scheduled_at' => 'datetime',
            'pickup_completed_at' => 'datetime',
            'expected_delivery_date' => 'date',
            'actual_delivery_date' => 'datetime',
            'cancelled_at' => 'datetime',
            'metadata' => 'array',
            'is_rto' => 'boolean',
            'is_returned' => 'boolean',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function originHub(): BelongsTo
    {
        return $this->belongsTo(Hub::class, 'origin_hub_id');
    }

    public function destinationHub(): BelongsTo
    {
        return $this->belongsTo(Hub::class, 'destination_hub_id');
    }

    public function currentHub(): BelongsTo
    {
        return $this->belongsTo(Hub::class, 'current_hub_id');
    }

    public function deliveryPartner(): BelongsTo
    {
        return $this->belongsTo(DeliveryPartner::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ShipmentStatus::class, 'status_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ShipmentMovement::class);
    }

    public function pickupRequest(): HasOne
    {
        return $this->hasOne(PickupRequest::class);
    }

    public function ndrRecords(): HasMany
    {
        return $this->hasMany(NdrRecord::class);
    }

    public function rtoRecord(): HasOne
    {
        return $this->hasOne(RtoRecord::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getVolumetricWeightAttribute(): ?float
    {
        if (is_null($this->length) || is_null($this->width) || is_null($this->height)) {
            return null;
        }

        $divisor = $this->metadata['volumetric_divisor'] ?? 5000;

        return round(((float) $this->length * (float) $this->width * (float) $this->height) / $divisor, 2);
    }

    public function getTotalWeightAttribute(): float
    {
        $actual = (float) ($this->weight ?? 0);
        $volumetric = (float) ($this->volumetric_weight ?? 0);

        return max($actual, $volumetric);
    }

    public function getStatusColorAttribute(): ?string
    {
        return $this->status?->color;
    }

    public function getTrackingTimelineAttribute()
    {
        return $this->events()->orderBy('created_at', 'desc')->get();
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->whereHas('status', function (Builder $q) use ($status) {
            $q->where('slug', $status);
        });
    }

    public function scopeByMerchant(Builder $query, int $merchantId): Builder
    {
        return $query->where('merchant_id', $merchantId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('cancelled_at')
            ->whereNull('actual_delivery_date');
    }
}
