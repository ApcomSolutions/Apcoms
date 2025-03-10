<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\InsightWebController;
use App\Http\Controllers\Web\Admin\InsightAdminController;
use App\Http\Controllers\Web\Admin\CategoryAdminController;
use Illuminate\Support\Facades\Route;

// 🏠 Home route
Route::get('/', [HomeController::class, 'index'])->name('home');

// 📰 Routes untuk halaman website (Insights)
Route::prefix('insights')->name('insights.')->group(function () {
    Route::get('/', [InsightWebController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [InsightWebController::class, 'category'])->name('category');
    Route::get('/search', [InsightWebController::class, 'search'])->name('search');
    Route::get('/{slug}', [InsightWebController::class, 'show'])->name('show');
});

// 📊 Routes untuk halaman admin (dashboard dan insights)
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard utama
    Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('home');

    // Dashboard analytics
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Insights management
    Route::get('/insights', [InsightAdminController::class, 'index'])->name('insights');

    // Categories management
    Route::get('/categories', [CategoryAdminController::class, 'index'])->name('categories');

    // Teams, Clients, Gallery
    Route::get('/teams', [App\Http\Controllers\Web\Admin\TeamAdminController::class, 'index'])->name('teams');
    Route::get('/clients', [App\Http\Controllers\Web\Admin\ClientAdminController::class, 'index'])->name('clients');
    Route::get('/gallery', [App\Http\Controllers\Web\Admin\GalleryAdminController::class, 'index'])->name('gallery');
});




//ini dari fe
Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});


Route::get('/penerbitskt', function () {
    return view('penerbitskt.index');
})->name('penerbitskt');




