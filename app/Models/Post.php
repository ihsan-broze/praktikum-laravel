<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'views',
        'allow_comments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'views' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug) && !empty($post->title)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::updating(function ($post) {
            // Regenerate slug if title changed but slug wasn't manually changed
            if ($post->isDirty('title') && !$post->isDirty('slug')) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the post.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get only approved comments for the post.
     */
    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    /**
     * Get only pending comments for the post.
     */
    public function pendingComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'pending');
    }

    /**
     * Get only spam comments for the post.
     */
    public function spamComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'spam');
    }

    /**
     * Get only rejected comments for the post.
     */
    public function rejectedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'rejected');
    }

    /**
     * Get approved parent comments (top-level comments only).
     */
    public function approvedParentComments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->where('status', 'approved')
            ->whereNull('parent_id');
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at');
    }

    /**
     * Scope a query to only include draft posts.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to order posts by most recent first.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to only include posts by a specific author.
     */
    public function scopeByAuthor($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to search posts by title and content.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /**
     * Check if the post is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at !== null;
    }

    /**
     * Check if the post is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Get the count of approved comments.
     */
    public function getApprovedCommentsCountAttribute(): int
    {
        return $this->approvedComments()->count();
    }

    /**
     * Get the count of pending comments.
     */
    public function getPendingCommentsCountAttribute(): int
    {
        return $this->pendingComments()->count();
    }

    /**
     * Check if the post has any comments.
     */
    public function hasComments(): bool
    {
        return $this->comments()->exists();
    }

    /**
     * Check if the post has any approved comments.
     */
    public function hasApprovedComments(): bool
    {
        return $this->approvedComments()->exists();
    }

    /**
     * Get excerpt of the post content.
     */
    public function getExcerpt($length = 150)
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags($this->content), $length);
    }

    /**
     * Calculate estimated reading time for the post.
     */
    public function getReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $averageWordsPerMinute = 200;
        
        return (int) max(1, ceil($wordCount / $averageWordsPerMinute));
    }

    /**
     * Increment the view count for the post.
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Check if comments are allowed on this post.
     */
    public function isCommentable()
    {
        return $this->allow_comments;
    }

    /**
     * Get related posts from the same category.
     */
    public function getRelatedPosts($limit = 5)
    {
        return static::where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->where('status', 'published')
            ->limit($limit)
            ->get();
    }

    
}