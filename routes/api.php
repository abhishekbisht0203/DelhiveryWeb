<?php

use App\Http\Controllers\Api\ShipmentApiController;
use App\Http\Controllers\TrackingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    | Authenticated User
    */
    Route::get('/user', fn (Request $request) => $request->user());

    /*
    | Shipments
    */
    Route::apiResource('shipments', ShipmentApiController::class)->except(['edit', 'create']);
    Route::patch('shipments/{shipment}/status', [ShipmentApiController::class, 'updateStatus']);
    Route::get('shipments/{shipment}/events', [ShipmentApiController::class, 'events']);

    /*
    | Tracking (Authenticated)
    */
    Route::get('tracking/{awb}', [ShipmentApiController::class, 'tracking']);

    /*
    | Pickups
    */
    Route::apiResource('pickups', \App\Http\Controllers\Api\PickupApiController::class);

    /*
    | Hubs
    */
    Route::apiResource('hubs', \App\Http\Controllers\Api\HubApiController::class);

    /*
    | Delivery Partners
    */
    Route::apiResource('delivery-partners', \App\Http\Controllers\Api\DeliveryPartnerApiController::class);

    /*
    | Merchants
    */
    Route::apiResource('merchants', \App\Http\Controllers\Api\MerchantApiController::class);

    /*
    | NDR
    */
    Route::apiResource('ndr', \App\Http\Controllers\Api\NdrApiController::class);

    /*
    | RTO
    */
    Route::apiResource('rto', \App\Http\Controllers\Api\RtoApiController::class);

    /*
    | Payments
    */
    Route::apiResource('payments', \App\Http\Controllers\Api\PaymentApiController::class)->only(['index', 'show']);
    Route::get('payments/cod-report', [\App\Http\Controllers\Api\PaymentApiController::class, 'codReport']);

    /*
    | Dashboard Stats
    */
    Route::get('dashboard/stats', [\App\Http\Controllers\Api\DashboardApiController::class, 'stats']);
});

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::get('public/tracking/{awb}', [ShipmentApiController::class, 'tracking'])
    ->name('api.tracking.public');
