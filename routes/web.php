<?php

use App\Http\Controllers\Web\InsightWebController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Admin\InsightAdminController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/insights', [InsightWebController::class, 'index'])->name('insights.index');
Route::get('/insights/{slug}', [InsightWebController::class, 'show'])->name('insights.show');
Route::get('/admin/insights', [InsightWebController::class, 'index']);



Route::get('/admin/insights', [InsightAdminController::class, 'index'])->name('admin.insights');


Route::get('/test', function () {
  dd(file_exists(storage_path('app/public/img/hero.png')));
});