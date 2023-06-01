<?php

use Modules\Products\Http\Controllers\BrandController;

Route::middleware([])->group(function () {
    /**
     * Brands routes
     */
    Route::prefix("brands")->group(function () {
        Route::get('/', [BrandController::class, 'index']);
        Route::post('/', [BrandController::class, 'store']);

        Route::delete('/{id}', [BrandController::class, 'destroy']);
    });
});
