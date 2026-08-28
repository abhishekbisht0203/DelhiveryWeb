<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Import extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'total_rows',
        'successful_rows',
        'failed_rows',
        'status',
        'error_file_path',
        'metadata',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function errors(): HasMany
    {
        return $this->hasMany(ImportError::class);
    }
}
