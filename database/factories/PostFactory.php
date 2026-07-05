<?php

// database/factories/PostFactory.php
namespace Database\Factories;

use App\Models\User;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'user_id' => User::factory(),
            'kategori_id' => Kategori::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'content' => fake()->paragraphs(4, true),
            'thumbnail' => fake()->imageUrl(640, 480, 'posts'),
            'is_published' => true,
            'published_at' => now(),
        ];
    }
}
