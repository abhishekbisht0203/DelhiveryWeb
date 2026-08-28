<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShipmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive', 'cs_agent', 'merchant'])
            || $user->hasPermissionTo('view_shipments');
    }

    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasAnyRole(['ops_manager', 'ops_executive', 'cs_agent'])) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $shipment->merchant_id === $user->merchant_id;
        }

        if ($user->hasRole('delivery_partner')) {
            return $shipment->delivery_partner_id === $user->delivery_partner_id;
        }

        return $user->hasPermissionTo('view_shipments');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive', 'merchant'])
            || $user->hasPermissionTo('create_shipments');
    }

    public function update(User $user, Shipment $shipment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasAnyRole(['ops_manager', 'ops_executive'])) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $shipment->merchant_id === $user->merchant_id
                && in_array($shipment->status?->slug, ['created', 'pickup_scheduled']);
        }

        return $user->hasPermissionTo('update_shipments');
    }

    public function delete(User $user, Shipment $shipment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('ops_manager')) {
            return true;
        }

        if ($user->hasRole('merchant')) {
            return $shipment->merchant_id === $user->merchant_id
                && in_array($shipment->status?->slug, ['created', 'pickup_scheduled']);
        }

        return $user->hasPermissionTo('delete_shipments');
    }

    public function updateStatus(User $user, Shipment $shipment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasAnyRole(['ops_manager', 'ops_executive'])) {
            return true;
        }

        if ($user->hasRole('delivery_partner')) {
            return $shipment->delivery_partner_id === $user->delivery_partner_id;
        }

        return $user->hasPermissionTo('update_shipment_status');
    }

    public function bulkUpload(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'merchant'])
            || $user->hasPermissionTo('bulk_upload_shipments');
    }

    public function export(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager', 'ops_executive'])
            || $user->hasPermissionTo('export_shipments');
    }

    public function viewStats(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ops_manager'])
            || $user->hasPermissionTo('view_shipment_stats');
    }
}
