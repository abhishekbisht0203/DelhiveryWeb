<?php

namespace App\Policies;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerchantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager'])
            || $user->hasPermissionTo('view_merchants');
    }

    public function view(User $user, Merchant $merchant): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $user->merchant_id === $merchant->id;
        }

        return $user->hasPermissionTo('view_merchants');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager'])
            || $user->hasPermissionTo('create_merchants');
    }

    public function update(User $user, Merchant $merchant): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $user->merchant_id === $merchant->id;
        }

        return $user->hasPermissionTo('update_merchants');
    }

    public function delete(User $user, Merchant $merchant): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasPermissionTo('delete_merchants');
    }
}
