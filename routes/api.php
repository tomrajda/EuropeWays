<?php

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


use App\Http\Controllers\UserDataController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\FlightHistoryController;

Route::post('/save-data', [UserDataController::class, 'save']);
Route::post('/save-history', [HistoryController::class, 'save']);
Route::post('/save-flight-history', [FlightHistoryController::class, 'save']);

Route::middleware(['auth:sanctum'])->get('/history-data', [HistoryController::class, 'index']);
Route::middleware(['auth:sanctum'])->get('/flight-history', [FlightHistoryController::class, 'index']);