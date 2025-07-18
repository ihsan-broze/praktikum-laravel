<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;

class PostModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $author;
    private Category $category;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->author = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->post = Post::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id
        ]);
    }

    /** @test */
    public function post_belongs_to_user()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($this->user->id, $post->user->id);
    }

    /** @test */
    public function post_belongs_to_category()
    {
        $post = Post::factory()->create(['category_id' => $this->category->id]);
        
        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertEquals($this->category->id, $post->category->id);
    }

    /** @test */
    public function post_has_many_comments()
    {
        Comment::factory()->count(3)->create([
            'post_id' => $this->post->id,
            'status' => 'approved'
        ]);

        $this->assertCount(3, $this->post->comments);
        $this->assertInstanceOf(Comment::class, $this->post->comments->first());
    }

    /** @test */
    public function post_has_many_approved_comments()
    {
        // Create approved comments
        Comment::factory()->count(3)->create([
            'post_id' => $this->post->id,
            'status' => 'approved'
        ]);

        // Create pending comments
        Comment::factory()->count(2)->create([
            'post_id' => $this->post->id,
            'status' => 'pending'
        ]);

        $approvedComments = $this->post->approvedComments;
        
        $this->assertCount(3, $approvedComments);
        foreach ($approvedComments as $comment) {
            $this->assertEquals('approved', $comment->status);
        }
    }

    /** @test */
    public function post_can_get_comments_count()
    {
        Comment::factory()->count(5)->create([
            'post_id' => $this->post->id,
            'status' => 'approved'
        ]);

        Comment::factory()->count(2)->create([
            'post_id' => $this->post->id,
            'status' => 'pending'
        ]);

        $post = Post::withCount('comments')->find($this->post->id);
        $this->assertEquals(7, $post->comments_count);

        $post = Post::withCount('approvedComments')->find($this->post->id);
        $this->assertEquals(5, $post->approved_comments_count);
    }

    /** @test */
    public function post_scope_published_returns_only_published_posts()
    {
        // Clear any existing posts first
        Post::query()->delete();
        
        Post::factory()->count(3)->published()->create([
            'category_id' => $this->category->id
        ]);
        Post::factory()->count(2)->draft()->create([
            'category_id' => $this->category->id
        ]);

        $publishedPosts = Post::published()->get();
        
        $this->assertCount(3, $publishedPosts);
        foreach ($publishedPosts as $post) {
            $this->assertEquals('published', $post->status);
            $this->assertNotNull($post->published_at);
        }
    }

    /** @test */
    public function post_scope_draft_returns_only_draft_posts()
    {
        // Clear any existing posts first
        Post::query()->delete();
        
        Post::factory()->count(2)->published()->create([
            'category_id' => $this->category->id
        ]);
        Post::factory()->count(3)->draft()->create([
            'category_id' => $this->category->id
        ]);

        $draftPosts = Post::draft()->get();
        
        $this->assertCount(3, $draftPosts);
        foreach ($draftPosts as $post) {
            $this->assertEquals('draft', $post->status);
        }
    }

    /** @test */
    public function post_scope_recent_returns_posts_in_chronological_order()
    {
        // Clear any existing posts first
        Post::query()->delete();
        
        $oldPost = Post::factory()->create([
            'created_at' => Carbon::now()->subDays(5),
            'category_id' => $this->category->id
        ]);
        
        $newPost = Post::factory()->create([
            'created_at' => Carbon::now()->subDays(1),
            'category_id' => $this->category->id
        ]);

        $recentPosts = Post::recent()->get();
        
        $this->assertCount(2, $recentPosts);
        $this->assertEquals($newPost->id, $recentPosts->first()->id);
        $this->assertEquals($oldPost->id, $recentPosts->last()->id);
    }

    /** @test */
    public function post_scope_by_author_returns_posts_by_specific_author()
    {
        // Clear existing posts and create fresh ones
        Post::query()->delete();
        
        Post::factory()->count(3)->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id
        ]);
        Post::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $authorPosts = Post::byAuthor($this->author->id)->get();
        
        $this->assertCount(3, $authorPosts);
        foreach ($authorPosts as $post) {
            $this->assertEquals($this->author->id, $post->user_id);
        }
    }

    /** @test */
    public function post_has_slug_attribute()
    {
        $post = Post::factory()->withTitle('This is a Test Title')->create([
            'category_id' => $this->category->id
        ]);

        $this->assertNotNull($post->slug);
        $this->assertEquals('this-is-a-test-title', $post->slug);
    }

    /** @test */
    public function post_casts_published_at_to_carbon_instance()
    {
        $post = Post::factory()->create([
            'published_at' => '2024-01-01 12:00:00',
            'category_id' => $this->category->id
        ]);

        $this->assertInstanceOf(Carbon::class, $post->published_at);
    }

    /** @test */
    public function post_can_check_if_published()
    {
        $publishedPost = Post::factory()->published()->create([
            'category_id' => $this->category->id
        ]);

        $draftPost = Post::factory()->draft()->create([
            'category_id' => $this->category->id
        ]);

        $this->assertTrue($publishedPost->isPublished());
        $this->assertFalse($draftPost->isPublished());
    }

    /** @test */
    public function post_can_get_excerpt()
    {
        $longContent = str_repeat('This is a long content. ', 20);
        $post = Post::factory()->create([
            'content' => $longContent,
            'excerpt' => null, // Force it to use content
            'category_id' => $this->category->id
        ]);

        $excerpt = $post->getExcerpt(50);
        
        $this->assertLessThanOrEqual(53, strlen($excerpt)); // Account for "..." 
        $this->assertStringEndsWith('...', $excerpt);
    }

    /** @test */
    public function post_can_get_reading_time()
    {
        $content = str_repeat('word ', 250); // ~250 words
        $post = Post::factory()->create([
            'content' => $content,
            'category_id' => $this->category->id
        ]);

        $readingTime = $post->getReadingTime();
        
        $this->assertIsInt($readingTime);
        $this->assertGreaterThan(0, $readingTime);
        $this->assertEquals(2, $readingTime); // 250 words / 200 WPM = 2 minutes (rounded up)
    }

    /** @test */
    public function post_soft_deletes()
    {
        $post = Post::factory()->create(['category_id' => $this->category->id]);
        $postId = $post->id;

        $post->delete();

        $this->assertSoftDeleted('posts', ['id' => $postId]);
        $this->assertNull(Post::find($postId));
        $this->assertNotNull(Post::withTrashed()->find($postId));
    }

    /** @test */
    public function post_factory_creates_valid_post()
    {
        $post = Post::factory()->create(['category_id' => $this->category->id]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
        ]);

        $this->assertNotNull($post->title);
        $this->assertNotNull($post->content);
        $this->assertNotNull($post->user_id);
        $this->assertNotNull($post->category_id);
    }

    /** @test */
    public function post_factory_can_create_published_post()
    {
        $post = Post::factory()->published()->create([
            'category_id' => $this->category->id
        ]);

        $this->assertEquals('published', $post->status);
        $this->assertNotNull($post->published_at);
    }

    /** @test */
    public function post_factory_can_create_draft_post()
    {
        $post = Post::factory()->draft()->create([
            'category_id' => $this->category->id
        ]);

        $this->assertEquals('draft', $post->status);
        $this->assertNull($post->published_at);
    }

    /** @test */
    public function post_validates_required_fields()
    {
        // This test expects a QueryException when required fields are missing
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Post::create([]); // This should fail due to NOT NULL constraints
    }

    /** @test */
    public function post_title_must_be_unique()
    {
        // Note: This test assumes you have a unique constraint on title
        // If you don't have this constraint, this test will fail
        Post::factory()->create([
            'title' => 'Unique Title',
            'category_id' => $this->category->id
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Post::factory()->create([
            'title' => 'Unique Title',
            'category_id' => $this->category->id
        ]);
    }

    /** @test */
    public function post_slug_is_generated_from_title()
    {
        $post = Post::factory()->create([
            'title' => 'This is My Amazing Post Title!',
            'category_id' => $this->category->id,
        ]);

        $expectedSlug = 'this-is-my-amazing-post-title';
        $this->assertEquals($expectedSlug, $post->slug);
    }

    /** @test */
    public function post_can_be_searched_by_title_and_content()
    {
        Post::factory()->create([
            'title' => 'Laravel Testing Guide',
            'content' => 'This is about PHP testing',
            'category_id' => $this->category->id
        ]);

        Post::factory()->create([
            'title' => 'Vue.js Tutorial',
            'content' => 'This is about JavaScript',
            'category_id' => $this->category->id
        ]);

        $results = Post::search('Laravel')->get();
        $this->assertCount(1, $results);
        $this->assertStringContainsString('Laravel', $results->first()->title);

        $results = Post::search('JavaScript')->get();
        $this->assertCount(1, $results);
        $this->assertStringContainsString('JavaScript', $results->first()->content);
    }

    /** @test */
    public function post_can_get_related_posts()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        
        $mainPost = Post::factory()->create([
            'category_id' => $category1->id,
            'status' => 'published'
        ]);

        // Create related posts in same category
        Post::factory()->count(3)->create([
            'category_id' => $category1->id,
            'status' => 'published'
        ]);

        // Create posts in different category
        Post::factory()->count(2)->create([
            'category_id' => $category2->id,
            'status' => 'published'
        ]);

        $relatedPosts = $mainPost->getRelatedPosts(2);
        
        $this->assertCount(2, $relatedPosts);
        foreach ($relatedPosts as $post) {
            $this->assertEquals($category1->id, $post->category_id);
            $this->assertNotEquals($mainPost->id, $post->id);
        }
    }

    /** @test */
    public function post_increments_view_count()
    {
        $post = Post::factory()->withViews(5)->create([
            'category_id' => $this->category->id
        ]);
        
        $initialViews = $post->views;
        $post->incrementViews();
        
        $this->assertEquals($initialViews + 1, $post->fresh()->views);
    }

    /** @test */
    public function post_can_check_if_commentable()
    {
        $commentablePost = Post::factory()->withComments()->create([
            'category_id' => $this->category->id
        ]);

        $nonCommentablePost = Post::factory()->withoutComments()->create([
            'category_id' => $this->category->id
        ]);

        $this->assertTrue($commentablePost->isCommentable());
        $this->assertFalse($nonCommentablePost->isCommentable());
    }
}