<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Publik — siapa aja bisa akses tanpa login
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post:slug}', [PostController::class, 'show']);



// Butuh login — harus kirim token
Route::middleware(['auth:sanctum',"throttle:60,1"])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post:slug}', [PostController::class, 'update']);
    Route::delete('/posts/{post:slug}',[PostController::class,'destroy']);
});

//Add route to admin
Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::get('/admin/posts', [PostController::class, 'adminIndex']);
});

//rate limiting
Route::middleware('throttle:5,1')->group(function() {
    //max 5 request per minute
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

