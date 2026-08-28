<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RtoRecordResource;
use App\Models\RtoRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RtoApiController extends Controller
{
    public function index(Request $request)
    {
        $query = RtoRecord::with(['shipment', 'ndrRecord']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min($request->input('per_page', 20), 100);
        $records = $query->latest('initiated_at')->paginate($perPage);

        return response()->json([
            'data' => RtoRecordResource::collection($records)->response()->getData(true)['data'],
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page'    => $records->lastPage(),
                'per_page'     => $records->perPage(),
                'total'        => $records->total(),
            ],
        ]);
    }

    public function show(RtoRecord $rto)
    {
        $rto->load(['shipment.status', 'shipment.merchant', 'ndrRecord']);

        return new RtoRecordResource($rto);
    }
}
