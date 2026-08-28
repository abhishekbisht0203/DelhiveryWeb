<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function track(Request $request)
    {
        $request->validate([
            'awb' => 'required|string',
        ]);

        $awb = $request->input('awb');

        $shipment = DB::table('shipments')
            ->join('shipment_statuses', 'shipments.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('hubs as current_hub', 'shipments.current_hub_id', '=', 'current_hub.id')
            ->select(
                'shipments.awb_number',
                'shipments.order_id',
                'shipments.receiver_name',
                'shipments.receiver_city',
                'shipments.receiver_state',
                'shipment_statuses.slug as status',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'current_hub.name as current_hub_name',
                'current_hub.city as current_hub_city',
                'shipments.expected_delivery_date',
                'shipments.pickup_scheduled_at',
                'shipments.actual_delivery_date',
                'shipments.created_at'
            )
            ->where('shipments.awb_number', $awb)
            ->first();

        if (!$shipment) {
            return view('tracking.index')->withErrors(['awb' => 'Shipment not found.']);
        }

        $events = DB::table('shipment_events')
            ->leftJoin('shipment_statuses', 'shipment_events.status_id', '=', 'shipment_statuses.id')
            ->leftJoin('hubs', 'shipment_events.hub_id', '=', 'hubs.id')
            ->select(
                'shipment_events.status_slug',
                'shipment_events.description',
                'shipment_events.location',
                'shipment_events.event_type',
                'shipment_events.created_at',
                'shipment_statuses.name as status_name',
                'shipment_statuses.color as status_color',
                'hubs.name as hub_name'
            )
            ->where('shipment_events.shipment_id', function ($q) use ($awb) {
                $q->select('id')->from('shipments')->where('awb_number', $awb);
            })
            ->orderBy('shipment_events.created_at', 'desc')
            ->get();

        return view('tracking.show', compact('shipment', 'events'));
    }
}
