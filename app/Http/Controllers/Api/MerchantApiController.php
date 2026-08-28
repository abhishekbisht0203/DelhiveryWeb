<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MerchantApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Merchant::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min($request->input('per_page', 20), 100);
        $merchants = $query->orderBy('business_name')->paginate($perPage);

        return response()->json([
            'data' => MerchantResource::collection($merchants)->response()->getData(true)['data'],
            'meta' => [
                'current_page' => $merchants->currentPage(),
                'last_page'    => $merchants->lastPage(),
                'per_page'     => $merchants->perPage(),
                'total'        => $merchants->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name'          => 'required|string|max:255',
            'owner_name'             => 'required|string|max:255',
            'phone'                  => 'required|string|max:15',
            'email'                  => 'required|email|unique:merchants,email',
            'gst_number'             => 'nullable|string|max:20',
            'pan_number'             => 'nullable|string|max:20',
            'billing_address'        => 'nullable|string|max:500',
            'billing_city'           => 'nullable|string|max:100',
            'billing_state'          => 'nullable|string|max:100',
            'billing_pincode'        => 'nullable|string|max:10',
            'cod_enabled'            => 'nullable|boolean',
            'cod_fee_percent'        => 'nullable|numeric|min:0|max:100',
            'max_cod_amount'         => 'nullable|numeric|min:0',
            'monthly_shipment_limit' => 'nullable|integer|min:0',
            'pricing_tier'           => 'nullable|string|max:50',
            'status'                 => 'nullable|in:active,inactive,suspended',
        ]);

        $validated['organization_id'] = $request->user()->organization_id;

        $merchant = Merchant::create($validated);

        return (new MerchantResource($merchant))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(Merchant $merchant)
    {
        $merchant->loadCount(['shipments', 'payments']);

        return new MerchantResource($merchant);
    }

    public function update(Request $request, Merchant $merchant)
    {
        $validated = $request->validate([
            'business_name'          => 'sometimes|string|max:255',
            'owner_name'             => 'sometimes|string|max:255',
            'phone'                  => 'sometimes|string|max:15',
            'email'                  => 'sometimes|email|unique:merchants,email,' . $merchant->id,
            'gst_number'             => 'nullable|string|max:20',
            'pan_number'             => 'nullable|string|max:20',
            'cod_enabled'            => 'nullable|boolean',
            'cod_fee_percent'        => 'nullable|numeric|min:0|max:100',
            'max_cod_amount'         => 'nullable|numeric|min:0',
            'status'                 => 'nullable|in:active,inactive,suspended',
        ]);

        $merchant->update($validated);

        return new MerchantResource($merchant->fresh());
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();

        return response()->json(['message' => 'Merchant deleted.'], 200);
    }
}
