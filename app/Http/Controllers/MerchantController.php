<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Merchant::class);

        $query = DB::table('merchants')
            ->selectRaw('merchants.*, (SELECT COUNT(*) FROM shipments WHERE shipments.merchant_id = merchants.id) as total_shipments');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('cod_enabled')) {
            $query->where('cod_enabled', $request->boolean('cod_enabled'));
        }

        $merchants = $query->orderBy('business_name')->paginate($request->input('per_page', 20));

        return view('merchants.index', compact('merchants'));
    }

    public function create()
    {
        $this->authorize('create', Merchant::class);

        return view('merchants.create');
    }

    public function store(StoreMerchantRequest $request)
    {
        $this->authorize('create', Merchant::class);

        $merchant = Merchant::create([
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
        ]);

        return redirect()
            ->route('merchants.show', $merchant)
            ->with('success', "Merchant {$merchant->business_name} created successfully.");
    }

    public function show(Merchant $merchant)
    {
        $this->authorize('view', $merchant);

        $merchant->loadCount(['shipments', 'payments', 'invoices']);

        $recentShipments = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->select(
                'shipments.awb_number',
                'shipments.receiver_name',
                'shipments.receiver_city',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'shipments.created_at'
            )
            ->where('shipments.merchant_id', $merchant->id)
            ->latest()
            ->limit(10)
            ->get();

        $codSummary = DB::table('shipments')
            ->selectRaw('SUM(cod_amount) as total_cod, SUM(collected_amount) as total_collected')
            ->where('merchant_id', $merchant->id)
            ->where('payment_mode', 'cod')
            ->first();

        return view('merchants.show', compact('merchant', 'recentShipments', 'codSummary'));
    }

    public function edit(Merchant $merchant)
    {
        $this->authorize('update', $merchant);

        return view('merchants.edit', compact('merchant'));
    }

    public function update(UpdateMerchantRequest $request, Merchant $merchant)
    {
        $this->authorize('update', $merchant);

        $merchant->update($request->validated());

        return redirect()
            ->route('merchants.show', $merchant)
            ->with('success', 'Merchant updated successfully.');
    }

    public function destroy(Merchant $merchant)
    {
        $this->authorize('delete', $merchant);

        $merchant->delete();

        return redirect()
            ->route('merchants.index')
            ->with('success', 'Merchant deleted successfully.');
    }

    public function shipments(Merchant $merchant)
    {
        $this->authorize('view', $merchant);

        $shipments = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->select(
                'shipments.awb_number',
                'shipments.order_id',
                'shipments.receiver_name',
                'shipments.receiver_city',
                'shipments.payment_mode',
                'shipments.cod_amount',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'shipments.created_at'
            )
            ->where('shipments.merchant_id', $merchant->id)
            ->latest()
            ->paginate(20);

        return view('merchants.shipments', compact('merchant', 'shipments'));
    }

    public function codSummary(Merchant $merchant)
    {
        $this->authorize('view', $merchant);

        $summary = DB::table('shipments')
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total_shipments,
                SUM(CASE WHEN payment_mode = "cod" THEN cod_amount ELSE 0 END) as cod_amount,
                SUM(CASE WHEN payment_mode = "cod" THEN collected_amount ELSE 0 END) as collected_amount
            ')
            ->where('merchant_id', $merchant->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $totals = DB::table('shipments')
            ->selectRaw('
                SUM(CASE WHEN payment_mode = "cod" THEN cod_amount ELSE 0 END) as total_cod,
                SUM(CASE WHEN payment_mode = "cod" THEN collected_amount ELSE 0 END) as total_collected
            ')
            ->where('merchant_id', $merchant->id)
            ->first();

        return view('merchants.cod-summary', compact('merchant', 'summary', 'totals'));
    }
}
