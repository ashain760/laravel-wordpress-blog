<?php

use App\Http\Controllers\Api\WpPostController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'wp.auth'])->group(function(){
    Route::get('/posts',[WpPostController::class,'index']);
    Route::post('/posts',[WpPostController::class,'store']);
    Route::put('/posts/{id}',[WpPostController::class,'update']);
    Route::delete('/posts/{id}',[WpPostController::class,'destroy']);
    Route::post('/posts/{id}/priority',[WpPostController::class,'setPriority']);
});
