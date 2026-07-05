<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


// Route::get('/posts', [PostController::class, 'index']);
// Route::get('/posts/{post}', [PostController::class, 'show']);
// Route::post('/posts', [PostController::class, 'store']);
// Route::put('/posts/{post}', [PostController::class, 'update']);

Route::apiResource('posts', PostController::class)->only(['index','show','store','update'])->parameters(['posts' => 'post:slug']);
