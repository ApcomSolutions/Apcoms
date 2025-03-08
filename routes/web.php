<?php

use App\Http\Controllers\Api\InsightController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('api')->middleware('api')->group(function () {
    Route::get('insights', [InsightController::class, 'index']);
    Route::get('insights/{id}', [InsightController::class, 'show']);
    Route::post('insights', [InsightController::class, 'store']);
    Route::put('insights/{id}', [InsightController::class, 'update']);
    Route::patch('insights/{id}', [InsightController::class, 'update']);
    Route::delete('insights/{id}', [InsightController::class, 'destroy']);
});
