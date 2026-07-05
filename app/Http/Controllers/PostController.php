<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;


class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::published()->with(['user', 'kategori', 'tags'])
            ->when($request->kategori, function ($query, $kategori) {
                $query->whereHas('kategori', fn($q) => $q->where('slug', $kategori));
            })
            ->when($request->tag, function ($query, $tag) {
                $query->whereHas('tags', fn($q) => $q->where('slug', $tag));
            })
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->paginate(10);
        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        $post->load(['user', 'kategori', 'tags', 'comments.user']);
        return new PostResource($post);
    }

    public function store(StorePostRequest $request)
    {
        $post = new Post($request->validated());
        $post->slug = Str::slug($request->title) . '-' . Str::random(6);
        $post->user_id = auth()->id();
        //update published_at disaat is_published true
        if ($request->is_published && !$request->published_at) {
            $post->published_at = now();
        }
        $post->save();

        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }

        return response()->json($post->load(['user', 'kategori', 'tags']), 201);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }
        return response()->json($post->load(['user', 'kategori', 'tags']));
    }
}
