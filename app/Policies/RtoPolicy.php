<?php

namespace App\Policies;

use App\Models\RtoRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RtoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('view_rto');
    }

    public function view(User $user, RtoRecord $rto): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $rto->shipment->merchant_id === $user->merchant_id;
        }

        return $user->hasAnyRole(['ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('view_rto');
    }

    public function updateStatus(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('update_rto_status');
    }
}
