<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Kategori;
use App\Models\Tag;

test('siapa aja bisa lihat list post', function () {
    Post::factory(5)->create();

    $response = $this->getJson('/api/posts');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'content']
            ]
        ]);
});

test('siapa aja bisa lihat detail post', function () {
    $post = Post::factory()->create();

    $response = $this->getJson("/api/posts/{$post->slug}");

    $response->assertStatus(200)
        ->assertJsonPath('data.title', $post->title);
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

    // pastiin data beneran tersimpan di database
    $this->assertDatabaseHas('posts', ['title' => 'Post Test']);
});

test('user yang belum login tidak bisa bikin post', function () {
    $response = $this->postJson('/api/posts', [
        'title' => 'Post Test',
        'content' => 'Konten test',
    ]);

    $response->assertStatus(401);
});

test('validasi gagal kalau title kosong', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/posts', [
            'content' => 'Konten tanpa judul',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('user bisa update post miliknya sendiri', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/posts/{$post->slug}", [
            'title' => 'Judul Diupdate',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'Judul Diupdate');

    $this->assertDatabaseHas('posts', ['title' => 'Judul Diupdate']);
});

test('user tidak bisa edit post milik orang lain', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userA->id]);

    $response = $this->actingAs($userB, 'sanctum')
        ->putJson("/api/posts/{$post->slug}", [
            'title' => 'Coba Edit Post Orang Lain',
        ]);

    $response->assertStatus(403);
});

test('user bisa hapus post miliknya sendiri', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/posts/{$post->slug}");

    $response->assertStatus(200);

    // pastiin soft deleted (masih ada di DB tapi deleted_at terisi)
    $this->assertSoftDeleted('posts', ['id' => $post->id]);
});

test('user tidak bisa hapus post milik orang lain', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userA->id]);

    $response = $this->actingAs($userB, 'sanctum')
        ->deleteJson("/api/posts/{$post->slug}");

    $response->assertStatus(403);
});
