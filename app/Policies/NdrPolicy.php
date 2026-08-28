<?php

namespace App\Policies;

use App\Models\NdrRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NdrPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive', 'cs_agent'])
            || $user->hasPermissionTo('view_ndr');
    }

    public function view(User $user, NdrRecord $ndr): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $ndr->shipment->merchant_id === $user->merchant_id;
        }

        return $user->hasAnyRole(['ops_manager', 'ops_executive', 'cs_agent'])
            || $user->hasPermissionTo('view_ndr');
    }

    public function resolve(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive', 'cs_agent'])
            || $user->hasPermissionTo('resolve_ndr');
    }

    public function reattempt(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('reattempt_ndr');
    }

    public function initiateRto(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager'])
            || $user->hasPermissionTo('initiate_rto');
    }
}
