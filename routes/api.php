<?php

// File: routes/api.php

use App\Http\Controllers\Api\InsightController;
use App\Http\Controllers\Api\InsightCategoryController;
use App\Http\Controllers\Api\TempUploadController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\GalleryController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsCategoryController;
use App\Http\Controllers\Api\NewsTrackingController;

// ==================== INSIGHTS & INSIGHTS CATEGORIES ====================

// 📰 API untuk Insights
Route::prefix('insights')->group(function () {
    Route::get('/', [InsightController::class, 'showAllInsights']);          // Semua insights (JSON)
    Route::get('/search', [InsightController::class, 'search']);             // Pencarian insights
    Route::get('/{slug}', [InsightController::class, 'show']);               // Get insight by ID (JSON)
    Route::post('/', [InsightController::class, 'store']);                   // Tambah insight
    Route::put('/{slug}', [InsightController::class, 'update']);             // Update insight
    Route::delete('/{slug}', [InsightController::class, 'destroy']);         // Hapus insight
});

// 📂 API untuk Insight Categories (renamed from Categories)
Route::prefix('insight-categories')->group(function () {
    Route::get('/', [InsightCategoryController::class, 'showAllCategory']);          // Semua categories (JSON)
    Route::get('/{id}', [InsightCategoryController::class, 'showCategoryById']);     // Get category by ID
    Route::post('/', [InsightCategoryController::class, 'store']);                   // Tambah category
    Route::put('/{id}', [InsightCategoryController::class, 'update']);               // Update category
    Route::delete('/{id}', [InsightCategoryController::class, 'destroy']);           // Hapus category
});

// Backwards compatibility for existing code
Route::prefix('categories')->group(function () {
    Route::get('/', [InsightCategoryController::class, 'showAllCategory']);
    Route::get('/{id}', [InsightCategoryController::class, 'showCategoryById']);
    Route::post('/', [InsightCategoryController::class, 'store']);
    Route::put('/{id}', [InsightCategoryController::class, 'update']);
    Route::delete('/{id}', [InsightCategoryController::class, 'destroy']);
});

// ==================== NEWS & NEWS CATEGORIES ====================

// 📰 API untuk News
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'showAllNews']);                    // Semua berita (JSON)
    Route::get('/paginated', [NewsController::class, 'getPaginatedNews']);      // Berita dengan pagination
    Route::get('/search', [NewsController::class, 'search']);                   // Pencarian berita
    Route::get('/featured', [NewsController::class, 'getFeaturedNews']);        // Berita unggulan
    Route::get('/archive/periods', [NewsController::class, 'getArchivePeriods']); // Periode arsip
    Route::get('/archive/{year}/{month?}', [NewsController::class, 'getNewsArchive']); // Arsip berita
    Route::get('/id/{id}', [NewsController::class, 'showById']);                // Get berita by ID
    Route::get('/{id}/related', [NewsController::class, 'getRelatedNews']);     // Berita terkait
    Route::get('/{slug}', [NewsController::class, 'show']);                     // Get berita by slug
    Route::post('/', [NewsController::class, 'store']);                         // Tambah berita
    Route::put('/{slug}', [NewsController::class, 'update']);                   // Update berita
    Route::delete('/{slug}', [NewsController::class, 'destroy']);               // Hapus berita

    // API untuk berita berdasarkan kategori (moved inside news prefix)
    Route::get('/category/{categoryId}', [NewsController::class, 'getNewsByCategory']); // Berita berdasarkan ID kategori
    Route::get('/category-slug/{categorySlug}', [NewsController::class, 'getPaginatedNewsByCategory']); // Berita berdasarkan slug kategori

    // API utilitas (moved inside news prefix)
    Route::post('/excerpt', [NewsController::class, 'generateExcerpt']); // Generate excerpt dari konten
});

