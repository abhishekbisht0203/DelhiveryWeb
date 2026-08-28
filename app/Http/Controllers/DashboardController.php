<?php

namespace App\Http\Controllers;

use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
    ) {}

    public function index(Request $request)
    {
        $stats = $this->shipmentService->getShipmentStats(
            merchantId: $request->user()->merchant_id,
        );

        $recentShipments = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->select(
                'shipments.id',
                'shipments.awb_number',
                'shipments.receiver_name',
                'shipments.receiver_city',
                'shipments.payment_mode',
                'shipments.cod_amount',
                'shipment_statuses.slug as status',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'merchants.business_name as merchant_name',
                'shipments.created_at'
            )
            ->latest('shipments.created_at')
            ->limit(10)
            ->get();

        $dailyStats = DB::table('shipments')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status_id IN (SELECT id FROM shipment_statuses WHERE slug = 'delivered') THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status_id IN (SELECT id FROM shipment_statuses WHERE slug IN ('in_transit','at_origin_hub','at_destination_hub')) THEN 1 ELSE 0 END) as in_transit")
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

        return view('dashboard', compact('stats', 'recentShipments', 'dailyStats', 'topMerchants'));
    }
}
