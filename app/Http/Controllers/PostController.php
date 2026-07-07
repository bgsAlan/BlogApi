<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
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

    public function __construct(protected PostService $postService) {}

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->create($request->validated(), auth()->id());

        return new PostResource($post->load(['user', 'kategori', 'tags']));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post = $this->postService->update($post, $request->validated());

        return new PostResource($post->load(['user', 'kategori', 'tags']));
    }

    //add adminIndex
    public function adminIndex() {
        $posts = Post::with(['user','kategori','tags'])->latest()->paginate(10);
        return PostResource::collection($posts);
    }

    public function destroy(Post $post) {
        $this->authorize('delete',$post);
        $post->delete();
        return response()->json(['message' => 'Post berhasil dihapus']);
    }
}
