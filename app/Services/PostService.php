<?php
// app/Services/PostService.php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class PostService
{
    public function create(array $data, int $userId): Post
    {
        $post = new Post($data);
        $post->slug = Str::slug($data['title']) . '-' . Str::random(6);
        $post->user_id = $userId;

        if (($data['is_published'] ?? false) && empty($data['published_at'])) {
            $post->published_at = now();
        }

        $post->save();

        if (!empty($data['tags'])) {
            $post->tags()->attach($data['tags']);
        }

        return $post;
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post;
    }
}
