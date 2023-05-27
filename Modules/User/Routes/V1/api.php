<?php

declare(strict_types=1);

namespace Modules\User;

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\RoleController;
use Modules\User\Http\Controllers\UserController;




Route::middleware([])->group(function () {
    Route::get('/user', [UserController::class, 'index']);
});


Route::middleware([])->group(function () {

    /**
     * Roles Routes
     */
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'create']);
        Route::delete('/', [RoleController::class, 'deletes']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::put('/assign-role', [RoleController::class, 'assignRole']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
    });
});
