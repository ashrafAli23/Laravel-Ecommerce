<?php

use Illuminate\Http\Request;
use Modules\Media\Http\Controllers\FileController;
use Modules\Media\Http\Controllers\FolderController;
use Modules\Media\Http\Controllers\MediaController;

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

Route::middleware('auth:api')->get('/media', function (Request $request) {
    return $request->user();
});

Route::prefix('media')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/action', [MediaController::class, 'action']);
    Route::post('/download', [MediaController::class, 'download']);
    Route::post('/files/uploads', [FileController::class, 'upload']);
    Route::post('/files/uploads-url', [FileController::class, 'uploadUrl']);
    Route::post('/folders/create', [FolderController::class, 'store']);
});