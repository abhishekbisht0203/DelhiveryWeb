<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RtoRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'ndr_record_id',
        'reason',
        'initiated_by',
        'rto_awb',
        'status',
        'initiated_at',
        'completed_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'initiated_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function ndrRecord(): BelongsTo
    {
        return $this->belongsTo(NdrRecord::class);
    }
}
