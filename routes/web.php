<?php
// File: routes/web.php

use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\HomeController;
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


Route::get('/robots.txt', function () {
    $content = "";

    if (app()->environment('production')) {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\nDisallow: /forgot-password\nDisallow: /verify-otp\n\nSitemap: " . url('/sitemap.xml');
    } else {
        $content = "User-agent: *\nDisallow: /";
    }

    return response($content, 200)
        ->header('Content-Type', 'text/plain');
});
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
Route::get('/forgot-password', function () {
    return view('login.forgot-password');
})->name('password.request')
    ->middleware('guest');

Route::post('/forgot-password', [PasswordResetController::class, 'sendOTP'])
    ->name('login.forgot-password')
    ->middleware('guest');

// Perbaikan: Menggunakan controller method untuk halaman verify OTP
Route::get('/verify-otp', [PasswordResetController::class, 'showVerifyOtpForm'])
    ->name('login.verify-otp.form')
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
    $seoData = new \RalphJSmit\Laravel\SEO\Support\SEOData(
        title: 'Tentang Kami',
        description: 'Kenali lebih dekat ApCom Solutions dan layanan kami dalam membangun reputasi bisnis Anda.',
        url: url('/about')
    );

    return view('about', ['seoData' => $seoData]);
});

Route::get('/contact', function () {
    $seoData = new \RalphJSmit\Laravel\SEO\Support\SEOData(
        title: 'Hubungi Kami',
        description: 'Hubungi tim ApCom Solutions untuk konsultasi komunikasi dan PR untuk bisnis Anda.',
        url: url('/contact')
    );

    return view('contact', ['seoData' => $seoData]);
});

