<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CodService
{
    public function recordCollection(
        Shipment $shipment,
        float $amount,
        ?string $reference = null,
        ?User $collector = null
    ): object {
        return DB::table('cod_collections')->insertGetId([
            'shipment_id'  => $shipment->id,
            'merchant_id'  => $shipment->merchant_id,
            'amount'       => $amount,
            'reference'    => $reference,
            'collected_by' => $collector?->id,
            'collected_at' => now(),
            'status'       => 'collected',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function remitToMerchant(
        Merchant $merchant,
        float $amount,
        ?string $reference = null
    ): object {
        return DB::table('cod_remittances')->insertGetId([
            'merchant_id'  => $merchant->id,
            'amount'       => $amount,
            'reference'    => $reference,
            'remitted_at'  => now(),
            'status'       => 'remitted',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function getMerchantCodSummary(
        Merchant $merchant,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array {
        $query = DB::table('shipments')
            ->where('merchant_id', $merchant->id)
            ->where('payment_type', 'cod');

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        $totalCod = (clone $query)->sum('cod_amount');
        $collectedCod = (clone $query)->where('current_status', 'delivered')->sum('cod_amount');
        $pendingCod = (clone $query)->where('current_status', '!=', 'delivered')->sum('cod_amount');

        $remitted = DB::table('cod_remittances')
            ->where('merchant_id', $merchant->id)
            ->sum('amount');

        return [
            'merchant_id'    => $merchant->id,
            'total_cod'      => $totalCod,
            'collected'      => $collectedCod,
            'pending'        => $pendingCod,
            'remitted'       => $remitted,
            'balance'        => $collectedCod - $remitted,
        ];
    }

    public function getPendingCollections(?int $hubId = null): \Illuminate\Support\Collection
    {
        $query = DB::table('shipments')
            ->where('payment_type', 'cod')
            ->where('current_status', '!=', 'delivered')
            ->where('cod_amount', '>', 0);

        if ($hubId) {
            $query->where(function ($q) use ($hubId) {
                $q->where('origin_hub_id', $hubId)
                  ->orWhere('destination_hub_id', $hubId);
            });
        }

        return $query->get();
    }
}
