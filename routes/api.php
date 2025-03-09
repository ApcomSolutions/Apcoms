<?php
use App\Http\Controllers\Api\InsightController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

// 📰 API untuk Insights
Route::prefix('insights')->group(function () {
    Route::get('/', [InsightController::class, 'showAllInsights']);          // Semua insights (JSON)
    Route::get('/search', [InsightController::class, 'search']);             // Pencarian insights
    Route::get('/{slug}', [InsightController::class, 'show']);               // Get insight by ID (JSON)
    Route::post('/', [InsightController::class, 'store']);                   // Tambah insight
    Route::put('/{slug}', [InsightController::class, 'update']);             // Update insight
    Route::delete('/{slug}', [InsightController::class, 'destroy']);         // Hapus insight
});

// 📂 API untuk Categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'showAllCategory']);
    Route::get('/{slug}', [CategoryController::class, 'showCategoryById']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{slug}', [CategoryController::class, 'update']);
    Route::delete('/{slug}', [CategoryController::class, 'destroy']);
});

// 📊 API untuk Admin Dashboard
Route::prefix('admin')->group(function () {
    // Insights list untuk halaman admin
    Route::get('/insights', [InsightController::class, 'showAllInsights']);

    // Dashboard analytics
    Route::prefix('dashboard')->group(function () {
        // Statistik umum
        Route::get('/stats', [TrackingController::class, 'getOverallStats']);         // Statistik keseluruhan
        Route::get('/top-articles', [TrackingController::class, 'getTopArticles']);   // Artikel populer
        Route::get('/recent-views', [TrackingController::class, 'getRecentViews']);   // Kunjungan terbaru
        Route::get('/device-breakdown', [TrackingController::class, 'getDeviceBreakdown']); // Statistik perangkat

        // Statistik insight tertentu
        Route::get('/insight-stats/{insightId}', [TrackingController::class, 'getInsightStats']);

        // Data grafik dashboard
        Route::get('/activity-time-series', [TrackingController::class, 'getActivityTimeSeries']);  // Data deret waktu aktivitas
        Route::get('/read-time-distribution', [TrackingController::class, 'getReadTimeDistribution']); // Distribusi waktu baca
        Route::get('/trend-data', [TrackingController::class, 'getTrendData']);      // Data tren perubahan persentase
    });
});

// 📡 API untuk Tracking waktu baca
Route::prefix('tracking')->group(function () {
    Route::post('/read-time', [TrackingController::class, 'trackReadTime']);         // Simpan waktu baca
    Route::get('/stats/{insightId}', [TrackingController::class, 'getStats']);       // Statistik insight tertentu
});
