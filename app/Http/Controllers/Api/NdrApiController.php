<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NdrRecordResource;
use App\Models\NdrRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NdrApiController extends Controller
{
    public function index(Request $request)
    {
        $query = NdrRecord::with(['shipment', 'deliveryPartner', 'hub']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('reason')) {
            $query->where('reason', 'like', "%{$request->input('reason')}%");
        }

        $perPage = min($request->input('per_page', 20), 100);
        $records = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => NdrRecordResource::collection($records)->response()->getData(true)['data'],
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page'    => $records->lastPage(),
                'per_page'     => $records->perPage(),
                'total'        => $records->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id'         => 'required|exists:shipments,id',
            'delivery_partner_id' => 'nullable|exists:delivery_partners,id',
            'hub_id'              => 'nullable|exists:hubs,id',
            'attempt_number'      => 'required|integer|min:1',
            'reason'              => 'required|string|max:255',
            'remarks'             => 'nullable|string|max:500',
        ]);

        $validated['status'] = 'pending';

        $record = NdrRecord::create($validated);

        return (new NdrRecordResource($record->load(['shipment', 'deliveryPartner', 'hub'])))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(NdrRecord $ndr)
    {
        $ndr->load(['shipment.status', 'deliveryPartner', 'hub']);

        return new NdrRecordResource($ndr);
    }

    public function update(Request $request, NdrRecord $ndr)
    {
        $validated = $request->validate([
            'next_action'       => 'sometimes|in:reattempt,rto,cancelled,delivered',
            'customer_response' => 'nullable|string|max:500',
            'remarks'           => 'nullable|string|max:500',
            'status'            => 'sometimes|in:pending,resolved,reattempt_scheduled,rto_initiated',
            'reattempt_date'    => 'nullable|date',
        ]);

        $ndr->update($validated);

        return new NdrRecordResource($ndr->fresh(['shipment', 'deliveryPartner', 'hub']));
    }

    public function destroy(NdrRecord $ndr)
    {
        $ndr->delete();

        return response()->json(['message' => 'NDR record deleted.'], 200);
    }
}
