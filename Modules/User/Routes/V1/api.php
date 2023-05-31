<?php

declare(strict_types=1);

namespace Modules\User;

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\RoleController;
use Modules\User\Http\Controllers\UserController;


Route::middleware(['auth:sanctum'])->group(function () {
    /**
     * Users Routes
     */
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'create']);
        Route::delete('/', [UserController::class, 'deletes']);
        Route::put('/{id}/change-password', [UserController::class, 'changePassword']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    /**
     * Roles Routes
     */
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'create']);
        Route::delete('/', [RoleController::class, 'deletes']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::put('/assign-role', [RoleController::class, 'assignRole']);
    });
});
