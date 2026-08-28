<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryPartnerRequest;
use App\Models\DeliveryPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryPartnerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', DeliveryPartner::class);

        $query = DB::table('delivery_partners')
            ->leftJoin('hubs', 'delivery_partners.hub_id', '=', 'hubs.id')
            ->selectRaw('delivery_partners.*, hubs.name as hub_name')
            ->selectRaw('(SELECT COUNT(*) FROM shipments WHERE shipments.delivery_partner_id = delivery_partners.id) as total_deliveries');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('delivery_partners.name', 'like', "%{$search}%")
                  ->orWhere('delivery_partners.phone', 'like', "%{$search}%")
                  ->orWhere('delivery_partners.vehicle_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hub_id')) {
            $query->where('delivery_partners.hub_id', $request->input('hub_id'));
        }

        if ($request->filled('status')) {
            $query->where('delivery_partners.status', $request->input('status'));
        }

        if ($request->filled('vehicle_type')) {
            $query->where('delivery_partners.vehicle_type', $request->input('vehicle_type'));
        }

        $partners = $query->orderBy('delivery_partners.name')->paginate($request->input('per_page', 20));

        return view('delivery-partners.index', compact('partners'));
    }

    public function create()
    {
        $this->authorize('create', DeliveryPartner::class);

        $hubs = DB::table('hubs')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('delivery-partners.create', compact('hubs'));
    }

    public function store(StoreDeliveryPartnerRequest $request)
    {
        $this->authorize('create', DeliveryPartner::class);

        $partner = DeliveryPartner::create([
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
        ]);

        return redirect()
            ->route('delivery-partners.show', $partner)
            ->with('success', "Delivery partner {$partner->name} created successfully.");
    }

    public function show(DeliveryPartner $partner)
    {
        $this->authorize('view', $partner);

        $partner->load('hub');
        $partner->loadCount(['shipments', 'pickupRequests']);

        $recentDeliveries = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->select(
                'shipments.awb_number',
                'shipments.receiver_name',
                'shipments.receiver_city',
                'shipment_statuses.name as status_name',
                'shipments.actual_delivery_date',
                'shipments.created_at'
            )
            ->where('shipments.delivery_partner_id', $partner->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('delivery-partners.show', compact('partner', 'recentDeliveries'));
    }

    public function edit(DeliveryPartner $partner)
    {
        $this->authorize('update', $partner);

        $hubs = DB::table('hubs')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('delivery-partners.edit', compact('partner', 'hubs'));
    }

    public function update(StoreDeliveryPartnerRequest $request, DeliveryPartner $partner)
    {
        $this->authorize('update', $partner);

        $partner->update($request->validated());

        return redirect()
            ->route('delivery-partners.show', $partner)
            ->with('success', 'Delivery partner updated successfully.');
    }

    public function destroy(DeliveryPartner $partner)
    {
        $this->authorize('delete', $partner);

        $partner->delete();

        return redirect()
            ->route('delivery-partners.index')
            ->with('success', 'Delivery partner deleted successfully.');
    }

    public function dashboard(DeliveryPartner $partner)
    {
        $this->authorize('view', $partner);

        $todayDeliveries = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->select('shipments.awb_number', 'shipments.receiver_name', 'shipments.receiver_address', 'shipment_statuses.slug as status')
            ->where('shipments.delivery_partner_id', $partner->id)
            ->whereDate('shipments.created_at', today())
            ->get();

        $stats = [
            'today_total'     => $todayDeliveries->count(),
            'today_delivered' => $todayDeliveries->where('status', 'delivered')->count(),
            'today_pending'   => $todayDeliveries->whereNotIn('status', ['delivered', 'delivery_failed'])->count(),
            'today_failed'    => $todayDeliveries->where('status', 'delivery_failed')->count(),
        ];

        return view('delivery-partners.dashboard', compact('partner', 'todayDeliveries', 'stats'));
    }
}
