<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;
    // 投稿一覧へのアクセステスト
    public function test_guest_user_cannot_access_posts_index()
    {
        $respose = $this->get(route('posts.index'));
        $respose->assertRedirect(route('login'));
    }
    
    public function test_user_can_access_posts_index()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    // 投稿詳細へのアクセステスト
    public function test_guest_cannot_access_posts_show()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('posts.show', $post));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_posts_show()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    // 新規投稿画面へのアクセステスト
    public function test_guest_cannot_access_posts_create()
    {
        $response = $this->get(route('posts.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_posts_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('posts.create'));
        $response->assertStatus(200);
    }

    // 新規投稿テスト
    public function test_guest_cannot_access_posts_store()
    {
        $post = [
            'title' => 'test title',
            'content' => 'test content',
        ];

        $response = $this->post(route('posts.store'), $post);

        $this->assertDatabaseMissing('posts', $post);
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_posts_store()
    {
        $user = User::factory()->create();
        $post = [
            'title' => 'test title',
            'content' => 'test content',
        ];

        $response = $this->actingAs($user)->post(route('posts.store'), $post);

        $this->assertDatabaseHas('posts', $post);
        $response->assertRedirect(route('posts.index'));
    }
}
