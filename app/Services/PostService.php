<?php
// app/Services/PostService.php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function create(array $data, int $userId): Post
    {
        $post = new Post($data);
        $post->slug = Str::slug($data['title']) . '-' . Str::random(6);
        $post->user_id = $userId;

        //Check if thumbnail is send
        if(isset($data['thumbnail']) && $data['thumbnail'] instanceOf UploadedFile) {
            $post->thumbnail = $data['thumbnail']->store('thumbnails', 'public');
        }
        if (($data['is_published'] ?? false) && empty($data['published_at'])) {
            $post->published_at = now();
        }

        $post->save();

        if (!empty($data['tags'])) {
            $post->tags()->attach($data['tags']);
        }
        Cache::flush();
        return $post;
    }

    public function update(Post $post, array $data): Post
    {
        // 1. Proses thumbnail DULU, sebelum $post->update()
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            // hapus yang lama, kalau ada
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail); // path udah lengkap, disk yang benar
            }

            // upload baru, simpen path-nya
            $data['thumbnail'] = $data['thumbnail']->store('thumbnails', 'public');
        }

        // 2. Baru update, $data['thumbnail'] sekarang udah berupa STRING path, bukan object
        $post->update($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        Cache::flush();

        return $post;
    }
}
