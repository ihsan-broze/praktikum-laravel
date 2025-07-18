<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CommentModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Post $post;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create all dependencies properly
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->post = Post::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);
    }

    /** @test */
    public function it_can_create_a_comment()
    {
        $commentData = [
            'content' => 'This is a test comment.',
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'status' => 'approved',
            'post_id' => $this->post->id,
        ];

        $comment = Comment::create($commentData);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($commentData['content'], $comment->content);
        $this->assertEquals($commentData['author_name'], $comment->author_name);
        $this->assertEquals($commentData['status'], $comment->status);
        $this->assertDatabaseHas('comments', $commentData);
    }

    /** @test */
    public function it_belongs_to_a_post()
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id]);

        $this->assertInstanceOf(Post::class, $comment->post);
        $this->assertEquals($this->post->id, $comment->post->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(User::class, $comment->user);
        $this->assertEquals($this->user->id, $comment->user->id);
    }

    /** @test */
    public function it_can_have_a_parent_comment()
    {
        $parentComment = Comment::factory()->create();
        $replyComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $this->assertInstanceOf(Comment::class, $replyComment->parent);
        $this->assertEquals($parentComment->id, $replyComment->parent->id);
    }

    /** @test */
    public function it_can_have_reply_comments()
    {
        $parentComment = Comment::factory()->create();
        $replies = Comment::factory()->count(3)->create(['parent_id' => $parentComment->id]);

        $this->assertCount(3, $parentComment->replies);
        $this->assertInstanceOf(Comment::class, $parentComment->replies->first());
    }

    /** @test */
    public function it_has_approved_replies()
    {
        $parentComment = Comment::factory()->create();
        
        Comment::factory()->count(2)->create([
            'parent_id' => $parentComment->id,
            'status' => 'approved'
        ]);
        
        Comment::factory()->create([
            'parent_id' => $parentComment->id,
            'status' => 'pending'
        ]);

        $this->assertCount(2, $parentComment->approvedReplies);
    }

    /** @test */
    public function approved_scope_returns_only_approved_comments()
    {
        Comment::factory()->count(3)->create(['status' => 'approved']);
        Comment::factory()->count(2)->create(['status' => 'pending']);
        Comment::factory()->count(1)->create(['status' => 'spam']);

        $approvedComments = Comment::approved()->get();

        $this->assertCount(3, $approvedComments);
        foreach ($approvedComments as $comment) {
            $this->assertEquals('approved', $comment->status);
        }
    }

    /** @test */
    public function pending_scope_returns_only_pending_comments()
    {
        Comment::factory()->count(2)->create(['status' => 'pending']);
        Comment::factory()->count(3)->create(['status' => 'approved']);

        $pendingComments = Comment::pending()->get();

        $this->assertCount(2, $pendingComments);
        foreach ($pendingComments as $comment) {
            $this->assertEquals('pending', $comment->status);
        }
    }

    /** @test */
    public function spam_scope_returns_only_spam_comments()
    {
        Comment::factory()->count(2)->create(['status' => 'spam']);
        Comment::factory()->count(3)->create(['status' => 'approved']);

        $spamComments = Comment::spam()->get();

        $this->assertCount(2, $spamComments);
        foreach ($spamComments as $comment) {
            $this->assertEquals('spam', $comment->status);
        }
    }

    /** @test */
    public function parent_scope_returns_only_parent_comments()
    {
        $parentComments = Comment::factory()->count(3)->create(['parent_id' => null]);
        Comment::factory()->count(2)->create(['parent_id' => $parentComments->first()->id]);

        $parentResults = Comment::parentComments()->get();

        $this->assertCount(3, $parentResults);
        foreach ($parentResults as $comment) {
            $this->assertNull($comment->parent_id);
        }
    }

    /** @test */
    public function replies_scope_returns_only_reply_comments()
    {
        $parentComment = Comment::factory()->create();
        Comment::factory()->count(2)->create(['parent_id' => $parentComment->id]);
        Comment::factory()->count(3)->create(['parent_id' => null]);

        $replies = Comment::replyComments()->get();

        $this->assertCount(2, $replies);
        foreach ($replies as $comment) {
            $this->assertNotNull($comment->parent_id);
        }
    }

    /** @test */
    public function by_post_scope_returns_comments_for_specific_post()
    {
        $otherPost = Post::factory()->create();
        
        Comment::factory()->count(3)->create(['post_id' => $this->post->id]);
        Comment::factory()->count(2)->create(['post_id' => $otherPost->id]);

        $postComments = Comment::byPost($this->post->id)->get();

        $this->assertCount(3, $postComments);
        foreach ($postComments as $comment) {
            $this->assertEquals($this->post->id, $comment->post_id);
        }
    }

    /** @test */
    public function by_user_scope_returns_comments_by_specific_user()
    {
        $otherUser = User::factory()->create();
        
        Comment::factory()->count(3)->create(['user_id' => $this->user->id]);
        Comment::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $userComments = Comment::byUser($this->user->id)->get();

        $this->assertCount(3, $userComments);
        foreach ($userComments as $comment) {
            $this->assertEquals($this->user->id, $comment->user_id);
        }
    }

    /** @test */
    public function is_approved_attribute_returns_correct_value()
    {
        $approvedComment = Comment::factory()->create(['status' => 'approved']);
        $pendingComment = Comment::factory()->create(['status' => 'pending']);

        $this->assertTrue($approvedComment->is_approved);
        $this->assertFalse($pendingComment->is_approved);
    }

    /** @test */
    public function is_pending_attribute_returns_correct_value()
    {
        $pendingComment = Comment::factory()->create(['status' => 'pending']);
        $approvedComment = Comment::factory()->create(['status' => 'approved']);

        $this->assertTrue($pendingComment->is_pending);
        $this->assertFalse($approvedComment->is_pending);
    }

    /** @test */
    public function is_spam_attribute_returns_correct_value()
    {
        $spamComment = Comment::factory()->create(['status' => 'spam']);
        $approvedComment = Comment::factory()->create(['status' => 'approved']);

        $this->assertTrue($spamComment->is_spam);
        $this->assertFalse($approvedComment->is_spam);
    }

    /** @test */
    public function author_attribute_returns_user_name_when_user_exists()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $this->assertEquals($this->user->name, $comment->author);
    }

    /** @test */
    public function author_attribute_returns_author_name_when_no_user()
    {
        $comment = Comment::factory()->create([
            'user_id' => null,
            'author_name' => 'Anonymous User'
        ]);

        $this->assertEquals('Anonymous User', $comment->author);
    }

    /** @test */
    public function depth_attribute_calculates_comment_hierarchy_depth()
    {
        $level1 = Comment::factory()->create();
        $level2 = Comment::factory()->create(['parent_id' => $level1->id]);
        $level3 = Comment::factory()->create(['parent_id' => $level2->id]);

        $this->assertEquals(0, $level1->depth);
        $this->assertEquals(1, $level2->depth);
        $this->assertEquals(2, $level3->depth);
    }

    /** @test */
    public function approve_method_sets_status_to_approved()
    {
        $comment = Comment::factory()->create(['status' => 'pending']);

        $result = $comment->approve();

        $this->assertTrue($result);
        $this->assertEquals('approved', $comment->fresh()->status);
    }

    /** @test */
    public function reject_method_sets_status_to_rejected()
    {
        $comment = Comment::factory()->create(['status' => 'pending']);

        $result = $comment->reject();

        $this->assertTrue($result);
        $this->assertEquals('rejected', $comment->fresh()->status);
    }

    /** @test */
    public function mark_as_spam_method_sets_status_to_spam()
    {
        $comment = Comment::factory()->create(['status' => 'approved']);

        $result = $comment->markAsSpam();

        $this->assertTrue($result);
        $this->assertEquals('spam', $comment->fresh()->status);
    }

    /** @test */
    public function can_be_edited_by_returns_true_for_owner()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($comment->canBeEditedBy($this->user));
    }

    /** @test */
    public function can_be_edited_by_returns_false_for_non_owner()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $this->assertFalse($comment->canBeEditedBy($otherUser));
    }

    /** @test */
    public function can_be_deleted_by_returns_true_for_owner()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($comment->canBeDeletedBy($this->user));
    }

    /** @test */
    public function can_be_deleted_by_returns_false_for_non_owner()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $this->assertFalse($comment->canBeDeletedBy($otherUser));
    }

    /** @test */
    public function is_reply_returns_true_for_reply_comment()
    {
        $parentComment = Comment::factory()->create();
        $replyComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $this->assertTrue($replyComment->isReply());
        $this->assertFalse($parentComment->isReply());
    }

    /** @test */
    public function has_replies_returns_true_when_comment_has_replies()
    {
        $parentComment = Comment::factory()->create();
        Comment::factory()->create(['parent_id' => $parentComment->id]);

        $commentWithoutReplies = Comment::factory()->create();

        $this->assertTrue($parentComment->hasReplies());
        $this->assertFalse($commentWithoutReplies->hasReplies());
    }

    /** @test */
    public function get_hierarchy_returns_comment_hierarchy()
    {
        $level1 = Comment::factory()->create();
        $level2 = Comment::factory()->create(['parent_id' => $level1->id]);
        $level3 = Comment::factory()->create(['parent_id' => $level2->id]);

        $hierarchy = $level3->getHierarchy();

        $this->assertCount(2, $hierarchy);
        $this->assertEquals($level1->id, $hierarchy[0]->id);
        $this->assertEquals($level2->id, $hierarchy[1]->id);
    }

    /** @test */
    public function it_soft_deletes()
    {
        $comment = Comment::factory()->create();
        $commentId = $comment->id;

        $comment->delete();

        $this->assertSoftDeleted('comments', ['id' => $commentId]);
        $this->assertNull(Comment::find($commentId));
        $this->assertNotNull(Comment::withTrashed()->find($commentId));
    }
}