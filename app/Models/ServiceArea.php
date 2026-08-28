<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'hub_id',
        'name',
        'code',
        'pincodes',
        'cities',
        'states',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'pincodes' => 'array',
            'cities' => 'array',
            'states' => 'array',
        ];
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }
}
