<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-n1', function () {
    $posts = \App\Models\Post::all();
    foreach ($posts as $post) {
        echo $post->user->name . '<br>';
    }
});
Route::get('/test-eager', function () {
    $posts = \App\Models\Post::with('user')->get();
    foreach ($posts as $post) {
        echo $post->user->name . '<br>';
    }
});
Route::get('/compare/nested', function () {
    $posts = \App\Models\Post::with(['user', 'kategori', 'tags', 'comments.user'])->get();
});
