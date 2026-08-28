<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'finance'])
            || $user->hasPermissionTo('view_invoices');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $invoice->merchant_id === $user->merchant_id;
        }

        return $user->hasAnyRole(['ops_manager', 'finance'])
            || $user->hasPermissionTo('view_invoices');
    }

    public function download(User $user, Invoice $invoice): bool
    {
        return $this->view($user, $invoice);
    }
}