// 📂 API untuk News Categories
Route::prefix('news-categories')->group(function () {
    Route::get('/', [NewsCategoryController::class, 'showAllCategories']);      // Semua kategori (JSON)
    Route::get('/dropdown', [NewsCategoryController::class, 'getCategoriesForDropdown']); // Format dropdown
    Route::get('/popular', [NewsCategoryController::class, 'getPopularCategories']); // Kategori populer
    Route::get('/with-news', [NewsCategoryController::class, 'getCategoriesWithNews']); // Kategori dengan berita
    Route::get('/search', [NewsCategoryController::class, 'searchCategories']);  // Cari kategori
    Route::get('/{id}', [NewsCategoryController::class, 'showCategoryById']);    // Get kategori by ID
    Route::get('/slug/{slug}', [NewsCategoryController::class, 'showCategoryBySlug']); // Get kategori by slug
    Route::get('/slug/{slug}/news', [NewsCategoryController::class, 'getCategoryWithNews']); // Kategori dengan beritanya
    Route::post('/', [NewsCategoryController::class, 'store']);                  // Tambah kategori
    Route::put('/{id}', [NewsCategoryController::class, 'update']);              // Update kategori
    Route::delete('/{id}', [NewsCategoryController::class, 'destroy']);          // Hapus kategori
    Route::post('/order', [NewsCategoryController::class, 'updateCategoriesOrder']); // Update urutan kategori
});

// ==================== ADMIN DASHBOARDS ====================

// 📊 API untuk Admin Dashboard
Route::prefix('admin')->group(function () {
    // Insights list untuk halaman admin
    Route::get('/insights', [InsightController::class, 'showAllInsights']);

    // Add the new method to get all news including drafts
    Route::get('/news/all', [NewsController::class, 'getAllNewsWithDrafts']); // Add this line!

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

    // 📊 API untuk News pada Admin Dashboard
    Route::prefix('news')->group(function () {
        // Statistik berita
        Route::get('/stats', [NewsController::class, 'getNewsStatsByPeriod']);      // Statistik berita

        // News tracking dan analytics
        Route::prefix('tracking')->group(function () {
            Route::get('/stats', [NewsTrackingController::class, 'getOverallStats']);      // Statistik keseluruhan
            Route::get('/top', [NewsTrackingController::class, 'getTopNews']);             // Berita populer
            Route::get('/recent', [NewsTrackingController::class, 'getRecentViews']);      // Kunjungan terbaru
            Route::get('/devices', [NewsTrackingController::class, 'getDeviceBreakdown']); // Statistik perangkat
            Route::get('/news/{newsId}', [NewsTrackingController::class, 'getNewsStats']); // Statistik berita tertentu
            Route::get('/activity', [NewsTrackingController::class, 'getActivityTimeSeries']); // Data deret waktu aktivitas
            Route::get('/read-time', [NewsTrackingController::class, 'getReadTimeDistribution']); // Distribusi waktu baca
            Route::get('/trends', [NewsTrackingController::class, 'getTrendData']);        // Data tren
        });
    });
});

// ==================== TRACKING ====================

// 📡 API untuk Tracking waktu baca (insights)
Route::prefix('tracking')->group(function () {
    Route::post('/read-time', [TrackingController::class, 'trackReadTime']);         // Simpan waktu baca
    Route::get('/stats/{insightId}', [TrackingController::class, 'getStats']);       // Statistik insight tertentu
});

// 📡 API untuk News Tracking
Route::prefix('news-tracking')->group(function () {
    Route::post('/track-read-time', [NewsTrackingController::class, 'trackReadTime']); // Simpan waktu baca
});

// ==================== OTHER MODULES ====================

// Team routes
Route::prefix('teams')->group(function () {
    Route::get('/', [TeamController::class, 'index']);
    Route::get('/active', [TeamController::class, 'active']);
    Route::get('/{id}', [TeamController::class, 'show']);
    Route::post('/', [TeamController::class, 'store']);
    Route::put('/{id}', [TeamController::class, 'update']);
    Route::delete('/{id}', [TeamController::class, 'destroy']);
    Route::post('/update-order', [TeamController::class, 'updateOrder']);
});

// Client routes
Route::prefix('clients')->group(function () {
    Route::get('/', [ClientController::class, 'index']);
    Route::get('/active', [ClientController::class, 'active']);
    Route::get('/{id}', [ClientController::class, 'show']);
    Route::post('/', [ClientController::class, 'store']);
    Route::put('/{id}', [ClientController::class, 'update']);
    Route::delete('/{id}', [ClientController::class, 'destroy']);
    Route::post('/update-order', [ClientController::class, 'updateOrder']);
});

