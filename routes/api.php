<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyListController;
use App\Http\Controllers\Api\MasterUsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\TestStatus\Risky;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('otp', 'otp');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/', 'profile');
            Route::post('/', 'update');
        });

        Route::post('logout', 'logout');
    });


    Route::controller(MasterUsersController::class)->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/now', 'getUserNow');
            Route::get('/master', 'showUserMaster');
            Route::post('/', 'create');
            Route::get('/', 'read');
            Route::put('/', 'update');
        });
    });


    Route::controller(CompanyListController::class)->group(function () {
        Route::prefix('company')->group(function () {
            Route::get('/list', 'index');
            Route::post('', 'create');
            Route::put('/', 'edit');
            Route::get('get-by-id', 'getById');
        });
    });
});
