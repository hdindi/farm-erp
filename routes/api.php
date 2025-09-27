<?php

use App\Http\Controllers\BatchController;
use Illuminate\Http\Request;
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

// Example route - often uses sanctum for auth
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Add your other API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('batches', BatchController::class)->names([
        'index' => 'api.batches.index',
        'store' => 'api.batches.store',
        'show' => 'api.batches.show',
        'update' => 'api.batches.update',
        'destroy' => 'api.batches.destroy',
    ]);
    Route::get('batches/{batch}/performance', [BatchController::class, 'performance'])->name('api.batches.performance');
    Route::get('/batches/{batch}/details', [BatchController::class, 'getBatchDetails'])->name('api.batches.details');
    Route::get('/daily-records/{dailyRecord}/feed-data', [BatchController::class, 'getFeedDataForDailyRecord'])->name('api.daily-records.feed-data');
});
