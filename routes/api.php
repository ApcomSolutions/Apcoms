<?php
use App\Http\Controllers\Api\InsightController;
use Illuminate\Support\Facades\Route;

Route::prefix('insights')->group(function () {
    Route::get('/', [InsightController::class, 'showAllInsights']); // Get semua insights (JSON)
    Route::get('/{id}', [InsightController::class, 'show']); // Get insight by ID (JSON)
    Route::post('/', [InsightController::class, 'store']); // Create insight (JSON)
    Route::put('/{id}', [InsightController::class, 'update']); // Update insight (JSON)
    Route::delete('/{id}', [InsightController::class, 'destroy']); // Delete insight (JSON)
});


