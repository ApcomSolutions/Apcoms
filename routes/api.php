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
    Route::get('/', [CategoryController::class, 'showAllCategory']);          // Semua categories (JSON)
    Route::get('/{id}', [CategoryController::class, 'showCategoryById']);     // Get category by ID
    Route::post('/', [CategoryController::class, 'store']);                   // Tambah category
    Route::put('/{id}', [CategoryController::class, 'update']);               // Update category
    Route::delete('/{id}', [CategoryController::class, 'destroy']);           // Hapus category
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

// Team routes
Route::get('/teams', [App\Http\Controllers\Api\TeamController::class, 'index']);
Route::get('/teams/active', [App\Http\Controllers\Api\TeamController::class, 'active']);
Route::get('/teams/{id}', [App\Http\Controllers\Api\TeamController::class, 'show']);
Route::post('/teams', [App\Http\Controllers\Api\TeamController::class, 'store']);
Route::put('/teams/{id}', [App\Http\Controllers\Api\TeamController::class, 'update']);
Route::delete('/teams/{id}', [App\Http\Controllers\Api\TeamController::class, 'destroy']);
Route::post('/teams/update-order', [App\Http\Controllers\Api\TeamController::class, 'updateOrder']);

// Client routes
Route::get('/clients', [App\Http\Controllers\Api\ClientController::class, 'index']);
Route::get('/clients/active', [App\Http\Controllers\Api\ClientController::class, 'active']);
Route::get('/clients/{id}', [App\Http\Controllers\Api\ClientController::class, 'show']);
Route::post('/clients', [App\Http\Controllers\Api\ClientController::class, 'store']);
Route::put('/clients/{id}', [App\Http\Controllers\Api\ClientController::class, 'update']);
Route::delete('/clients/{id}', [App\Http\Controllers\Api\ClientController::class, 'destroy']);
Route::post('/clients/update-order', [App\Http\Controllers\Api\ClientController::class, 'updateOrder']);

// Gallery routes
Route::get('/gallery', [App\Http\Controllers\Api\GalleryController::class, 'index']);
Route::get('/gallery/active', [App\Http\Controllers\Api\GalleryController::class, 'active']);
Route::get('/gallery/carousel', [App\Http\Controllers\Api\GalleryController::class, 'carousel']);
Route::get('/gallery/{id}', [App\Http\Controllers\Api\GalleryController::class, 'show']);
Route::post('/gallery', [App\Http\Controllers\Api\GalleryController::class, 'store']);
Route::put('/gallery/{id}', [App\Http\Controllers\Api\GalleryController::class, 'update']);
Route::delete('/gallery/{id}', [App\Http\Controllers\Api\GalleryController::class, 'destroy']);
Route::post('/gallery/update-order', [App\Http\Controllers\Api\GalleryController::class, 'updateOrder']);