// Gallery routes
Route::prefix('gallery')->group(function () {
    Route::get('/', [GalleryController::class, 'index']);
    Route::get('/active', [GalleryController::class, 'active']);
    Route::get('/carousel', [GalleryController::class, 'carousel']);
    Route::get('/{id}', [GalleryController::class, 'show']);
    Route::post('/', [GalleryController::class, 'store']);
    Route::put('/{id}', [GalleryController::class, 'update']);
    Route::delete('/{id}', [GalleryController::class, 'destroy']);
    Route::post('/update-order', [GalleryController::class, 'updateOrder']);
});

// File uploads
Route::post('/temp-uploads', [TempUploadController::class, 'store']);
Route::post('/temp-uploads/cancel', [TempUploadController::class, 'cancel']);
Route::post('/trix-uploads', [TempUploadController::class, 'storeTrixAttachment']);


// 🔐 API untuk Autentikasi
Route::prefix('auth')->group(function () {
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/forgot-password', [App\Http\Controllers\Api\AuthController::class, 'requestOTP']);
    Route::post('/verify-otp', [App\Http\Controllers\Api\AuthController::class, 'verifyOTP']);
    Route::post('/reset-password', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
    Route::post('/resend-otp', [App\Http\Controllers\Api\AuthController::class, 'resendOTP']);

    // Routes yang memerlukan autentikasi
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
    });
});




