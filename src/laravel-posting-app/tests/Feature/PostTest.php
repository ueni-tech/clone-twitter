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

    // 投稿編集画面へのアクセステスト
    public function test_guest_cannot_access_posts_edit()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('posts.edit', $post));

        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_access_others_posts_edit()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();
        $others_post = Post::factory()->create(['user_id' => $other_user->id]);

        $response = $this->actingAs($user)->get(route('posts.edit', $others_post));

        $response->assertRedirect(route('posts.index'));
    }

    public function test_user_can_access_posts_edit()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('posts.edit', $post));

        $response->assertStatus(200);
    }

    // 投稿更新テスト
    public function test_guest_cannot_update_post()
    {
        $user = User::factory()->create();
        $old_post = Post::factory()->create(['user_id' => $user->id]);

        $new_post = [
            'title' => 'new title',
            'content' => 'new content',
        ];

        $response = $this->patch(route('posts.update', $old_post), $new_post);
        $this->assertDatabaseMissing('posts', $new_post);
        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_update_others_post()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();
        $others_old_post = Post::factory()->create(['user_id' => $other_user->id]);

        $new_post = [
            'title' => 'new title',
            'content' => 'new content',
        ];

        $response = $this->actingAs($user)->patch(route('posts.update', $others_old_post), $new_post);
        $this->assertDatabaseMissing('posts', $new_post);
        $response->assertRedirect(route('posts.index'));
    }

    public function test_user_can_update_post()
    {
        $user = User::factory()->create();
        $old_post = Post::factory()->create(['user_id' => $user->id]);

        $new_post = [
            'title' => 'new title',
            'content' => 'new content',
        ];

        $response = $this->actingAs($user)->patch(route('posts.update', $old_post), $new_post);
        $this->assertDatabaseHas('posts', $new_post);
        $response->assertRedirect(route('posts.show', $old_post));
    }

    // 投稿削除テスト
    public function test_guest_cannot_destroy_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('posts.destroy', $post));

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_destroy_others_post()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();
        $others_post = Post::factory()->create(['user_id' => $other_user->id]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $others_post));

        $this->assertDatabaseHas('posts', ['id' => $others_post->id]);
        $response->assertRedirect(route('posts.index'));
    }

    public function test_user_can_destroy_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $response->assertRedirect(route('posts.index'));
    }
}
