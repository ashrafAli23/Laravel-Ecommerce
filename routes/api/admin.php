<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\AccountController;
use App\Http\Controllers\Api\V1\Admin\BannerController;
use App\Http\Controllers\Api\V1\Admin\BrandController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\CouponsController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\UnitsController;
use App\Http\Controllers\Api\V1\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    //------------ CATEGORY ------------
    Route::prefix('category')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::put('/status/{id}', [CategoryController::class, 'updateStatus']);
        Route::put('/update-image/{id}', [CategoryController::class, 'updateImage']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

    //------------ CATEGORY ------------
    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
    });

    //------------ USERS ------------
    Route::apiResource('/user', UsersController::class);

    //------------ UNITS ------------
    Route::apiResource('/units', UnitsController::class);

    //------------ COUPONS ------------
    Route::apiResource('/coupons', CouponsController::class);

    //------------ BRAND ------------
    Route::prefix('brand')->group(function () {
        Route::get('/', [BrandController::class, 'index']);
        Route::post('/', [BrandController::class, 'store']);
        Route::get('/{id}', [BrandController::class, 'show']);
        Route::put('/{id}', [BrandController::class, 'update']);
        Route::put('/status/{id}', [BrandController::class, 'updateStatus']);
        Route::put('/update-image/{id}', [BrandController::class, 'updateImage']);
        Route::delete('/{id}', [BrandController::class, 'destroy']);
    });

    //------------ BANNER ------------
    Route::prefix('banner')->group(function () {
        Route::get('/', [BannerController::class, 'index']);
        Route::post('/', [BannerController::class, 'store']);
        Route::get('/{id}', [BannerController::class, 'show']);
        Route::put('/{id}', [BannerController::class, 'update']);
        Route::put('/status/{id}', [BannerController::class, 'updateStatus']);
        Route::put('/update-image/{id}', [BannerController::class, 'updateImage']);
        Route::delete('/{id}', [BannerController::class, 'destroy']);
    });
});