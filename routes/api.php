<?php
use App\Http\Controllers\Api\InsightController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

// 📰 API untuk Insights
Route::prefix('insights')->group(function () {
    Route::get('/', [InsightController::class, 'showAllInsights']); // Semua insights (JSON)
    Route::get('/search', [InsightController::class, 'search']); // Pencarian insights
    Route::get('/{slug}', [InsightController::class, 'show']); // Get insight by ID (JSON)
    Route::post('/', [InsightController::class, 'store']); // Tambah insight
    Route::put('/{slug}', [InsightController::class, 'update']); // Update insight
    Route::delete('/{slug}', [InsightController::class, 'destroy']); // Hapus insight
});

// 📂 API untuk Categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'showAllCategory']);
    Route::get('/{slug}', [CategoryController::class, 'showCategoryById']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{slug}', [CategoryController::class, 'update']);
    Route::delete('/{slug}', [CategoryController::class, 'destroy']);
});

// 📊 API untuk Tracking (Dashboard Analytics)
Route::prefix('admin/dashboard')->group(function () {
    Route::get('/stats', [TrackingController::class, 'getOverallStats']); // Statistik keseluruhan
    Route::get('/top-articles', [TrackingController::class, 'getTopArticles']); // Artikel populer
    Route::get('/recent-views', [TrackingController::class, 'getRecentViews']); // Kunjungan terbaru
    Route::get('/device-breakdown', [TrackingController::class, 'getDeviceBreakdown']); // Statistik perangkat
});

// 📡 API untuk Tracking waktu baca
Route::prefix('tracking')->group(function () {
    Route::post('/read-time', [TrackingController::class, 'trackReadTime']); // Simpan waktu baca
    Route::get('/stats/{insightId}', [TrackingController::class, 'getStats']); // Statistik insight tertentu
});
Route::get('/admin/dashboard/insight-stats/{insightId}', [TrackingController::class, 'getInsightStats']);
// Add to your api.php routes file
Route::get('/admin/insights', [InsightController::class, 'showAllInsights']);
