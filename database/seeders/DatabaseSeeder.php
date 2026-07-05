<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(5)->create();
        $kategoris = Kategori::factory(4)->create();
        $tags = Tag::factory(8)->create();

        Post::factory(20)
            ->recycle($users)     // pakai user yang udah ada, bukan bikin user baru tiap post
            ->recycle($kategoris) // sama, pakai kategori yang udah ada
            ->create()
            ->each(function (Post $post) use ($tags, $users) {
                // attach 1-3 tag random ke tiap post
                $post->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')
                );

                // 0-5 comment random per post
                Comment::factory(rand(0, 5))
                    ->recycle($post)
                    ->recycle($users)
                    ->create();
            });
    }
}