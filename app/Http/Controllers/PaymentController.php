<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = DB::table('payments')
            ->leftJoin('shipments', 'payments.shipment_id', '=', 'shipments.id')
            ->leftJoin('merchants', 'payments.merchant_id', '=', 'merchants.id')
            ->select(
                'payments.*',
                'shipments.awb_number',
                'merchants.business_name as merchant_name'
            );

        if ($request->filled('type')) {
            $query->where('payments.type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('payments.status', $request->input('status'));
        }

        if ($request->filled('merchant_id')) {
            $query->where('payments.merchant_id', $request->input('merchant_id'));
        }

        if ($request->filled('date_from')) {
            $query->where('payments.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('payments.created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $payments = $query->orderBy('payments.created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return view('payments.index', compact('payments'));
    }

    public function codReport(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = DB::table('payments')
            ->leftJoin('shipments', 'payments.shipment_id', '=', 'shipments.id')
            ->leftJoin('merchants', 'payments.merchant_id', '=', 'merchants.id')
            ->select(
                'payments.merchant_id',
                'merchants.business_name as merchant_name',
                DB::raw('COUNT(*) as total_collections'),
                DB::raw('SUM(payments.amount) as total_amount'),
                DB::raw('SUM(CASE WHEN payments.status = "completed" THEN payments.amount ELSE 0 END) as completed_amount'),
                DB::raw('SUM(CASE WHEN payments.status = "pending" THEN payments.amount ELSE 0 END) as pending_amount')
            )
            ->where('payments.type', 'cod_collection')
            ->groupBy('payments.merchant_id', 'merchants.business_name');

        if ($request->filled('date_from')) {
            $query->where('payments.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('payments.created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $codReport = $query->orderByDesc('total_amount')->get();

        $totals = [
            'total_collections' => $codReport->sum('total_collections'),
            'total_amount'      => $codReport->sum('total_amount'),
            'completed_amount'  => $codReport->sum('completed_amount'),
            'pending_amount'    => $codReport->sum('pending_amount'),
        ];

        return view('payments.cod-report', compact('codReport', 'totals'));
    }

    public function remittance(Request $request)
    {
        $this->authorize('processRemittance', Payment::class);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'merchant_id'       => 'required|exists:merchants,id',
                'amount'            => 'required|numeric|min:0.01',
                'payment_method'    => 'required|in:bank_transfer,upi,cheque',
                'transaction_ref'   => 'nullable|string|max:100',
                'notes'             => 'nullable|string|max:500',
            ]);

            $pendingPayments = Payment::where('merchant_id', $validated['merchant_id'])
                ->where('type', 'cod_collection')
                ->where('status', 'completed')
                ->whereNull('remittance_id')
                ->get();

            $remittanceId = DB::table('remittances')->insertGetId([
                'merchant_id'      => $validated['merchant_id'],
                'amount'           => $validated['amount'],
                'payment_method'   => $validated['payment_method'],
                'transaction_ref'  => $validated['transaction_ref'] ?? null,
                'notes'            => $validated['notes'] ?? null,
                'status'           => 'completed',
                'processed_by'     => auth()->id(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            foreach ($pendingPayments as $payment) {
                $payment->update(['remittance_id' => $remittanceId]);
            }

            return redirect()
                ->route('payments.index')
                ->with('success', "Remittance of {$validated['amount']} processed successfully.");
        }

        $pendingByMerchant = DB::table('payments')
            ->leftJoin('merchants', 'payments.merchant_id', '=', 'merchants.id')
            ->select(
                'payments.merchant_id',
                'merchants.business_name as merchant_name',
                DB::raw('SUM(payments.amount) as pending_amount'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->where('payments.type', 'cod_collection')
            ->where('payments.status', 'completed')
            ->whereNull('payments.remittance_id')
            ->groupBy('payments.merchant_id', 'merchants.business_name')
            ->get();

        return view('payments.remittance', compact('pendingByMerchant'));
    }
}
