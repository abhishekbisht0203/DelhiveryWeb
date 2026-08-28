<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected string $reportType;
    protected array $filters;

    public function __construct(string $reportType, array $filters = [])
    {
        $this->reportType = $reportType;
        $this->filters = $filters;
    }

    public function collection()
    {
        return match ($this->reportType) {
            'shipments'  => $this->getShipmentData(),
            'delivery'   => $this->getDeliveryData(),
            'cod'        => $this->getCodData(),
            'ndr'        => $this->getNdrData(),
            default      => collect(),
        };
    }

    public function headings(): array
    {
        return match ($this->reportType) {
            'shipments' => [
                'AWB Number', 'Merchant', 'Status', 'Payment Type', 'Origin',
                'Destination', 'Current City', 'COD Amount', 'Weight', 'Created At',
            ],
            'delivery' => [
                'Delivery Partner', 'Total Delivered', 'Total Failed', 'Success Rate (%)',
                'COD Collected', 'Average Delivery Time (hrs)',
            ],
            'cod' => [
                'Merchant', 'Total Collections', 'COD Amount', 'Shipping Charges',
                'Net Payable', 'Remittance Status',
            ],
            'ndr' => [
                'AWB Number', 'Reason', 'Attempt', 'Status', 'Next Action',
                'Created At', 'Resolved At',
            ],
            default => [],
        };
    }

    public function map($row): array
    {
        return match ($this->reportType) {
            'shipments' => [
                $row->awb_number, $row->merchant, $row->status, $row->payment_type,
                $row->origin_hub, $row->destination_hub, $row->current_hub_city,
                $row->cod_amount, $row->weight, $row->created_at,
            ],
            'delivery' => [
                $row->partner_name, $row->delivered_count, $row->failed_count,
                $row->success_rate, $row->cod_collected, $row->avg_delivery_time,
            ],
            'cod' => [
                $row->merchant_name, $row->total_collections, $row->cod_amount,
                $row->shipping_charges, $row->net_payable, $row->remittance_status,
            ],
            'ndr' => [
                $row->awb_number, $row->reason, $row->attempt_number, $row->status,
                $row->next_action, $row->created_at, $row->resolved_at,
            ],
            default => [],
        };
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    protected function getShipmentData()
    {
        $query = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->leftJoin('hubs as origin', 'shipments.origin_hub_id', '=', 'origin.id')
            ->leftJoin('hubs as dest', 'shipments.destination_hub_id', '=', 'dest.id')
            ->select(
                'shipments.awb_number', 'merchants.business_name as merchant',
                'shipment_statuses.name as status', 'shipments.payment_type',
                'origin.name as origin_hub', 'dest.name as destination_hub',
                'shipments.current_hub_city', 'shipments.cod_amount',
                'shipments.weight', 'shipments.created_at'
            );

        if (! empty($this->filters['date_from'])) {
            $query->where('shipments.created_at', '>=', $this->filters['date_from']);
        }
        if (! empty($this->filters['date_to'])) {
            $query->where('shipments.created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        return $query->orderBy('shipments.created_at', 'desc')->get();
    }

    protected function getDeliveryData()
    {
        $query = DB::table('shipments')
            ->join('delivery_partners', 'shipments.delivery_partner_id', '=', 'delivery_partners.id')
            ->select(
                'delivery_partners.name as partner_name',
                DB::raw("SUM(CASE WHEN shipment_statuses.slug = 'delivered' THEN 1 ELSE 0 END) as delivered_count"),
                DB::raw("SUM(CASE WHEN shipment_statuses.slug = 'ndr' THEN 1 ELSE 0 END) as failed_count"),
                DB::raw("ROUND(SUM(CASE WHEN shipment_statuses.slug = 'delivered' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as success_rate"),
                DB::raw("SUM(CASE WHEN shipment_statuses.slug = 'delivered' THEN shipments.cod_amount ELSE 0 END) as cod_collected"),
                DB::raw("ROUND(AVG(TIMESTAMPDIFF(HOUR, shipments.created_at, shipments.status_updated_at)), 1) as avg_delivery_time")
            )
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->groupBy('delivery_partners.id', 'delivery_partners.name');

        if (! empty($this->filters['date_from'])) {
            $query->where('shipments.created_at', '>=', $this->filters['date_from']);
        }
        if (! empty($this->filters['date_to'])) {
            $query->where('shipments.created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        return $query->orderByDesc('delivered_count')->get();
    }

    protected function getCodData()
    {
        $query = DB::table('payments')
            ->join('merchants', 'payments.merchant_id', '=', 'merchants.id')
            ->select(
                'merchants.business_name as merchant_name',
                DB::raw('COUNT(*) as total_collections'),
                DB::raw('SUM(CASE WHEN payments.type = "cod_collection" THEN payments.amount ELSE 0 END) as cod_amount'),
                DB::raw('SUM(CASE WHEN payments.type = "shipping_charge" THEN payments.amount ELSE 0 END) as shipping_charges'),
                DB::raw('SUM(CASE WHEN payments.type = "cod_collection" THEN payments.amount ELSE 0 END) - SUM(CASE WHEN payments.type = "cod_collection" THEN payments.fee ELSE 0 END) as net_payable'),
                DB::raw('CASE WHEN SUM(CASE WHEN payments.type = "cod_remittance" THEN 1 ELSE 0 END) > 0 THEN "Done" ELSE "Pending" END as remittance_status')
            )
            ->groupBy('merchants.id', 'merchants.business_name');

        if (! empty($this->filters['date_from'])) {
            $query->where('payments.created_at', '>=', $this->filters['date_from']);
        }
        if (! empty($this->filters['date_to'])) {
            $query->where('payments.created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        return $query->orderByDesc('cod_amount')->get();
    }

    protected function getNdrData()
    {
        $query = DB::table('ndr_records')
            ->join('shipments', 'ndr_records.shipment_id', '=', 'shipments.id')
            ->select(
                'shipments.awb_number', 'ndr_records.reason', 'ndr_records.attempt_number',
                'ndr_records.status', 'ndr_records.next_action',
                'ndr_records.created_at', 'ndr_records.resolved_at'
            );

        if (! empty($this->filters['date_from'])) {
            $query->where('ndr_records.created_at', '>=', $this->filters['date_from']);
        }
        if (! empty($this->filters['date_to'])) {
            $query->where('ndr_records.created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        return $query->orderBy('ndr_records.created_at', 'desc')->get();
    }
}
