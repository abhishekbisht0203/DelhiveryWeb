<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShipmentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'group',
        'sort_order',
        'color',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'status_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNormal(Builder $query): Builder
    {
        return $query->where('group', 'normal');
    }

    public function scopeExceptions(Builder $query): Builder
    {
        return $query->where('group', 'exception');
    }
}
