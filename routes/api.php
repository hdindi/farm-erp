<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
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

Route::get('/batches/{batch}', [BatchController::class, 'getBatchDetails']);
Route::get('/daily-records/{dailyRecord}/feed-data', [BatchController::class, 'getFeedDataForDailyRecord']);

