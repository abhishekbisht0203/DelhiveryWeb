<?php

namespace App\Policies;

use App\Models\Hub;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HubPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('view_hubs');
    }

    public function view(User $user, Hub $hub): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasAnyRole(['ops_manager', 'ops_executive'])) {
            return true;
        }

        return $user->hasPermissionTo('view_hubs');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasPermissionTo('create_hubs');
    }

    public function update(User $user, Hub $hub): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasPermissionTo('update_hubs');
    }

    public function delete(User $user, Hub $hub): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasPermissionTo('delete_hubs');
    }
}
