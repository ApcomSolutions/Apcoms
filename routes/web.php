<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\InsightWebController;
use App\Http\Controllers\Web\Admin\InsightAdminController;
use Illuminate\Support\Facades\Route;

// 🏠 Home route
Route::get('/', [HomeController::class, 'index'])->name('home');

// 📰 Routes untuk halaman website (Insigts)
Route::get('/insights', [InsightWebController::class, 'index'])->name('insights.index');
Route::get('/insights/category/{slug}', [InsightWebController::class, 'category'])->name('insights.category');
Route::get('/insights/{slug}', [InsightWebController::class, 'show'])->name('insights.show');

// 📊 Routes untuk halaman admin (dashboard dan insights)
Route::prefix('admin')->group(function () {
    Route::get('/insights', [InsightAdminController::class, 'index'])->name('admin.insights');
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Pastikan file "dashboard.blade.php" ada di "resources/views/admin/"
    })->name('admin.dashboard');
});


Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
