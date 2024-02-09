<?php

use App\Http\Controllers\Api\AccountsBalancesController;
use App\Http\Controllers\Api\AccountsController;
use App\Http\Controllers\Api\AccountsTransactionsController;
use App\Http\Controllers\Api\AssetConditionsController;
use App\Http\Controllers\Api\AssetDepreciationsController;
use App\Http\Controllers\Api\AssetLocationsController;
use App\Http\Controllers\Api\AssetsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\MasterUsersController;
use App\Http\Controllers\Api\CompaniesController;
use App\Http\Controllers\Api\CompaniesDepartmentController;
use App\Http\Controllers\Api\CompaniesBranchController;
use App\Http\Controllers\Api\EmployeeCategoryController;
use App\Http\Controllers\Api\EmployeesController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PackageProductController;
use App\Http\Controllers\Api\PackagingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProcessingActivityController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\ProductsCategoriesController;
use App\Http\Controllers\Api\ProductsPricesController;
use App\Http\Controllers\Api\ProductsMovementsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\VendorContactsController;
use App\Http\Controllers\Api\VendorsController;
use App\Http\Controllers\Api\VendorTransactionsController;
use App\Http\Controllers\Api\WarehousesController;
use App\Http\Controllers\Api\WarehouseLocationsController;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::post('/', [AuthController::class, 'update']);
    });
    Route::get('/profiles', [ProfileController::class, 'index']);
    Route::post('/profiles', [ProfileController::class, 'store']);
    Route::get('/profiles', [ProfileController::class, 'show']);
    Route::put('/profiles/{id}', [ProfileController::class, 'update']);
    Route::delete('/profiles', [ProfileController::class, 'destroy']);
    Route::apiResource('businesses', BusinessController::class);

    // Users routes
    Route::apiResource('users', MasterUsersController::class);
    Route::get('users/me', [MasterUsersController::class, 'me']);

    // Companies routes
    Route::apiResource('companies', CompaniesController::class);
    Route::apiResource('companies/{company}/departments', CompaniesDepartmentController::class)->parameters([
        'departments' => 'department'
    ]);
    Route::apiResource('companies/{company}/branches', CompaniesBranchController::class)->parameters([
        'branches' => 'branch'
    ]);


    // Employees routes
    Route::apiResource('employees', EmployeesController::class);
    Route::resource('employee-categories', EmployeeCategoryController::class);

    // Products routes
    Route::apiResource('products', ProductsController::class);
    Route::apiResource('product-categories', ProductsCategoriesController::class);
    Route::apiResource('product-prices', ProductsPricesController::class);
    Route::apiResource('product-movements', ProductsMovementsController::class);
    Route::get('/products/{productId}/processing-activities', [ProductsController::class, 'processingActivities'])->name('products.processingActivities');


    // Warehouses routes
    Route::apiResource('warehouses', WarehousesController::class);
    Route::apiResource('warehouse-locations', WarehouseLocationsController::class);

    // Data vendor, customer and suppliers
    Route::apiResource('vendors', VendorsController::class);
    Route::apiResource('vendor-contacts', VendorContactsController::class);
    Route::apiResource('vendor-transactions', VendorTransactionsController::class);

    // Data asset
    Route::apiResource('asset-locations', AssetLocationsController::class);
    Route::apiResource('asset-conditions', AssetConditionsController::class);
    Route::apiResource('asset-depreciations', AssetDepreciationsController::class);
    Route::apiResource('assets', AssetsController::class);

    // Data Keuangan
    Route::apiResource('accounts', AccountsController::class);
    Route::apiResource('accounts-transactions', AccountsTransactionsController::class);
    Route::apiResource('accounts-balances', AccountsBalancesController::class);
    Route::get('/accounting-data', [AccountsController::class, 'getAccountingData']);

    // Data order, invoices, dan payment
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('/order-details', [OrderController::class, 'getOrderDetails']);
    Route::get('/order-details/{orderID}', [OrderController::class, 'getOrderDetail']);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('orders', OrderController::class);
    Route::get('/orders/{orderId}/processing-activities', [OrderController::class, 'processingActivities'])->name('orders.processingActivities');


    // Leads
    Route::resource('leads', LeadController::class);

    // Reports
    Route::get('/sales-report', [ReportController::class, 'salesReport']);
    Route::get('/purchase-report', [ReportController::class, 'purchaseReport']);
    Route::get('/revenue-report', [ReportController::class, 'revenueReport']);
    Route::get('/expenses-report', [ReportController::class, 'expensesReport']);
    Route::get('/inventory-report', [ReportController::class, 'inventoryReport']);
    Route::get('/leads-report', [ReportController::class, 'leadsReport']);
    Route::get('/vendor-report', [ReportController::class, 'vendorReport']);
    Route::get('/balance-sheet-report', [ReportController::class, 'BalanceSheetReport']);
    Route::get('/payables-report', [ReportController::class, 'PayablesReport']);
    Route::get('/receivables-report', [ReportController::class, 'ReceivablesReport']);
    Route::get('/cashflow-report', [ReportController::class, 'CashFlowReport']);
    Route::get('/ledger-report', [ReportController::class, 'LedgerReport']);
    Route::get('/cash-ledger-report', [ReportController::class, 'generateCashLedgerReport']);
    Route::get('/production-report', [ReportController::class, 'productionReport']);
    Route::get('/production-report/{order_id}', [ReportController::class, 'productionReportDetails']);

    // Proses produksi
    Route::apiResource('packaging', PackagingController::class);
    Route::apiResource('packages', PackageController::class);
    Route::get('/api/packages/{id}/with-products', [PackageController::class, 'withProducts']);
    Route::apiResource('packages-product', PackageProductController::class);
    Route::apiResource('processing-activities', ProcessingActivityController::class);
    Route::get('processing-activities/by-order/{order_id}', [ProcessingActivityController::class, 'getActivitiesByOrder']);
    Route::get('processing-activities/by-product/{product_id}', [ProcessingActivityController::class, 'getActivitiesByProduct']);
});
Route::post('login', [AuthController::class, 'login']);

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('otp', [AuthController::class, 'otp']);
