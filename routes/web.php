<?php

use App\Http\Controllers\Web\InsightWebController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/insights', [InsightWebController::class, 'index']);
Route::get('/insights/{$id}', [InsightWebController::class, 'show'])->name('insights.show');
