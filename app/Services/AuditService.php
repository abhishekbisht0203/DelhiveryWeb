<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public function log(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null
    ): AuditLog {
        return AuditLog::create([
            'action'      => $action,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->id,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'user_id'        => $user?->id ?? auth()->id(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    public function getForModel(Model $model): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('auditable_type', get_class($model))
            ->where('auditable_id', $model->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getForUser(User $user, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
