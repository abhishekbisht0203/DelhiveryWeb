<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'merchant_id',
        'organization_id',
        'period_start',
        'period_end',
        'total_shipments',
        'total_amount',
        'cod_collected',
        'cod_remitted',
        'freight_charges',
        'other_charges',
        'tax_amount',
        'total_payable',
        'status',
        'due_date',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'cod_collected' => 'decimal:2',
            'cod_remitted' => 'decimal:2',
            'freight_charges' => 'decimal:2',
            'other_charges' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_payable' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
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
}
