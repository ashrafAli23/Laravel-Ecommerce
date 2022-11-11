<?php

declare(strict_types=1);


use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\Auth\UserAuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\User\AccountController;
use App\Http\Controllers\Api\V1\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::prefix('auth')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::get('/logout', [UserAuthController::class, 'logout'])->middleware(['auth:sanctum', 'abilities:user-access']);
    Route::post('/forgot-password', [ResetPasswordController::class, 'reset_password_request']);
    Route::post('/verify-token', [ResetPasswordController::class, 'verify_token']);
    Route::post('/reset-password', [ResetPasswordController::class, 'submit_new_password']);
});


Route::prefix('products')->group(function () {

    /**
     * show all products
     */
    Route::get('/', [ProductController::class, 'index']);

    /**
     * show product
     */
    Route::get('/{slug}', [ProductController::class, 'show']);
});

Route::prefix('cart')->group(function () {
    /**
     * add to cart
     */
    Route::post('/', [CartController::class, 'addToCart']);
    Route::get('/', [CartController::class, 'showCartDetails']);
    Route::put('/update', [CartController::class, 'updateCart']);
    Route::delete('/delete', [CartController::class, 'destroyCart']);
});

Route::post('/apply-coupon', CouponController::class);

Route::middleware(['auth:sanctum', 'abilities:user-access'])->group(function () {

    //------------ DASHBOARD ------------
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [AccountController::class, 'index']);
        Route::get('/profile', [AccountController::class, 'getProfile']);
        Route::post('/update-profile', [AccountController::class, 'updateProfile']);
        Route::post('/delete-account', [AccountController::class, 'deleteAccount']);
    });

    //------------ ORDER ------------
    Route::prefix('order')->group(function () {
        Route::post('/', [OrderController::class, 'createOrder']);
        Route::put('/{id}', [OrderController::class, 'updateOrderStatus']);
        Route::get('/{id}', [OrderController::class, 'getOrderDetails']);
    });

    //------------ WISHLIST ------------
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/', [WishlistController::class, 'store']);
        Route::delete('/', [WishlistController::class, 'destroyAll']);
        Route::delete('/{id}', [WishlistController::class, 'destroy']);
    });
});


require __DIR__ . '/admin.php';
