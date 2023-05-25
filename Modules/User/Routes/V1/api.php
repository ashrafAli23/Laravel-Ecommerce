<?php

declare(strict_types=1);

namespace Modules\User;

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;


Route::prefix('auth')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::get('/logout', [UserAuthController::class, 'logout'])->middleware(['auth:sanctum']);
    Route::post('/forgot-password', [ResetPasswordController::class, 'reset_password_request']);
    Route::post('/verify-token', [ResetPasswordController::class, 'verify_token']);
    Route::post('/reset-password', [ResetPasswordController::class, 'submit_new_password']);
});

Route::middleware([])->group(function () {
    Route::get('/user', [UserController::class, 'index']);
});