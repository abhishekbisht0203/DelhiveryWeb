<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['shipment', 'merchant']);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->input('merchant_id'));
        }

        $perPage = min($request->input('per_page', 20), 100);
        $payments = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => PaymentResource::collection($payments)->response()->getData(true)['data'],
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
                'per_page'     => $payments->perPage(),
                'total'        => $payments->total(),
            ],
        ]);
    }

    public function show(Payment $payment)
    {
        $payment->load(['shipment', 'merchant', 'processor']);

        return new PaymentResource($payment);
    }

    public function codReport(Request $request)
    {
        $query = DB::table('payments')
            ->leftJoin('merchants', 'payments.merchant_id', '=', 'merchants.id')
            ->select(
                'payments.merchant_id',
                'merchants.business_name as merchant_name',
                DB::raw('COUNT(*) as total_collections'),
                DB::raw('SUM(payments.amount) as total_amount'),
                DB::raw('SUM(CASE WHEN payments.status = "completed" THEN payments.amount ELSE 0 END) as completed_amount')
            )
            ->where('payments.type', 'cod_collection')
            ->groupBy('payments.merchant_id', 'merchants.business_name');

        if ($request->filled('date_from')) {
            $query->where('payments.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('payments.created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $report = $query->orderByDesc('total_amount')->get();

        return response()->json([
            'data' => $report,
            'totals' => [
                'total_collections' => $report->sum('total_collections'),
                'total_amount'      => $report->sum('total_amount'),
                'completed_amount'  => $report->sum('completed_amount'),
            ],
        ]);
    }
}
