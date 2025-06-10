<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\FlightHistoryController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/save-data', [UserDataController::class, 'save']);
Route::post('/save-history', [HistoryController::class, 'save']);
Route::post('/save-flight-history', [FlightHistoryController::class, 'save']);

Route::middleware(['auth:sanctum'])->get('/history-data', [HistoryController::class, 'index']);
Route::middleware(['auth:sanctum'])->get('/flight-history', [FlightHistoryController::class, 'index']);

Route::middleware(['auth:sanctum'])->delete('/flight-history/{id}', [FlightHistoryController::class, 'destroy']);
