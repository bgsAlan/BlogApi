<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Kategori;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('siapa aja bisa lihat list post', function () {
    $response = $this->getJson('/api/posts');
    $response->assertStatus(200);
});

test('user yang login bisa bikin post', function () {
    $user = User::factory()->create();
    $kategori = Kategori::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/posts', [
            'title' => 'Post Test',
            'content' => 'Konten test',
            'kategori_id' => $kategori->id,
            'is_published' => true,
        ]);
        
    $response->assertStatus(201)
    
        ->assertJsonPath('data.title', 'Post Test');
});

test('user yang belum login tidak bisa bikin post', function () {
    $response = $this->postJson('/api/posts', [
        'title' => 'Post Test',
        'content' => 'Konten test',
    ]);

    $response->assertStatus(401);
});

test('user tidak bisa edit post milik orang lain', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $kategori = Kategori::factory()->create();

    $post = Post::factory()->create(['user_id' => $userA->id]);

    $response = $this->actingAs($userB, 'sanctum')
        ->putJson("/api/posts/{$post->slug}", [
            'title' => 'Coba Edit Post Orang Lain',
        ]);

    $response->assertStatus(403);
});
