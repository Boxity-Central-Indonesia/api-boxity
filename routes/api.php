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
use App\Http\Controllers\Api\EmployeesController;
use App\Http\Controllers\Api\EmployeesCategoryController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\ProductsCategoriesController;
use App\Http\Controllers\Api\ProductsPricesController;
use App\Http\Controllers\Api\ProductsMovementsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VendorContactsController;
use App\Http\Controllers\Api\VendorsController;
use App\Http\Controllers\Api\VendorTransactionsController;
use App\Http\Controllers\Api\WarehousesController;
use App\Http\Controllers\Api\WarehouseLocationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [AuthController::class, 'profile']);
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

    // Products routes
    Route::apiResource('products', ProductsController::class);
    Route::apiResource('products/categories', ProductsCategoriesController::class);
    Route::apiResource('products.{product}/prices', ProductsPricesController::class, ['except' => ['show']]);
    Route::apiResource('products.movements', ProductsMovementsController::class, ['except' => ['show']]);
    Route::get('products/movements/{product}', [ProductsMovementsController::class, 'forProduct']);
    Route::get('products/movements/warehouses/{warehouse}', [ProductsMovementsController::class, 'forWarehouse']);

    // Warehouses routes
    Route::apiResource('warehouses', WarehousesController::class);
    Route::apiResource('warehouses/{warehouse}/locations', WarehouseLocationsController::class, ['except' => ['show']]);

    // Data vendor, customer and suppliers
    Route::apiResource('vendors', VendorsController::class);
    Route::apiResource('vendors.transaction', VendorTransactionsController::class);
    Route::apiResource('vendor.contacts', VendorContactsController::class);

    // Data asset
    Route::apiResource('asset.locations', AssetLocationsController::class);
    Route::apiResource('asset.conditions', AssetConditionsController::class);
    Route::apiResource('assets.depreciation', AssetDepreciationsController::class)->only(['show', 'store', 'update']);
    Route::apiResource('assets', AssetsController::class);

    // Data Keuangan
    Route::apiResource('accounts', AccountsController::class);
    Route::apiResource('accounts.transactions', AccountsTransactionsController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::apiResource('accounts.balances', AccountsBalancesController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
});
Route::post('login', [AuthController::class, 'login']);

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('otp', [AuthController::class, 'otp']);
