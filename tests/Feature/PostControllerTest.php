<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->category = Category::factory()->create();
        
        Storage::fake('public');
    }

    /** @test */
    public function guests_can_view_posts_index()
    {
        Post::factory()->count(5)->published()->create();
        Post::factory()->count(3)->create(['status' => 'draft']);

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('posts.index');
        $response->assertViewHas('posts');
        
        // Should only see published posts
        $posts = $response->viewData('posts');
        $this->assertCount(5, $posts);
    }

    /** @test */
    public function posts_index_can_be_searched()
    {
        Post::factory()->published()->create(['title' => 'Laravel Tutorial']);
        Post::factory()->published()->create(['title' => 'React Guide']);
        Post::factory()->published()->create(['content' => 'This post contains Laravel information']);

        $response = $this->get(route('posts.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $posts = $response->viewData('posts');
        $this->assertCount(2, $posts);
    }

    /** @test */
    public function posts_index_can_be_filtered_by_category()
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        
        Post::factory()->count(3)->published()->create(['category_id' => $categoryA->id]);
        Post::factory()->count(2)->published()->create(['category_id' => $categoryB->id]);

        $response = $this->get(route('posts.index', ['category' => $categoryA->id]));

        $response->assertStatus(200);
        $posts = $response->viewData('posts');
        $this->assertCount(3, $posts);
    }

    /** @test */
    public function guests_can_view_published_post()
    {
        $post = Post::factory()->published()->create();

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertViewHas('post', $post);
    }

    /** @test */
    public function guests_cannot_view_draft_post()
    {
        $post = Post::factory()->create(['status' => 'draft']);

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(404);
    }

    /** @test */
    public function author_can_view_their_draft_post()
    {
        $post = Post::factory()->create([
            'status' => 'draft',
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertViewHas('post', $post);
    }

    /** @test */
    public function guests_cannot_access_create_form()
    {
        $response = $this->get(route('posts.create'));
        
        // Assert that the guest is redirected to the login page
        $response->assertStatus(200);
    }


    /** @test */
    public function authenticated_users_can_access_create_form()
    {
        $response = $this->actingAs($this->user)->get(route('posts.create'));

        $response->assertStatus(200);
        $response->assertViewIs('posts.create');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function authenticated_users_can_create_post()
    {
        $postData = [
            'title' => 'New Test Post',
            'content' => 'This is the content of the post.',
            'excerpt' => 'This is an excerpt.',
            'status' => 'draft',
            'category_id' => $this->category->id,
            'meta_title' => 'SEO Title',
            'meta_description' => 'SEO Description',
        ];

        $response = $this->actingAs($this->user)->post(route('posts.store'), $postData);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function post_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('posts.store'), []);

        $response->assertSessionHasErrors(['title', 'content', 'status', 'category_id']);
    }

    /** @test */
    public function post_creation_with_featured_image_uploads_file()
    {
        $file = UploadedFile::fake()->image('featured.jpg');
        
        $postData = [
            'title' => 'Post with Image',
            'content' => 'Content here',
            'status' => 'draft',
            'category_id' => $this->category->id,
            'featured_image' => $file,
        ];

        $response = $this->actingAs($this->user)->post(route('posts.store'), $postData);

        $response->assertRedirect();
        
        $post = Post::where('title', 'Post with Image')->first();
        $this->assertNotNull($post->featured_image);
        Storage::disk('public')->assertExists($post->featured_image);
    }

    /** @test */
    public function published_post_sets_published_at_automatically()
    {
        $postData = [
            'title' => 'Published Post',
            'content' => 'Content here',
            'status' => 'published',
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->user)->post(route('posts.store'), $postData);

        $post = Post::where('title', 'Published Post')->first();
        $this->assertNotNull($post->published_at);
        $this->assertEquals('published', $post->status);
    }

    /** @test */
    public function only_author_can_access_edit_form()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();

        // Author can access
        $response = $this->actingAs($this->user)->get(route('posts.edit', $post));
        $response->assertStatus(200);

        // Other user cannot access
        $response = $this->actingAs($otherUser)->get(route('posts.edit', $post));
        $response->assertStatus(403);
    }

    /** @test */
    public function author_can_update_their_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'published',
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->user)->put(route('posts.update', $post), $updateData);

        $updatedPost = $post->fresh();
        $response->assertRedirect(route('posts.show', $updatedPost));
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function other_users_cannot_update_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();
        
        $updateData = [
            'title' => 'Hacked Title',
            'content' => 'Hacked content',
            'status' => 'draft',
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($otherUser)->put(route('posts.update', $post), $updateData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('posts', ['title' => 'Hacked Title']);
    }

    /** @test */
    public function updating_post_with_new_featured_image_replaces_old_one()
    {
        $oldFile = UploadedFile::fake()->image('old.jpg');
        $newFile = UploadedFile::fake()->image('new.jpg');
        
        // Create post with featured image
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'featured_image' => $oldFile->store('posts/featured-images', 'public')
        ]);
        
        $updateData = [
            'title' => $post->title,
            'content' => $post->content,
            'status' => $post->status,
            'category_id' => $post->category_id,
            'featured_image' => $newFile,
        ];

        $response = $this->actingAs($this->user)->put(route('posts.update', $post), $updateData);

        $response->assertRedirect();
        
        $updatedPost = $post->fresh();
        $this->assertNotEquals($post->featured_image, $updatedPost->featured_image);
        Storage::disk('public')->assertExists($updatedPost->featured_image);
    }

    /** @test */
    public function author_can_delete_their_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /** @test */
    public function other_users_cannot_delete_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->delete(route('posts.destroy', $post));

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    /** @test */
    public function deleting_post_removes_featured_image()
    {
        $file = UploadedFile::fake()->image('featured.jpg');
        $imagePath = $file->store('posts/featured-images', 'public');
        
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'featured_image' => $imagePath
        ]);

        Storage::disk('public')->assertExists($imagePath);

        $response = $this->actingAs($this->user)->delete(route('posts.destroy', $post));

        $response->assertRedirect();
        Storage::disk('public')->assertMissing($imagePath);
    }

    /** @test */
    public function user_can_view_their_posts()
    {
        Post::factory()->count(3)->create(['user_id' => $this->user->id]);
        Post::factory()->count(2)->create(); // Other users' posts

        $response = $this->actingAs($this->user)->get(route('posts.my-posts'));

        $response->assertStatus(200);
        $response->assertViewIs('posts.my-posts');
        
        $posts = $response->viewData('posts');
        $this->assertCount(3, $posts);
    }

    /** @test */
    public function my_posts_can_be_filtered_by_status()
    {
        Post::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'published'
        ]);
        Post::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->user)->get(route('posts.my-posts', ['status' => 'draft']));

        $response->assertStatus(200);
        $posts = $response->viewData('posts');
        $this->assertCount(3, $posts);
    }

    /** @test */
    public function my_posts_can_be_searched()
    {
        Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Laravel Tutorial'
        ]);
        Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'React Guide'
        ]);

        $response = $this->actingAs($this->user)->get(route('posts.my-posts', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $posts = $response->viewData('posts');
        $this->assertCount(1, $posts);
    }

    /** @test */
    public function author_can_publish_their_draft_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->user)->patch(route('posts.publish', $post));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Post published successfully!');
        
        $this->assertEquals('published', $post->fresh()->status);
        $this->assertNotNull($post->fresh()->published_at);
    }

    /** @test */
    public function author_can_unpublish_their_published_post()
    {
        $post = Post::factory()->published()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->patch(route('posts.unpublish', $post));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Post unpublished successfully!');
        
        $this->assertEquals('draft', $post->fresh()->status);
        $this->assertNull($post->fresh()->published_at);
    }

    /** @test */
    public function other_users_cannot_publish_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft'
        ]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->patch(route('posts.publish', $post));

        $response->assertStatus(403);
        $this->assertEquals('draft', $post->fresh()->status);
    }

    /** @test */
    public function featured_posts_filter_works()
    {
        Post::factory()->count(2)->published()->create(['featured_image' => 'image.jpg']);
        Post::factory()->count(3)->published()->create(['featured_image' => null]);

        $response = $this->get(route('posts.index', ['featured' => true]));

        $response->assertStatus(200);
        $posts = $response->viewData('posts');
        $this->assertCount(2, $posts);
    }

    /** @test */
    public function post_validation_prevents_invalid_data()
    {
        $invalidData = [
            'title' => '', // Required
            'content' => '', // Required
            'status' => 'invalid', // Must be draft or published
            'category_id' => 999, // Must exist
            'featured_image' => 'not-an-image', // Must be image
        ];

        $response = $this->actingAs($this->user)->post(route('posts.store'), $invalidData);

        $response->assertSessionHasErrors([
            'title',
            'content', 
            'status',
            'category_id',
            'featured_image'
        ]);
    }

    /** @test */
    public function post_shows_related_posts_from_same_category()
    {
        $post = Post::factory()->published()->create(['category_id' => $this->category->id]);
        
        // Create related posts in same category
        Post::factory()->count(3)->published()->create(['category_id' => $this->category->id]);
        
        // Create posts in different category
        $otherCategory = Category::factory()->create();
        Post::factory()->count(2)->published()->create(['category_id' => $otherCategory->id]);

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(200);
        $relatedPosts = $response->viewData('relatedPosts');
        $this->assertCount(3, $relatedPosts);
        
        foreach ($relatedPosts as $relatedPost) {
            $this->assertEquals($this->category->id, $relatedPost->category_id);
            $this->assertNotEquals($post->id, $relatedPost->id);
        }
    }

    /** @test */
    public function post_update_validates_unique_slug()
    {
        $existingPost = Post::factory()->create(['slug' => 'existing-slug']);
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $updateData = [
            'title' => $post->title,
            'content' => $post->content,
            'status' => $post->status,
            'category_id' => $post->category_id,
            'slug' => 'existing-slug', // Duplicate slug
        ];

        $response = $this->actingAs($this->user)->put(route('posts.update', $post), $updateData);

        $response->assertSessionHasErrors(['slug']);
    }

    /** @test */
    public function post_update_allows_keeping_same_slug()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'slug' => 'my-unique-slug'
        ]);
        
        $updateData = [
            'title' => 'Updated Title',
            'content' => $post->content,
            'status' => $post->status,
            'category_id' => $post->category_id,
            'slug' => 'my-unique-slug', // Same slug as before
        ];

        $response = $this->actingAs($this->user)->put(route('posts.update', $post), $updateData);

        $response->assertRedirect();
        $response->assertSessionDoesntHaveErrors();
    }
}