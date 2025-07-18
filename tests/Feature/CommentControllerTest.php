<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $moderator;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->moderator = User::factory()->create(['role' => 'moderator']);
        $this->post = Post::factory()->published()->create();
    }

        /** @test */
    public function guests_can_view_comments_for_post()
    {
        // Create a post with a specific ID or use the existing one
        $post = Post::factory()->create();
        
        // Create 5 approved comments for this specific post
        Comment::factory()->count(5)->create([
            'post_id' => $post->id,
            'status' => 'approved',
        ]);
        
        // Create some pending comments to ensure filtering works
        Comment::factory()->count(3)->create([
            'post_id' => $post->id,
            'status' => 'pending',
        ]);
        
        // Use the post's slug instead of id (since getRouteKeyName() returns 'slug')
        $response = $this->getJson("/api/posts/{$post->slug}/comments");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'comments' => [
                    'data' => [
                        '*' => ['id', 'content', 'created_at']
                    ]
                ]
            ]);
        
        $data = $response->json();
        $this->assertCount(5, $data['comments']['data']);
    }

    /** @test */
    public function authenticated_users_can_create_comments()
    {
        $commentData = [
            'content' => 'This is a test comment.',
            'post_id' => $this->post->id,
        ];

        $response = $this->actingAs($this->user)->post(route('comments.store'), $commentData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('comments', [
            'content' => $commentData['content'],
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'status' => 'approved', // Auto-approved for verified users
        ]);
    }

    /** @test */
    public function guests_can_create_comments_with_name_and_email()
    {
        $commentData = [
            'content' => 'This is a guest comment.',
            'post_id' => $this->post->id,
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
        ];

        $response = $this->post(route('comments.store'), $commentData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('comments', [
            'content' => $commentData['content'],
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'status' => 'pending', // Pending for guests
        ]);
    }

    /** @test */
    public function users_can_create_reply_comments()
    {
        $parentComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'status' => 'approved'
        ]);

        $replyData = [
            'content' => 'This is a reply comment.',
            'post_id' => $this->post->id,
            'parent_id' => $parentComment->id,
        ];

        $response = $this->actingAs($this->user)->post(route('comments.store'), $replyData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('comments', [
            'content' => $replyData['content'],
            'parent_id' => $parentComment->id,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function comment_creation_validates_required_fields()
    {
        $response = $this->post(route('comments.store'), []);

        $response->assertSessionHasErrors(['content', 'post_id']);
    }

    /** @test */
    public function comment_creation_validates_guest_fields()
    {
        $response = $this->post(route('comments.store'), [
            'content' => 'Test comment',
            'post_id' => $this->post->id,
            // Missing author_name and author_email for guests
        ]);

        $response->assertSessionHasErrors(['author_name', 'author_email']);
    }

    /** @test */
    public function authenticated_users_can_view_specific_comment()
    {
        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->user)->get(route('comments.show', $comment));

        $response->assertStatus(200);
        $response->assertViewIs('comments.show');
        $response->assertViewHas('comment', $comment);
    }

    /** @test */
    public function users_can_update_their_own_comments()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'approved'
        ]);

        $updateData = [
            'content' => 'Updated comment content.',
        ];

        $response = $this->actingAs($this->user)->put(route('comments.update', $comment), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment content.',
            'status' => 'pending', // Marked as pending after edit
        ]);
    }

    /** @test */
    public function users_cannot_update_others_comments()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'content' => 'Hacked content.',
        ];

        $response = $this->actingAs($this->user)->put(route('comments.update', $comment), $updateData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('comments', ['content' => 'Hacked content.']);
    }

    /** @test */
    public function users_can_delete_their_own_comments()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function users_cannot_delete_others_comments()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete(route('comments.destroy', $comment));

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function moderators_can_approve_comments()
    {
        $comment = Comment::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->moderator)->patch(route('moderator.comments.approve', $comment));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Comment approved successfully!');
        
        $this->assertEquals('approved', $comment->fresh()->status);
    }

    /** @test */
    public function moderators_can_reject_comments()
    {
        $comment = Comment::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->moderator)->patch(route('moderator.comments.reject', $comment));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Comment rejected successfully!');
        
        $this->assertEquals('rejected', $comment->fresh()->status);
    }

    /** @test */
    public function moderators_can_mark_comments_as_spam()
    {
        $comment = Comment::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($this->moderator)->patch(route('moderator.comments.spam', $comment));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Comment marked as spam!');
        
        $this->assertEquals('spam', $comment->fresh()->status);
    }

    /** @test */
    public function regular_users_cannot_moderate_comments()
    {
        $comment = Comment::factory()->create(['status' => 'pending']);

        // Test with regular user - expect 403 or 302 redirect
        $response = $this->actingAs($this->user)->patch(route('moderator.comments.approve', $comment));

        // Accept either 403 (direct) or 302 (redirect) as both indicate access denied
        $this->assertContains($response->getStatusCode(), [302, 403]);
        $this->assertEquals('pending', $comment->fresh()->status);
    }

    /** @test */
    public function moderators_can_view_moderation_page()
    {
        Comment::factory()->count(3)->create(['status' => 'pending']);
        Comment::factory()->count(2)->create(['status' => 'spam']);

        // Gunakan route moderator, bukan comments
        $response = $this->actingAs($this->moderator)->get(route('moderator.comments.moderate'));

        $response->assertStatus(200);
        $response->assertViewIs('comments.moderate');
        $response->assertViewHas('pendingComments');
        $response->assertViewHas('spamComments');
        
        $pendingComments = $response->viewData('pendingComments');
        $spamComments = $response->viewData('spamComments');
        
        $this->assertCount(3, $pendingComments);
        $this->assertCount(2, $spamComments);
    }

    /** @test */
    public function regular_users_cannot_view_moderation_page()
    {
        // Test with regular user - expect 403 or 302 redirect
        $response = $this->actingAs($this->user)->get(route('moderator.comments.moderate'));

        // Accept either 403 (direct) or 302 (redirect) as both indicate access denied
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    /** @test */
    public function moderators_can_bulk_moderate_comments()
    {
        $comments = Comment::factory()->count(3)->create(['status' => 'pending']);
        $commentIds = $comments->pluck('id')->toArray();

        $bulkData = [
            'comment_ids' => $commentIds,
            'action' => 'approve',
        ];

        $response = $this->actingAs($this->moderator)->post(route('moderator.comments.bulk-moderate'), $bulkData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Comments approved successfully!');
        
        foreach ($comments as $comment) {
            $this->assertEquals('approved', $comment->fresh()->status);
        }
    }

    /** @test */
    public function bulk_moderation_validates_data()
    {
        $response = $this->actingAs($this->moderator)->post(route('moderator.comments.bulk-moderate'), []);

        $response->assertSessionHasErrors(['comment_ids', 'action']);
    }

    /** @test */
    public function bulk_moderation_can_delete_comments()
    {
        $comments = Comment::factory()->count(2)->create(['status' => 'spam']);
        $commentIds = $comments->pluck('id')->toArray();

        $bulkData = [
            'comment_ids' => $commentIds,
            'action' => 'delete',
        ];

        $response = $this->actingAs($this->moderator)->post(route('moderator.comments.bulk-moderate'), $bulkData);

        $response->assertRedirect();
        
        foreach ($comments as $comment) {
            $this->assertSoftDeleted('comments', ['id' => $comment->id]);
        }
    }

    /** @test */
    public function can_get_comment_replies()
    {
        $parentComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'status' => 'approved'
        ]);
        
        Comment::factory()->count(3)->create([
            'parent_id' => $parentComment->id,
            'status' => 'approved'
        ]);
        
        Comment::factory()->create([
            'parent_id' => $parentComment->id,
            'status' => 'pending'
        ]);

        $response = $this->getJson(route('comments.replies', $parentComment));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'replies' => [
                '*' => ['id', 'content', 'parent_id']
            ],
            'total'
        ]);
        
        $data = $response->json();
        $this->assertCount(3, $data['replies']); // Only approved replies
        $this->assertEquals(3, $data['total']);
    }

    /** @test */
    public function comment_stores_metadata()
    {
        $commentData = [
            'content' => 'Test comment with metadata.',
            'post_id' => $this->post->id,
            'author_name' => 'Test User',
            'author_email' => 'test@example.com',
        ];

        $response = $this->post(route('comments.store'), $commentData);

        $comment = Comment::where('content', $commentData['content'])->first();
        
        $this->assertNotNull($comment->ip_address);
        $this->assertNotNull($comment->user_agent);
    }

    /** @test */
    public function ajax_comment_creation_returns_json()
    {
        $commentData = [
            'content' => 'AJAX comment test.',
            'post_id' => $this->post->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('comments.store'), $commentData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'comment' => ['id', 'content', 'user'],
            'status'
        ]);
    }

    /** @test */
    public function ajax_comment_update_returns_json()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'content' => 'Updated via AJAX.',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('comments.update', $comment), $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'comment'
        ]);
    }

    /** @test */
    public function ajax_comment_deletion_returns_json()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('comments.destroy', $comment));

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
    }

    /** @test */
    public function ajax_comment_moderation_returns_json()
    {
        $comment = Comment::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->moderator)
            ->patchJson(route('moderator.comments.approve', $comment));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'comment'
        ]);
    }

    /** @test */
    public function comment_content_validation()
    {
        $invalidData = [
            'content' => '', // Required
            'post_id' => 999, // Must exist
            'parent_id' => 999, // Must exist if provided
        ];

        $response = $this->actingAs($this->user)->post(route('comments.store'), $invalidData);

        $response->assertSessionHasErrors(['content', 'post_id', 'parent_id']);
    }

    /** @test */
    public function comment_content_length_validation()
    {
        $longContent = str_repeat('a', 1001); // Over 1000 chars

        $commentData = [
            'content' => $longContent,
            'post_id' => $this->post->id,
        ];

        $response = $this->actingAs($this->user)->post(route('comments.store'), $commentData);

        $response->assertSessionHasErrors(['content']);
    }
}