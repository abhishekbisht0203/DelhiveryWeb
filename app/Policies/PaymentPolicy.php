<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'finance'])
            || $user->hasPermissionTo('view_payments');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $payment->merchant_id === $user->merchant_id;
        }

        return $user->hasAnyRole(['ops_manager', 'finance'])
            || $user->hasPermissionTo('view_payments');
    }

    public function processRemittance(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'finance'])
            || $user->hasPermissionTo('process_remittance');
    }
}
