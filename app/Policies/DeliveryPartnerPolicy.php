<?php

namespace App\Policies;

use App\Models\DeliveryPartner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryPartnerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('view_delivery_partners');
    }

    public function view(User $user, DeliveryPartner $partner): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('delivery_partner')) {
            return $user->id === $partner->user_id;
        }

        return $user->hasAnyRole(['ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('view_delivery_partners');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasPermissionTo('create_delivery_partners');
    }

    public function update(User $user, DeliveryPartner $partner): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('delivery_partner')) {
            return $user->id === $partner->user_id;
        }

        return $user->hasPermissionTo('update_delivery_partners');
    }

    public function delete(User $user, DeliveryPartner $partner): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasPermissionTo('delete_delivery_partners');
    }
}
