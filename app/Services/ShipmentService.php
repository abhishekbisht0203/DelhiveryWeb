<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentService
{
    public const STATUS_TRANSITIONS = [
        'created'              => ['pickup_scheduled', 'cancelled'],
        'pickup_scheduled'     => ['pickup_assigned', 'pickup_failed', 'cancelled'],
        'pickup_assigned'      => ['picked_up', 'pickup_failed'],
        'picked_up'            => ['at_origin_hub'],
        'at_origin_hub'        => ['in_transit', 'held'],
        'in_transit'           => ['at_destination_hub', 'held'],
        'at_destination_hub'   => ['out_for_delivery'],
        'out_for_delivery'     => ['delivered', 'delivery_failed', 'ndr'],
        'delivery_failed'      => ['ndr', 'rto_initiated'],
        'ndr'                  => ['reattempt', 'rto_initiated', 'delivered', 'cancelled'],
        'reattempt'            => ['delivered', 'delivery_failed', 'ndr', 'rto_initiated'],
        'rto_initiated'        => ['rto_in_transit'],
        'rto_in_transit'       => ['rto_at_hub'],
        'rto_at_hub'           => ['rto_delivered'],
        'cancelled'            => [],
        'delivered'            => [],
        'rto_delivered'        => [],
    ];

    protected AwbService $awbService;
    protected TrackingService $trackingService;

    public function __construct(AwbService $awbService, TrackingService $trackingService)
    {
        $this->awbService = $awbService;
        $this->trackingService = $trackingService;
    }

    public function createShipment(array $data): Shipment
    {
        return DB::transaction(function () use ($data) {
            $awb = $this->awbService->generateAwb();

            $shipment = Shipment::create([
                'awb'              => $awb,
                'order_id'         => $data['order_id'] ?? null,
                'merchant_id'      => $data['merchant_id'],
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'customer_email'   => $data['customer_email'] ?? null,
                'customer_address' => $data['customer_address'],
                'customer_city'    => $data['customer_city'],
                'customer_state'   => $data['customer_state'],
                'customer_pincode' => $data['customer_pincode'],
                'origin_hub_id'    => $data['origin_hub_id'] ?? null,
                'destination_hub_id' => $data['destination_hub_id'] ?? null,
                'current_status'   => 'created',
                'payment_type'     => $data['payment_type'] ?? 'prepaid',
                'cod_amount'       => $data['cod_amount'] ?? 0,
                'declared_value'   => $data['declared_value'] ?? 0,
                'weight'           => $data['weight'] ?? null,
                'length'           => $data['length'] ?? null,
                'width'            => $data['width'] ?? null,
                'height'           => $data['height'] ?? null,
                'volumetric_weight' => $this->calculateVolumetricWeight(
                    $data['length'] ?? 0,
                    $data['width'] ?? 0,
                    $data['height'] ?? 0
                ),
                'pieces'           => $data['pieces'] ?? 1,
                'description'      => $data['description'] ?? null,
                'special_instructions' => $data['special_instructions'] ?? null,
            ]);

            $this->trackingService->addTrackingEvent(
                $shipment,
                'created',
                'Shipment created',
                null,
                null,
                'merchant',
                $data['merchant_id'] ?? null
            );

            return $shipment;
        });
    }

    public function updateStatus(
        Shipment $shipment,
        string $newStatusSlug,
        ?string $remarks = null,
        ?User $actor = null
    ): Shipment {
        $currentStatus = $shipment->current_status;

        if (!$this->isValidTransition($currentStatus, $newStatusSlug)) {
            throw new \InvalidArgumentException(
                "Invalid status transition from '{$currentStatus}' to '{$newStatusSlug}'"
            );
        }

        DB::transaction(function () use ($shipment, $newStatusSlug, $remarks, $actor, $currentStatus) {
            $shipment->update([
                'current_status' => $newStatusSlug,
            ]);

            $this->trackingService->addTrackingEvent(
                $shipment,
                $newStatusSlug,
                $remarks ?? "Status updated to {$newStatusSlug}",
                null,
                null,
                $actor ? 'user' : 'system',
                $actor?->id
            );

            Log::info('Shipment status updated', [
                'shipment_id' => $shipment->id,
                'awb'         => $shipment->awb,
                'old_status'  => $currentStatus,
                'new_status'  => $newStatusSlug,
                'actor_id'    => $actor?->id,
            ]);
        });

        return $shipment->fresh();
    }

    public function getValidTransitions(string $currentStatus): array
    {
        return self::STATUS_TRANSITIONS[$currentStatus] ?? [];
    }

    public function isValidTransition(string $from, string $to): bool
    {
        return in_array($to, $this->getValidTransitions($from));
    }

    public function searchShipments(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Shipment::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('awb', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('current_status', $status);
        }

        if ($merchantId = $request->input('merchant_id')) {
            $query->where('merchant_id', $merchantId);
        }

        if ($paymentType = $request->input('payment_type')) {
            $query->where('payment_type', $paymentType);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        if ($customerCity = $request->input('customer_city')) {
            $query->where('customer_city', $customerCity);
        }

        if ($customerPincode = $request->input('customer_pincode')) {
            $query->where('customer_pincode', $customerPincode);
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->input('per_page', 20);

        return $query->paginate($perPage);
    }

    public function getShipmentStats(
        ?int $merchantId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array {
        $query = Shipment::query();

        if ($merchantId) {
            $query->where('merchant_id', $merchantId);
        }

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        $today = now()->toDateString();
        $todayQuery = (clone $query)->whereDate('created_at', $today);

        return [
            'total_shipments'    => (clone $query)->count(),
            'today_shipments'    => $todayQuery->count(),
            'in_transit'         => (clone $query)->where('current_status', 'in_transit')->count(),
            'out_for_delivery'   => (clone $query)->where('current_status', 'out_for_delivery')->count(),
            'delivered'          => (clone $query)->where('current_status', 'delivered')->count(),
            'pending_pickup'     => (clone $query)->whereIn('current_status', ['created', 'pickup_scheduled', 'pickup_assigned'])->count(),
            'pickup_failed'      => (clone $query)->where('current_status', 'pickup_failed')->count(),
            'ndr_count'          => (clone $query)->where('current_status', 'ndr')->count(),
            'rto_count'          => (clone $query)->where('current_status', 'rto_initiated')->count(),
            'cancelled'          => (clone $query)->where('current_status', 'cancelled')->count(),
        ];
    }

    public function bulkCreateShipments(array $shipmentsData, User $user): array
    {
        $created = [];
        $errors = [];

        foreach ($shipmentsData as $index => $data) {
            try {
                $shipment = $this->createShipment($data);
                $created[] = $shipment;
            } catch (\Exception $e) {
                $errors[] = [
                    'row'     => $index + 1,
                    'message' => $e->getMessage(),
                    'data'    => $data,
                ];
            }
        }

        return [
            'created' => $created,
            'errors'  => $errors,
            'total'   => count($shipmentsData),
            'success' => count($created),
            'failed'  => count($errors),
        ];
    }

    public function calculateVolumetricWeight(float $length, float $width, float $height): float
    {
        if ($length <= 0 || $width <= 0 || $height <= 0) {
            return 0;
        }

        $divisor = config('shipments.volumetric_divisor', 5000);

        return round(($length * $width * $height) / $divisor, 2);
    }
}
