<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function shipmentReport(Request $request)
    {
        $query = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id');

        if ($request->filled('date_from')) {
            $query->where('shipments.created_at', '>=', $request->input('date_from'));
        } else {
            $query->where('shipments.created_at', '>=', now()->subDays(30));
        }

        if ($request->filled('date_to')) {
            $query->where('shipments.created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        if ($request->filled('merchant_id')) {
            $query->where('shipments.merchant_id', $request->input('merchant_id'));
        }

        $stats = [
            'total'        => (clone $query)->count(),
            'delivered'    => (clone $query)->where('shipment_statuses.slug', 'delivered')->count(),
            'in_transit'   => (clone $query)->whereIn('shipment_statuses.slug', ['in_transit', 'at_origin_hub', 'at_destination_hub'])->count(),
            'pending'      => (clone $query)->whereIn('shipment_statuses.slug', ['created', 'pickup_scheduled', 'pickup_assigned'])->count(),
            'failed'       => (clone $query)->where('shipment_statuses.slug', 'delivery_failed')->count(),
            'ndr'          => (clone $query)->where('shipment_statuses.slug', 'ndr')->count(),
            'rto'          => (clone $query)->where('shipments.is_rto', true)->count(),
            'cancelled'    => (clone $query)->where('shipment_statuses.slug', 'cancelled')->count(),
        ];

        $dailyTrend = (clone $query)
            ->select(
                DB::raw('DATE(shipments.created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN shipment_statuses.slug = 'delivered' THEN 1 ELSE 0 END) as delivered")
            )
            ->groupBy(DB::raw('DATE(shipments.created_at)'))
            ->orderBy('date')
            ->get();

        $statusBreakdown = (clone $query)
            ->select('shipment_statuses.name as status', 'shipment_statuses.color', DB::raw('COUNT(*) as count'))
            ->groupBy('shipment_statuses.id', 'shipment_statuses.name', 'shipment_statuses.color')
            ->get();

        return view('reports.shipment', compact('stats', 'dailyTrend', 'statusBreakdown'));
    }

    public function deliveryReport(Request $request)
    {
        $query = DB::table('shipments')
            ->join('delivery_partners', 'shipments.delivery_partner_id', '=', 'delivery_partners.id')
            ->select(
                'delivery_partners.id',
                'delivery_partners.name as partner_name',
                DB::raw('COUNT(*) as total_assigned'),
                DB::raw("SUM(CASE WHEN shipments.actual_delivery_date IS NOT NULL THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN shipments.status_id IN (SELECT id FROM shipment_statuses WHERE slug = 'delivery_failed') THEN 1 ELSE 0 END) as failed"),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, shipments.pickup_completed_at, shipments.actual_delivery_date)) as avg_delivery_hours')
            );

        if ($request->filled('date_from')) {
            $query->where('shipments.created_at', '>=', $request->input('date_from'));
        } else {
            $query->where('shipments.created_at', '>=', now()->subDays(30));
        }

        if ($request->filled('date_to')) {
            $query->where('shipments.created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $deliveryStats = $query->groupBy('delivery_partners.id', 'delivery_partners.name')
            ->orderByDesc('delivered')
            ->get();

        $overallStats = [
            'total_delivered'  => $deliveryStats->sum('delivered'),
            'total_failed'     => $deliveryStats->sum('failed'),
            'avg_delivery_hrs' => $deliveryStats->avg('avg_delivery_hours'),
        ];

        return view('reports.delivery', compact('deliveryStats', 'overallStats'));
    }

    public function codReport(Request $request)
    {
        $query = DB::table('shipments')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->select(
                'shipments.merchant_id',
                'merchants.business_name as merchant_name',
                DB::raw('COUNT(*) as total_shipments'),
                DB::raw('SUM(shipments.cod_amount) as total_cod'),
                DB::raw('SUM(shipments.collected_amount) as total_collected'),
                DB::raw('SUM(shipments.cod_amount) - SUM(shipments.collected_amount) as pending_collection')
            )
            ->where('shipments.payment_mode', 'cod');

        if ($request->filled('date_from')) {
            $query->where('shipments.created_at', '>=', $request->input('date_from'));
        } else {
            $query->where('shipments.created_at', '>=', now()->subDays(30));
        }

        if ($request->filled('date_to')) {
            $query->where('shipments.created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        if ($request->filled('merchant_id')) {
            $query->where('shipments.merchant_id', $request->input('merchant_id'));
        }

        $codData = $query->groupBy('shipments.merchant_id', 'merchants.business_name')
            ->orderByDesc('total_cod')
            ->get();

        $totals = [
            'total_shipments'    => $codData->sum('total_shipments'),
            'total_cod'          => $codData->sum('total_cod'),
            'total_collected'    => $codData->sum('total_collected'),
            'pending_collection' => $codData->sum('pending_collection'),
        ];

        return view('reports.cod', compact('codData', 'totals'));
    }

    public function ndrReport(Request $request)
    {
        $query = DB::table('ndr_records')
            ->join('shipments', 'ndr_records.shipment_id', '=', 'shipments.id')
            ->leftJoin('delivery_partners', 'ndr_records.delivery_partner_id', '=', 'delivery_partners.id')
            ->select(
                'ndr_records.reason',
                DB::raw('COUNT(*) as count'),
                DB::raw("SUM(CASE WHEN ndr_records.status = 'resolved' THEN 1 ELSE 0 END) as resolved"),
                DB::raw("SUM(CASE WHEN ndr_records.status = 'reattempt_scheduled' THEN 1 ELSE 0 END) as reattempted"),
                DB::raw("SUM(CASE WHEN ndr_records.next_action = 'rto' THEN 1 ELSE 0 END) as rto_count")
            );

        if ($request->filled('date_from')) {
            $query->where('ndr_records.created_at', '>=', $request->input('date_from'));
        } else {
            $query->where('ndr_records.created_at', '>=', now()->subDays(30));
        }

        if ($request->filled('date_to')) {
            $query->where('ndr_records.created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $ndrByReason = $query->groupBy('ndr_records.reason')->orderByDesc('count')->get();

        $totalNdr = $ndrByReason->sum('count');
        $totalResolved = $ndrByReason->sum('resolved');
        $resolutionRate = $totalNdr > 0 ? round(($totalResolved / $totalNdr) * 100, 2) : 0;

        return view('reports.ndr', compact('ndrByReason', 'totalNdr', 'totalResolved', 'resolutionRate'));
    }

    public function exportReport(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:shipments,delivery,cod,ndr',
            'date_from'   => 'nullable|date',
            'date_to'     => 'nullable|date|after_or_equal:date_from',
            'format'      => 'nullable|in:xlsx,csv',
        ]);

        $filename = "{$validated['report_type']}_report_" . now()->format('Y-m-d_His') . ".{$validated['format']}";

        return Excel::download(
            new \App\Exports\ReportExport($validated),
            $filename
        );
    }
}
