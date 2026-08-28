<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Shipment;
use App\Models\Hub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function exportShipments(Request $request): StreamedResponse
    {
        $query = Shipment::query();

        if ($status = $request->input('status')) {
            $query->where('current_status', $status);
        }

        if ($merchantId = $request->input('merchant_id')) {
            $query->where('merchant_id', $merchantId);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        $columns = [
            'awb', 'order_id', 'customer_name', 'customer_phone', 'customer_address',
            'customer_city', 'customer_state', 'customer_pincode', 'current_status',
            'payment_type', 'cod_amount', 'declared_value', 'weight', 'pieces',
            'created_at', 'delivered_at',
        ];

        return response()->stream(function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            $query->chunk(500, function ($shipments) use ($handle, $columns) {
                foreach ($shipments as $shipment) {
                    $row = [];
                    foreach ($columns as $col) {
                        $row[] = $shipment->{$col} ?? '';
                    }
                    fputcsv($handle, $row);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shipments_export.csv"',
        ]);
    }

    public function exportCODReport(Merchant $merchant, string $dateFrom, string $dateTo): StreamedResponse
    {
        $shipments = Shipment::where('merchant_id', $merchant->id)
            ->where('payment_type', 'cod')
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', $dateTo . ' 23:59:59')
            ->get();

        return response()->stream(function () use ($shipments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['AWB', 'Order ID', 'Customer', 'City', 'COD Amount', 'Status', 'Delivered At']);

            foreach ($shipments as $shipment) {
                fputcsv($handle, [
                    $shipment->awb,
                    $shipment->order_id,
                    $shipment->customer_name,
                    $shipment->customer_city,
                    $shipment->cod_amount,
                    $shipment->current_status,
                    $shipment->delivered_at,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"cod_report_{$merchant->id}_{$dateFrom}_{$dateTo}.csv\"",
        ]);
    }

    public function exportDeliveryReport(
        ?int $hubId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): StreamedResponse {
        $query = Shipment::query();

        if ($hubId) {
            $query->where(function ($q) use ($hubId) {
                $q->where('origin_hub_id', $hubId)
                  ->orWhere('destination_hub_id', $hubId);
            });
        }

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['AWB', 'Order ID', 'Status', 'Origin Hub', 'Destination Hub', 'Created', 'Delivered At']);

            $query->chunk(500, function ($shipments) use ($handle) {
                foreach ($shipments as $shipment) {
                    fputcsv($handle, [
                        $shipment->awb,
                        $shipment->order_id,
                        $shipment->current_status,
                        $shipment->originHub->name ?? '',
                        $shipment->destinationHub->name ?? '',
                        $shipment->created_at,
                        $shipment->delivered_at,
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="delivery_report.csv"',
        ]);
    }
}
