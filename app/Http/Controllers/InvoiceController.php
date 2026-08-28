<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $query = DB::table('invoices')
            ->leftJoin('merchants', 'invoices.merchant_id', '=', 'merchants.id')
            ->select(
                'invoices.*',
                'merchants.business_name as merchant_name'
            );

        if ($request->filled('status')) {
            $query->where('invoices.status', $request->input('status'));
        }

        if ($request->filled('merchant_id')) {
            $query->where('invoices.merchant_id', $request->input('merchant_id'));
        }

        if ($request->filled('date_from')) {
            $query->where('invoices.period_start', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('invoices.period_end', '<=', $request->input('date_to'));
        }

        $invoices = $query->orderBy('invoices.created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load('merchant');

        $shipments = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->select(
                'shipments.awb_number',
                'shipments.receiver_city',
                'shipments.weight',
                'shipments.payment_mode',
                'shipments.cod_amount',
                'shipments.freight_charges',
                'shipments.other_charges',
                'shipment_statuses.name as status_name'
            )
            ->where('shipments.merchant_id', $invoice->merchant_id)
            ->whereBetween('shipments.created_at', [$invoice->period_start, $invoice->period_end->endOfDay()])
            ->get();

        return view('invoices.show', compact('invoice', 'shipments'));
    }

    public function download(Invoice $invoice)
    {
        $this->authorize('download', $invoice);

        $invoice->load('merchant');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download("invoice_{$invoice->invoice_number}.pdf");
    }
}
