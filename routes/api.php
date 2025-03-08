<?php
use App\Http\Controllers\Api\InsightController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('insights')->group(function () {
    Route::get('/', [InsightController::class, 'showAllInsights']); // Get semua insights (JSON)
    Route::get('/{slug}', [InsightController::class, 'show']); // Get insight by ID (JSON)
    Route::post('/', [InsightController::class, 'store']); // Create insight (JSON)
    Route::put('/{slug}', [InsightController::class, 'update']); // Update insight (JSON)
    Route::delete('/{slug}', [InsightController::class, 'destroy']); // Delete insight (JSON)
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'showAllCategory']); // Get semua insights (JSON)
    Route::get('/{slug}', [CategoryController::class, 'showCategoryById']); // Get insight by ID (JSON)
    Route::post('/', [CategoryController::class, 'store']); // Create insight (JSON)
    Route::put('/{slug}', [CategoryController::class, 'update']); // Update insight (JSON)
    Route::delete('/{slug}', [CategoryController::class, 'destroy']); // Delete insight (JSON)
});


