<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
    ) {}

    public function stats(Request $request)
    {
        $stats = $this->shipmentService->getShipmentStats(
            merchantId: $request->user()->merchant_id,
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
        );

        $dailyTrend = DB::table('shipments')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status_id IN (SELECT id FROM shipment_statuses WHERE slug = 'delivered') THEN 1 ELSE 0 END) as delivered")
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $topMerchants = DB::table('shipments')
            ->join('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->select('merchants.business_name', DB::raw('COUNT(*) as shipment_count'))
            ->where('shipments.created_at', '>=', now()->subDays(30))
            ->groupBy('merchants.id', 'merchants.business_name')
            ->orderByDesc('shipment_count')
            ->limit(5)
            ->get();

        return response()->json([
            'stats'         => $stats,
            'daily_trend'   => $dailyTrend,
            'top_merchants' => $topMerchants,
        ]);
    }
}
