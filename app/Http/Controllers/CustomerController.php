<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('shipments')
            ->select(
                'receiver_name as name',
                'receiver_phone as phone',
                'receiver_email as email',
                'receiver_city as city',
                'receiver_state as state',
                'receiver_pincode as pincode',
                DB::raw('COUNT(*) as shipment_count'),
                DB::raw('MAX(created_at) as last_shipment_at')
            )
            ->groupBy('receiver_name', 'receiver_phone', 'receiver_email', 'receiver_city', 'receiver_state', 'receiver_pincode');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('receiver_name', 'like', "%{$search}%")
                  ->orWhere('receiver_phone', 'like', "%{$search}%")
                  ->orWhere('receiver_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('receiver_city', $request->input('city'));
        }

        if ($request->filled('pincode')) {
            $query->where('receiver_pincode', $request->input('pincode'));
        }

        $customers = $query->orderByDesc('last_shipment_at')->paginate($request->input('per_page', 20));

        return view('customers.index', compact('customers'));
    }

    public function show(Request $request)
    {
        $phone = $request->input('phone');

        if (!$phone) {
            return redirect()->route('customers.index');
        }

        $shipments = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('merchants', 'shipments.merchant_id', '=', 'merchants.id')
            ->select(
                'shipments.awb_number',
                'shipments.order_id',
                'shipments.receiver_name',
                'shipments.receiver_city',
                'shipments.payment_mode',
                'shipments.cod_amount',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'merchants.business_name as merchant_name',
                'shipments.created_at'
            )
            ->where('shipments.receiver_phone', $phone)
            ->latest()
            ->paginate(20);

        $customer = [
            'phone'   => $phone,
            'name'    => $shipments->first()?->receiver_name ?? '',
            'city'    => $shipments->first()?->receiver_city ?? '',
        ];

        return view('customers.show', compact('customer', 'shipments'));
    }
}
