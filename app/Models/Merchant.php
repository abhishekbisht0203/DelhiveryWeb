<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'business_name',
        'owner_name',
        'phone',
        'email',
        'gst_number',
        'pan_number',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_pincode',
        'cod_enabled',
        'cod_fee_percent',
        'max_cod_amount',
        'monthly_shipment_limit',
        'pricing_tier',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'cod_enabled' => 'boolean',
            'cod_fee_percent' => 'decimal:2',
            'max_cod_amount' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function pickupRequests(): HasMany
    {
        return $this->hasMany(PickupRequest::class);
    }
}
