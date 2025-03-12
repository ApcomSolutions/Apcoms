<?php
//// File: routes/web.php
//
//use App\Http\Controllers\HomeController;
//use App\Http\Controllers\PasswordResetController;
//use App\Http\Controllers\Web\Admin\AdminDashboardController;
//use App\Http\Controllers\Web\InsightWebController;
//use App\Http\Controllers\Web\Admin\InsightAdminController;
//use App\Http\Controllers\Web\Admin\CategoryAdminController;
//use App\Models\News;
//use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Web\Admin\NewsAdminCategoryController;
//
//
//// 🏠 Home route
//Route::get('/', [HomeController::class, 'index'])->name('home');
//
//// 📰 Routes untuk halaman website (Insights)
//Route::prefix('insights')->name('insights.')->group(function () {
//    Route::get('/', [InsightWebController::class, 'index'])->name('index');
//    Route::get('/category/{slug}', [InsightWebController::class, 'category'])->name('category');
//    Route::get('/search', [InsightWebController::class, 'search'])->name('search');
//    Route::get('/{slug}', [InsightWebController::class, 'show'])->name('show');
//});
//
//// 📊 Routes untuk halaman admin (dashboard dan insights)
//Route::prefix('admin')->name('admin.')->group(function () {
//    // Dashboard utama
//    Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('home');
//
//    // Dashboard analytics
//    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
//
//    // Insights management
//    Route::get('/insights', [InsightAdminController::class, 'index'])->name('insights');
//
//    // Categories management
//    Route::get('/categories', [CategoryAdminController::class, 'index'])->name('categories');
//
//    // Teams, Clients, Gallery
//    Route::get('/teams', [App\Http\Controllers\Web\Admin\TeamAdminController::class, 'index'])->name('teams');
//    Route::get('/clients', [App\Http\Controllers\Web\Admin\ClientAdminController::class, 'index'])->name('clients');
//    Route::get('/gallery', [App\Http\Controllers\Web\Admin\GalleryAdminController::class, 'index'])->name('gallery');
//});
//
//
//Route::prefix('admin/news')->name('admin.news.')->group(function () {
//    Route::get('/', [App\Http\Controllers\Web\Admin\NewsAdminController::class, 'index'])->name('index');
//    Route::get('/categories', [NewsAdminCategoryController::class, 'index'])->name('categories');
//});
//
//// Rute halaman statis
//Route::get('/about', function () {
//    return view('about');
//});
//
//Route::get('/contact', function () {
//    return view('contact');
//});
//
//// Untuk menampilkan form lupa password
//Route::get('/login', function () {
//    return view('login.index');
//})->name('login');
//
//// Untuk memproses permintaan reset password
//Route::post('/login', [PasswordResetController::class, 'sendOTP'])->name('login.forgot-password');
//
//// Menampilkan halaman verifikasi OTP
//Route::get('/verify-otp', function () {
//    if (!session('email')) {
//        return redirect()->route('login');
//    }
//    return view('login.verify-otp', ['email' => session('email')]);
//})->name('login.verify-otp.form');
//
//// Memproses verifikasi OTP
//Route::post('/verify-otp', [PasswordResetController::class, 'verifyOTP'])->name('login.verify-otp');
//
//// Mengirim ulang OTP
//Route::post('/resend-otp', [PasswordResetController::class, 'resendOTP'])->name('login.resend-otp');
//
//Route::get('/penerbitskt', function () {
//    return view('penerbitskt.index');
//})->name('penerbitskt');
//
//Route::get('/penerbitskt/layanan', function () {
//    return view('penerbitskt.layanan');
//})->name('penerbitskt.layanan');
//
//
//Route::prefix('admin/news')->name('admin.news.')->group(function () {
//    Route::get('/', [App\Http\Controllers\Web\Admin\NewsAdminController::class, 'index'])->name('index');
//    Route::get('/categories', [NewsAdminCategoryController::class, 'index'])->name('categories');
//});
//
//// 📰 Routes untuk halaman website (News)
//Route::prefix('news')->name('news.')->group(function () {
//    Route::get('/', [App\Http\Controllers\Web\NewsWebController::class, 'index'])->name('index');
//    Route::get('/category/{slug}', [App\Http\Controllers\Web\NewsWebController::class, 'category'])->name('category');
//    Route::get('/search', [App\Http\Controllers\Web\NewsWebController::class, 'search'])->name('search');
//    Route::get('/{slug}', [App\Http\Controllers\Web\NewsWebController::class, 'show'])->name('show');
//});
//


// File: routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\LoginController;
use App\Http\Controllers\Web\InsightWebController;
use App\Http\Controllers\Web\Admin\InsightAdminController;
use App\Http\Controllers\Web\Admin\CategoryAdminController;
use App\Http\Controllers\Web\Admin\NewsAdminController;
use App\Http\Controllers\Web\Admin\NewsAdminCategoryController;
use App\Http\Controllers\Web\Admin\TeamAdminController;
use App\Http\Controllers\Web\Admin\ClientAdminController;
use App\Http\Controllers\Web\Admin\GalleryAdminController;
use App\Http\Controllers\Web\NewsWebController;
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

// 📰 Routes untuk halaman website (News)
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsWebController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [NewsWebController::class, 'category'])->name('category');
    Route::get('/search', [NewsWebController::class, 'search'])->name('search');
    Route::get('/{slug}', [NewsWebController::class, 'show'])->name('show');
});

// 🔐 Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [LoginController::class, 'login'])
    ->middleware('guest');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// 🔑 Reset password routes
Route::post('/forgot-password', [PasswordResetController::class, 'sendOTP'])
    ->name('login.forgot-password')
    ->middleware('guest');

Route::get('/verify-otp', function () {
    if (!session('email')) {
        return redirect()->route('login');
    }
    return view('login.verify-otp', ['email' => session('email')]);
})->name('login.verify-otp.form')
    ->middleware('guest');

Route::post('/verify-otp', [PasswordResetController::class, 'verifyOTP'])
    ->name('login.verify-otp')
    ->middleware('guest');

Route::post('/resend-otp', [PasswordResetController::class, 'resendOTP'])
    ->name('login.resend-otp')
    ->middleware('guest');

// 📊 Routes untuk halaman admin (dashboard dan insights)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard utama
    Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('home');

    // Dashboard analytics
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Insights management
    Route::get('/insights', [InsightAdminController::class, 'index'])->name('insights');

    // Categories management
    Route::get('/categories', [CategoryAdminController::class, 'index'])->name('categories');

    // News management
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsAdminController::class, 'index'])->name('index');
        Route::get('/categories', [NewsAdminCategoryController::class, 'index'])->name('categories');
    });

    // Teams, Clients, Gallery
    Route::get('/teams', [TeamAdminController::class, 'index'])->name('teams');
    Route::get('/clients', [ClientAdminController::class, 'index'])->name('clients');
    Route::get('/gallery', [GalleryAdminController::class, 'index'])->name('gallery');
});

// Rute halaman statis
Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/penerbitskt', function () {
    return view('penerbitskt.index');
})->name('penerbitskt');

Route::get('/penerbitskt/layanan', function () {
    return view('penerbitskt.layanan');
})->name('penerbitskt.layanan');
