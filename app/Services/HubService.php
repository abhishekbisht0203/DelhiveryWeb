<?php

namespace App\Services;

use App\Models\Hub;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class HubService
{
    protected ShipmentService $shipmentService;
    protected NotificationService $notificationService;

    public function __construct(ShipmentService $shipmentService, NotificationService $notificationService)
    {
        $this->shipmentService = $shipmentService;
        $this->notificationService = $notificationService;
    }

    public function receiveShipment(Hub $hub, Shipment $shipment): Shipment
    {
        return DB::transaction(function () use ($hub, $shipment) {
            $this->shipmentService->updateStatus(
                $shipment,
                'at_destination_hub',
                "Received at hub: {$hub->name}",
                null
            );

            $shipment->update([
                'destination_hub_id' => $hub->id,
            ]);

            return $shipment->fresh();
        });
    }

    public function dispatchShipment(
        Hub $hub,
        Shipment $shipment,
        ?string $vehicleNumber = null
    ): Shipment {
        return DB::transaction(function () use ($hub, $shipment, $vehicleNumber) {
            $remarks = "Dispatched from hub: {$hub->name}";
            if ($vehicleNumber) {
                $remarks .= " (Vehicle: {$vehicleNumber})";
            }

            $this->shipmentService->updateStatus(
                $shipment,
                'in_transit',
                $remarks,
                null
            );

            return $shipment->fresh();
        });
    }

    public function transferShipment(
        Shipment $shipment,
        Hub $from,
        Hub $to,
        ?string $vehicleNumber = null
    ): Shipment {
        return DB::transaction(function () use ($shipment, $from, $to, $vehicleNumber) {
            $remarks = "Transfer from {$from->name} to {$to->name}";
            if ($vehicleNumber) {
                $remarks .= " (Vehicle: {$vehicleNumber})";
            }

            $this->shipmentService->updateStatus(
                $shipment,
                'in_transit',
                $remarks,
                null
            );

            $shipment->update([
                'origin_hub_id'      => $from->id,
                'destination_hub_id' => $to->id,
            ]);

            return $shipment->fresh();
        });
    }

    public function getHubStats(Hub $hub): array
    {
        $totalReceived = Shipment::where('destination_hub_id', $hub->id)->count();
        $totalDispatched = Shipment::where('origin_hub_id', $hub->id)->count();
        $currentlyAtHub = Shipment::where('destination_hub_id', $hub->id)
            ->where('current_status', 'at_destination_hub')
            ->count();
        $pendingDispatch = Shipment::where('origin_hub_id', $hub->id)
            ->whereIn('current_status', ['created', 'picked_up'])
            ->count();

        return [
            'hub_id'           => $hub->id,
            'hub_name'         => $hub->name,
            'total_received'   => $totalReceived,
            'total_dispatched' => $totalDispatched,
            'currently_at_hub' => $currentlyAtHub,
            'pending_dispatch' => $pendingDispatch,
        ];
    }
}