//// File: routes/api.php
//
//use App\Http\Controllers\Api\InsightController;
//use App\Http\Controllers\Api\InsightCategoryController; // Renamed from CategoryController
//use App\Http\Controllers\Api\TempUploadController;
//use App\Http\Controllers\Api\TrackingController;
//use App\Http\Controllers\Api\TeamController;
//use App\Http\Controllers\Api\ClientController;
//use App\Http\Controllers\Api\GalleryController;
//use Illuminate\Support\Facades\Route;
//
//use App\Http\Controllers\Api\NewsController;
//use App\Http\Controllers\Api\NewsCategoryController;
//use App\Http\Controllers\Api\NewsTrackingController;
//
//// ==================== INSIGHTS & INSIGHTS CATEGORIES ====================
//
//// 📰 API untuk Insights
//Route::prefix('insights')->group(function () {
//    Route::get('/', [InsightController::class, 'showAllInsights']);          // Semua insights (JSON)
//    Route::get('/search', [InsightController::class, 'search']);             // Pencarian insights
//    Route::get('/{slug}', [InsightController::class, 'show']);               // Get insight by ID (JSON)
//    Route::post('/', [InsightController::class, 'store']);                   // Tambah insight
//    Route::put('/{slug}', [InsightController::class, 'update']);             // Update insight
//    Route::delete('/{slug}', [InsightController::class, 'destroy']);         // Hapus insight
//});
//
//// 📂 API untuk Insight Categories (renamed from Categories)
//Route::prefix('insight-categories')->group(function () {
//    Route::get('/', [InsightCategoryController::class, 'showAllCategory']);          // Semua categories (JSON)
//    Route::get('/{id}', [InsightCategoryController::class, 'showCategoryById']);     // Get category by ID
//    Route::post('/', [InsightCategoryController::class, 'store']);                   // Tambah category
//    Route::put('/{id}', [InsightCategoryController::class, 'update']);               // Update category
//    Route::delete('/{id}', [InsightCategoryController::class, 'destroy']);           // Hapus category
//});
//
//// Backwards compatibility for existing code
//Route::prefix('categories')->group(function () {
//    Route::get('/', [InsightCategoryController::class, 'showAllCategory']);
//    Route::get('/{id}', [InsightCategoryController::class, 'showCategoryById']);
//    Route::post('/', [InsightCategoryController::class, 'store']);
//    Route::put('/{id}', [InsightCategoryController::class, 'update']);
//    Route::delete('/{id}', [InsightCategoryController::class, 'destroy']);
//});
//
//// ==================== NEWS & NEWS CATEGORIES ====================
//
//// 📰 API untuk News
//Route::prefix('news')->group(function () {
//    Route::get('/', [NewsController::class, 'showAllNews']);                    // Semua berita (JSON)
//    Route::get('/paginated', [NewsController::class, 'getPaginatedNews']);      // Berita dengan pagination
//    Route::get('/search', [NewsController::class, 'search']);                   // Pencarian berita
//    Route::get('/featured', [NewsController::class, 'getFeaturedNews']);        // Berita unggulan
//    Route::get('/archive/periods', [NewsController::class, 'getArchivePeriods']); // Periode arsip
//    Route::get('/archive/{year}/{month?}', [NewsController::class, 'getNewsArchive']); // Arsip berita
//    Route::get('/id/{id}', [NewsController::class, 'showById']);                // Get berita by ID
//    Route::get('/{id}/related', [NewsController::class, 'getRelatedNews']);     // Berita terkait
//    Route::get('/{slug}', [NewsController::class, 'show']);                     // Get berita by slug
//    Route::post('/', [NewsController::class, 'store']);                         // Tambah berita
//    Route::put('/{slug}', [NewsController::class, 'update']);                   // Update berita
//    Route::delete('/{slug}', [NewsController::class, 'destroy']);               // Hapus berita
//
//    // API untuk berita berdasarkan kategori (moved inside news prefix)
//    Route::get('/category/{categoryId}', [NewsController::class, 'getNewsByCategory']); // Berita berdasarkan ID kategori
//    Route::get('/category-slug/{categorySlug}', [NewsController::class, 'getPaginatedNewsByCategory']); // Berita berdasarkan slug kategori
//
//    // API utilitas (moved inside news prefix)
//    Route::post('/excerpt', [NewsController::class, 'generateExcerpt']); // Generate excerpt dari konten
//});
//
//// 📂 API untuk News Categories
//Route::prefix('news-categories')->group(function () {
//    Route::get('/', [NewsCategoryController::class, 'showAllCategories']);      // Semua kategori (JSON)
//    Route::get('/dropdown', [NewsCategoryController::class, 'getCategoriesForDropdown']); // Format dropdown
//    Route::get('/popular', [NewsCategoryController::class, 'getPopularCategories']); // Kategori populer
//    Route::get('/with-news', [NewsCategoryController::class, 'getCategoriesWithNews']); // Kategori dengan berita
//    Route::get('/search', [NewsCategoryController::class, 'searchCategories']);  // Cari kategori
//    Route::get('/{id}', [NewsCategoryController::class, 'showCategoryById']);    // Get kategori by ID
//    Route::get('/slug/{slug}', [NewsCategoryController::class, 'showCategoryBySlug']); // Get kategori by slug
//    Route::get('/slug/{slug}/news', [NewsCategoryController::class, 'getCategoryWithNews']); // Kategori dengan beritanya
//    Route::post('/', [NewsCategoryController::class, 'store']);                  // Tambah kategori
//    Route::put('/{id}', [NewsCategoryController::class, 'update']);              // Update kategori
//    Route::delete('/{id}', [NewsCategoryController::class, 'destroy']);          // Hapus kategori
//    Route::post('/order', [NewsCategoryController::class, 'updateCategoriesOrder']); // Update urutan kategori
//});
//
//// ==================== ADMIN DASHBOARDS ====================
//
//// 📊 API untuk Admin Dashboard
//Route::prefix('admin')->group(function () {
//    // Insights list untuk halaman admin
//    Route::get('/insights', [InsightController::class, 'showAllInsights']);
//
//    // Dashboard analytics
//    Route::prefix('dashboard')->group(function () {
//        // Statistik umum
//        Route::get('/stats', [TrackingController::class, 'getOverallStats']);         // Statistik keseluruhan
//        Route::get('/top-articles', [TrackingController::class, 'getTopArticles']);   // Artikel populer
//        Route::get('/recent-views', [TrackingController::class, 'getRecentViews']);   // Kunjungan terbaru
//        Route::get('/device-breakdown', [TrackingController::class, 'getDeviceBreakdown']); // Statistik perangkat
//
//        // Statistik insight tertentu
//        Route::get('/insight-stats/{insightId}', [TrackingController::class, 'getInsightStats']);
//
//        // Data grafik dashboard
//        Route::get('/activity-time-series', [TrackingController::class, 'getActivityTimeSeries']);  // Data deret waktu aktivitas
//        Route::get('/read-time-distribution', [TrackingController::class, 'getReadTimeDistribution']); // Distribusi waktu baca
//        Route::get('/trend-data', [TrackingController::class, 'getTrendData']);      // Data tren perubahan persentase
//    });
//
//    // 📊 API untuk News pada Admin Dashboard
//    Route::prefix('news')->group(function () {
//        // Statistik berita
//        Route::get('/stats', [NewsController::class, 'getNewsStatsByPeriod']);      // Statistik berita
//
//        // News tracking dan analytics
//        Route::prefix('tracking')->group(function () {
//            Route::get('/stats', [NewsTrackingController::class, 'getOverallStats']);      // Statistik keseluruhan
//            Route::get('/top', [NewsTrackingController::class, 'getTopNews']);             // Berita populer
//            Route::get('/recent', [NewsTrackingController::class, 'getRecentViews']);      // Kunjungan terbaru
//            Route::get('/devices', [NewsTrackingController::class, 'getDeviceBreakdown']); // Statistik perangkat
//            Route::get('/news/{newsId}', [NewsTrackingController::class, 'getNewsStats']); // Statistik berita tertentu
//            Route::get('/activity', [NewsTrackingController::class, 'getActivityTimeSeries']); // Data deret waktu aktivitas
//            Route::get('/read-time', [NewsTrackingController::class, 'getReadTimeDistribution']); // Distribusi waktu baca
//            Route::get('/trends', [NewsTrackingController::class, 'getTrendData']);        // Data tren
//        });
//    });
//});
//
//// ==================== TRACKING ====================
//
//// 📡 API untuk Tracking waktu baca (insights)
//Route::prefix('tracking')->group(function () {
//    Route::post('/read-time', [TrackingController::class, 'trackReadTime']);         // Simpan waktu baca
//    Route::get('/stats/{insightId}', [TrackingController::class, 'getStats']);       // Statistik insight tertentu
//});
//
//// 📡 API untuk News Tracking
//Route::prefix('news-tracking')->group(function () {
//    Route::post('/track-read-time', [NewsTrackingController::class, 'trackReadTime']); // Simpan waktu baca
//});
//
//// ==================== OTHER MODULES ====================
//
//// Team routes
//Route::prefix('teams')->group(function () {
//    Route::get('/', [TeamController::class, 'index']);
//    Route::get('/active', [TeamController::class, 'active']);
//    Route::get('/{id}', [TeamController::class, 'show']);
//    Route::post('/', [TeamController::class, 'store']);
//    Route::put('/{id}', [TeamController::class, 'update']);
//    Route::delete('/{id}', [TeamController::class, 'destroy']);
//    Route::post('/update-order', [TeamController::class, 'updateOrder']);
//});
//
//// Client routes
//Route::prefix('clients')->group(function () {
//    Route::get('/', [ClientController::class, 'index']);
//    Route::get('/active', [ClientController::class, 'active']);
//    Route::get('/{id}', [ClientController::class, 'show']);
//    Route::post('/', [ClientController::class, 'store']);
//    Route::put('/{id}', [ClientController::class, 'update']);
//    Route::delete('/{id}', [ClientController::class, 'destroy']);
//    Route::post('/update-order', [ClientController::class, 'updateOrder']);
//});
//
//// Gallery routes
//Route::prefix('gallery')->group(function () {
//    Route::get('/', [GalleryController::class, 'index']);
//    Route::get('/active', [GalleryController::class, 'active']);
//    Route::get('/carousel', [GalleryController::class, 'carousel']);
//    Route::get('/{id}', [GalleryController::class, 'show']);
//    Route::post('/', [GalleryController::class, 'store']);
//    Route::put('/{id}', [GalleryController::class, 'update']);
//    Route::delete('/{id}', [GalleryController::class, 'destroy']);
//    Route::post('/update-order', [GalleryController::class, 'updateOrder']);
//});
//
//// File uploads
//Route::post('/temp-uploads', [TempUploadController::class, 'store']);
//Route::post('/temp-uploads/cancel', [TempUploadController::class, 'cancel']);
//
//
//// 🔐 API untuk Autentikasi
//Route::prefix('auth')->group(function () {
//    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
//    Route::post('/forgot-password', [App\Http\Controllers\Api\AuthController::class, 'requestOTP']);
//    Route::post('/verify-otp', [App\Http\Controllers\Api\AuthController::class, 'verifyOTP']);
//    Route::post('/reset-password', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
//    Route::post('/resend-otp', [App\Http\Controllers\Api\AuthController::class, 'resendOTP']);
//
//    // Routes yang memerlukan autentikasi
//    Route::middleware('auth:sanctum')->group(function () {
//        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
//        Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
//    });
//});



