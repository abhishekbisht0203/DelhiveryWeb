<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryPartnerController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\NdrController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RtoController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));

Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking', [TrackingController::class, 'track'])->name('tracking.track');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    | Dashboard
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    | Shipments
    */
    Route::prefix('shipments')->name('shipments.')->group(function () {
        Route::get('/', [ShipmentController::class, 'index'])->name('index');
        Route::get('/create', [ShipmentController::class, 'create'])->name('create');
        Route::post('/', [ShipmentController::class, 'store'])->name('store');
        Route::get('/stats', [ShipmentController::class, 'getStats'])->name('stats');
        Route::get('/bulk-upload', [ShipmentController::class, 'bulkUpload'])->name('bulk-upload');
        Route::post('/bulk-upload', [ShipmentController::class, 'processBulkUpload'])->name('bulk-upload.process');
        Route::get('/export', [ShipmentController::class, 'export'])->name('export');
        Route::get('/track', [ShipmentController::class, 'track'])->name('track');
        Route::post('/track', [ShipmentController::class, 'trackByAwb'])->name('track-by-awb');
        Route::get('/{shipment}', [ShipmentController::class, 'show'])->name('show');
        Route::get('/{shipment}/edit', [ShipmentController::class, 'edit'])->name('edit');
        Route::put('/{shipment}', [ShipmentController::class, 'update'])->name('update');
        Route::delete('/{shipment}', [ShipmentController::class, 'destroy'])->name('destroy');
        Route::patch('/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('update-status');
    });

    /*
    | Pickups
    */
    Route::prefix('pickups')->name('pickups.')->group(function () {
        Route::get('/', [PickupController::class, 'index'])->name('index');
        Route::get('/{pickup}', [PickupController::class, 'show'])->name('show');
        Route::post('/{pickup}/assign', [PickupController::class, 'assign'])->name('assign');
        Route::post('/{pickup}/schedule', [PickupController::class, 'schedule'])->name('schedule');
        Route::post('/{pickup}/picked-up', [PickupController::class, 'markPickedUp'])->name('picked-up');
        Route::post('/{pickup}/failed', [PickupController::class, 'markFailed'])->name('failed');
        Route::post('/shipment/{shipment}/create-pickup', [PickupController::class, 'createForShipment'])->name('create-for-shipment');
    });

    /*
    | Hubs
    */
    Route::prefix('hubs')->name('hubs.')->group(function () {
        Route::get('/', [HubController::class, 'index'])->name('index');
        Route::get('/create', [HubController::class, 'create'])->name('create');
        Route::post('/', [HubController::class, 'store'])->name('store');
        Route::get('/{hub}', [HubController::class, 'show'])->name('show');
        Route::get('/{hub}/edit', [HubController::class, 'edit'])->name('edit');
        Route::put('/{hub}', [HubController::class, 'update'])->name('update');
        Route::delete('/{hub}', [HubController::class, 'destroy'])->name('destroy');
        Route::get('/{hub}/shipments', [HubController::class, 'shipments'])->name('shipments');
        Route::post('/{hub}/shipment/{shipment}/receive', [HubController::class, 'receive'])->name('receive');
        Route::post('/{hub}/dispatch', [HubController::class, 'dispatch'])->name('dispatch');
    });

    /*
    | Warehouses
    */
    Route::resource('warehouses', WarehouseController::class)->except(['show']);

    /*
    | Delivery Partners
    */
    Route::prefix('delivery-partners')->name('delivery-partners.')->group(function () {
        Route::get('/', [DeliveryPartnerController::class, 'index'])->name('index');
        Route::get('/create', [DeliveryPartnerController::class, 'create'])->name('create');
        Route::post('/', [DeliveryPartnerController::class, 'store'])->name('store');
        Route::get('/{partner}', [DeliveryPartnerController::class, 'show'])->name('show');
        Route::get('/{partner}/edit', [DeliveryPartnerController::class, 'edit'])->name('edit');
        Route::put('/{partner}', [DeliveryPartnerController::class, 'update'])->name('update');
        Route::delete('/{partner}', [DeliveryPartnerController::class, 'destroy'])->name('destroy');
        Route::get('/{partner}/dashboard', [DeliveryPartnerController::class, 'dashboard'])->name('dashboard');
    });

    /*
    | Merchants
    */
    Route::prefix('merchants')->name('merchants.')->group(function () {
        Route::get('/', [MerchantController::class, 'index'])->name('index');
        Route::get('/create', [MerchantController::class, 'create'])->name('create');
        Route::post('/', [MerchantController::class, 'store'])->name('store');
        Route::get('/{merchant}', [MerchantController::class, 'show'])->name('show');
        Route::get('/{merchant}/edit', [MerchantController::class, 'edit'])->name('edit');
        Route::put('/{merchant}', [MerchantController::class, 'update'])->name('update');
        Route::delete('/{merchant}', [MerchantController::class, 'destroy'])->name('destroy');
        Route::get('/{merchant}/shipments', [MerchantController::class, 'shipments'])->name('shipments');
        Route::get('/{merchant}/cod-summary', [MerchantController::class, 'codSummary'])->name('cod-summary');
    });

    /*
    | Customers
    */
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/show', [CustomerController::class, 'show'])->name('show');
    });

    /*
    | NDR Management
    */
    Route::prefix('ndr')->name('ndr.')->group(function () {
        Route::get('/', [NdrController::class, 'index'])->name('index');
        Route::get('/{ndr}', [NdrController::class, 'show'])->name('show');
        Route::post('/{ndr}/resolve', [NdrController::class, 'resolve'])->name('resolve');
        Route::post('/{ndr}/reattempt', [NdrController::class, 'reattempt'])->name('reattempt');
        Route::post('/{ndr}/initiate-rto', [NdrController::class, 'initiateRto'])->name('initiate-rto');
    });

    /*
    | RTO Management
    */
    Route::prefix('rto')->name('rto.')->group(function () {
        Route::get('/', [RtoController::class, 'index'])->name('index');
        Route::get('/{rto}', [RtoController::class, 'show'])->name('show');
        Route::patch('/{rto}/status', [RtoController::class, 'updateStatus'])->name('update-status');
    });

    /*
    | Payments & COD
    */
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/cod-report', [PaymentController::class, 'codReport'])->name('cod-report');
        Route::get('/remittance', [PaymentController::class, 'remittance'])->name('remittance');
        Route::post('/remittance', [PaymentController::class, 'remittance'])->name('remittance.process');
    });

    /*
    | Invoices
    */
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
    });

    /*
    | Reports
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/shipments', [ReportController::class, 'shipmentReport'])->name('shipments');
        Route::get('/delivery', [ReportController::class, 'deliveryReport'])->name('delivery');
        Route::get('/cod', [ReportController::class, 'codReport'])->name('cod');
        Route::get('/ndr', [ReportController::class, 'ndrReport'])->name('ndr');
        Route::get('/export', [ReportController::class, 'exportReport'])->name('export');
    });

    /*
    | User Management
    */
    Route::resource('users', UserController::class);

    /*
    | Roles & Permissions
    */
    Route::resource('roles', RoleController::class);

    /*
    | Settings
    */
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
    });
});
