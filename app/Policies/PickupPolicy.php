<?php

namespace App\Policies;

use App\Models\PickupRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PickupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('view_pickups');
    }

    public function view(User $user, PickupRequest $pickup): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $pickup->merchant_id === $user->merchant_id;
        }

        if ($user->hasRole('delivery_partner')) {
            return $pickup->assigned_to === $user->delivery_partner_id;
        }

        return $user->hasAnyRole(['ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('view_pickups');
    }

    public function assign(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('assign_pickups');
    }

    public function update(User $user, PickupRequest $pickup): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('delivery_partner')) {
            return $pickup->assigned_to === $user->delivery_partner_id;
        }

        return $user->hasAnyRole(['ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('update_pickups');
    }
}
