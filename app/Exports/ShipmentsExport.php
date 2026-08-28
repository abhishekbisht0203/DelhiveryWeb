<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected ?string $dateFrom;
    protected ?string $dateTo;
    protected ?int $merchantId;
    protected ?string $status;

    public function __construct(?string $dateFrom = null, ?string $dateTo = null, ?int $merchantId = null, ?string $status = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->merchantId = $merchantId;
        $this->status = $status;
    }

    public function collection()
    {
        $query = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->leftJoin('warehouses', 'shipments.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('hubs as origin', 'shipments.origin_hub_id', '=', 'origin.id')
            ->leftJoin('hubs as dest', 'shipments.destination_hub_id', '=', 'dest.id')
            ->leftJoin('delivery_partners', 'shipments.delivery_partner_id', '=', 'delivery_partners.id')
            ->select(
                'shipments.awb_number',
                'shipment_statuses.name as status',
                'merchants.business_name as merchant',
                'shipments.customer_name',
                'shipments.customer_phone',
                'shipments.customer_address',
                'shipments.customer_city',
                'shipments.customer_pincode',
                'shipments.item_description',
                'shipments.item_quantity',
                'shipments.declared_value',
                'shipments.cod_amount',
                'shipments.collectable_amount',
                'shipments.shipping_charges',
                'shipments.weight',
                'shipments.length',
                'shipments.width',
                'shipments.height',
                'shipments.package_type',
                'shipments.payment_type',
                'origin.name as origin_hub',
                'dest.name as destination_hub',
                'shipments.current_hub_city',
                'shipments.status_updated_at',
                'shipments.created_at as order_date',
                'delivery_partners.name as delivery_partner'
            );

        if ($this->dateFrom) {
            $query->where('shipments.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('shipments.created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->merchantId) {
            $query->where('shipments.merchant_id', $this->merchantId);
        }

        if ($this->status) {
            $query->where('shipment_statuses.slug', $this->status);
        }

        return $query->orderBy('shipments.created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'AWB Number',
            'Status',
            'Merchant',
            'Customer Name',
            'Customer Phone',
            'Customer Address',
            'Customer City',
            'Customer Pincode',
            'Item Description',
            'Item Quantity',
            'Declared Value',
            'COD Amount',
            'Collectable Amount',
            'Shipping Charges',
            'Weight (kg)',
            'Length (cm)',
            'Width (cm)',
            'Height (cm)',
            'Package Type',
            'Payment Type',
            'Origin Hub',
            'Destination Hub',
            'Current City',
            'Status Updated At',
            'Order Date',
            'Delivery Partner',
        ];
    }

    public function map($row): array
    {
        return [
            $row->awb_number,
            $row->status,
            $row->merchant,
            $row->customer_name,
            $row->customer_phone,
            $row->customer_address,
            $row->customer_city,
            $row->customer_pincode,
            $row->item_description,
            $row->item_quantity,
            $row->declared_value,
            $row->cod_amount,
            $row->collectable_amount,
            $row->shipping_charges,
            $row->weight,
            $row->length,
            $row->width,
            $row->height,
            $row->package_type,
            $row->payment_type,
            $row->origin_hub,
            $row->destination_hub,
            $row->current_hub_city,
            $row->status_updated_at,
            $row->order_date,
            $row->delivery_partner,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
