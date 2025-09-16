<?php

use App\Http\Controllers\WpLogoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WpAuthController;

Route::get('/wp/login',[WpAuthController::class,'redirectToWp']);
Route::get('/wp/callback',[WpAuthController::class,'handleCallback']);
Route::view('/{any}', 'welcome')->where('any', '.*'); // SPA catch-all

Route::post('/logout', [WpLogoutController::class, 'logout'])->name('logout');
