<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\Hub;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function getStats(?int $merchantId = null): array
    {
        $cacheKey = "dashboard_stats_{$merchantId}";

        return Cache::remember($cacheKey, 300, function () use ($merchantId) {
            $baseQuery = Shipment::query();
            if ($merchantId) {
                $baseQuery->where('merchant_id', $merchantId);
            }

            $today = now()->toDateString();

            $totalShipments = (clone $baseQuery)->count();
            $todayShipments = (clone $baseQuery)->whereDate('created_at', $today)->count();
            $inTransit = (clone $baseQuery)->where('current_status', 'in_transit')->count();
            $outForDelivery = (clone $baseQuery)->where('current_status', 'out_for_delivery')->count();
            $delivered = (clone $baseQuery)->where('current_status', 'delivered')->count();
            $pendingPickup = (clone $baseQuery)->whereIn('current_status', ['created', 'pickup_scheduled', 'pickup_assigned'])->count();
            $pickupFailed = (clone $baseQuery)->where('current_status', 'pickup_failed')->count();
            $ndrCount = (clone $baseQuery)->where('current_status', 'ndr')->count();
            $rtoCount = (clone $baseQuery)->where('current_status', 'rto_initiated')->count();
            $cancelled = (clone $baseQuery)->where('current_status', 'cancelled')->count();

            $codQuery = (clone $baseQuery)->where('payment_type', 'cod');
            $codTotal = (clone $codQuery)->sum('cod_amount');
            $codPending = (clone $codQuery)->where('current_status', '!=', 'delivered')->sum('cod_amount');
            $codCollected = (clone $codQuery)->where('current_status', 'delivered')->sum('cod_amount');
            $codRemitted = DB::table('cod_remittances')
                ->where('merchant_id', $merchantId)
                ->sum('amount');

            $revenue = (clone $baseQuery)->where('payment_type', 'prepaid')->sum('declared_value');

            $totalForRate = $totalShipments - $cancelled;
            $deliverySuccessRate = $totalForRate > 0 ? round(($delivered / $totalForRate) * 100, 2) : 0;
            $failedDeliveryRate = $totalForRate > 0 ? round((($pickupFailed + $ndrCount + $rtoCount + $cancelled) / $totalForRate) * 100, 2) : 0;

            $avgDeliveryTime = Shipment::where('current_status', 'delivered')
                ->whereNotNull('delivered_at')
                ->whereNotNull('created_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, delivered_at)) as avg_hours')
                ->value('avg_hours');

            return [
                'total_shipments'       => $totalShipments,
                'today_shipments'       => $todayShipments,
                'in_transit'            => $inTransit,
                'out_for_delivery'      => $outForDelivery,
                'delivered'             => $delivered,
                'pending_pickup'        => $pendingPickup,
                'pickup_failed'         => $pickupFailed,
                'ndr_count'             => $ndrCount,
                'rto_count'             => $rtoCount,
                'cancelled'             => $cancelled,
                'cod_total'             => $codTotal,
                'cod_pending'           => $codPending,
                'cod_collected'         => $codCollected,
                'cod_remitted'          => $codRemitted,
                'revenue'               => $revenue,
                'delivery_success_rate' => $deliverySuccessRate,
                'failed_delivery_rate'  => $failedDeliveryRate,
                'avg_delivery_time'     => $avgDeliveryTime ? round($avgDeliveryTime, 1) : null,
            ];
        });
    }

    public function getDailyShipmentsChart(int $days = 30): array
    {
        $cacheKey = "daily_shipments_chart_{$days}";

        return Cache::remember($cacheKey, 300, function () use ($days) {
            return Shipment::where('created_at', '>=', now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
        });
    }

    public function getStatusDistribution(?int $merchantId = null): array
    {
        $cacheKey = "status_distribution_{$merchantId}";

        return Cache::remember($cacheKey, 300, function () use ($merchantId) {
            $query = Shipment::selectRaw('current_status, COUNT(*) as count')
                ->groupBy('current_status');

            if ($merchantId) {
                $query->where('merchant_id', $merchantId);
            }

            return $query->get()
                ->pluck('count', 'current_status')
                ->toArray();
        });
    }

    public function getTopHubs(int $limit = 10): array
    {
        $cacheKey = "top_hubs_{$limit}";

        return Cache::remember($cacheKey, 300, function () use ($limit) {
            return Hub::selectRaw('hubs.id, hubs.name, COUNT(shipments.id) as shipment_count')
                ->leftJoin('shipments', function ($join) {
                    $join->on('hubs.id', '=', 'shipments.origin_hub_id')
                        ->orWhere('hubs.id', '=', 'shipments.destination_hub_id');
                })
                ->groupBy('hubs.id', 'hubs.name')
                ->orderByDesc('shipment_count')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getCODCollectionChart(int $days = 30): array
    {
        $cacheKey = "cod_collection_chart_{$days}";

        return Cache::remember($cacheKey, 300, function () use ($days) {
            return DB::table('shipments')
                ->where('payment_type', 'cod')
                ->where('current_status', 'delivered')
                ->where('delivered_at', '>=', now()->subDays($days))
                ->selectRaw('DATE(delivered_at) as date, SUM(cod_amount) as total, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($row) {
                    return [
                        'date'  => $row->date,
                        'total' => (float) $row->total,
                        'count' => (int) $row->count,
                    ];
                })
                ->toArray();
        });
    }
}
