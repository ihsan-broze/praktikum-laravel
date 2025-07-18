<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'author_name',
        'author_email',
        'status',
        'post_id',
        'user_id',
        'parent_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Accessors untuk kompatibilitas dengan test
    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsSpamAttribute(): bool
    {
        return $this->status === 'spam';
    }

    public function getAuthorAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->author_name ?? 'Anonymous');
    }

    public function getDepthAttribute(): int
    {
        $depth = 0;
        $current = $this;
        
        while ($current->parent) {
            $depth++;
            $current = $current->parent;
            
            // Prevent infinite loop
            if ($depth > 10) break;
        }
        
        return $depth;
    }

    // Relationships
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Alias untuk replies (untuk backward compatibility dengan view lama)
    public function replies(): HasMany
    {
        return $this->children();
    }

    // ✅ TAMBAHAN: Relationship untuk approved replies
    public function approvedReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->where('status', 'approved');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeSpam($query)
    {
        return $query->where('status', 'spam');
    }

    // ✅ TAMBAHAN: Missing scopes yang dibutuhkan test
    public function scopeParentComments($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplyComments($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeByPost($query, $postId)
    {
        return $query->where('post_id', $postId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isSpam(): bool
    {
        return $this->status === 'spam';
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function hasReplies(): bool
    {
        return $this->children()->count() > 0;
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function getHierarchy(): array
    {
        $hierarchy = [];
        $current = $this->parent;
        
        while ($current) {
            array_unshift($hierarchy, $current);
            $current = $current->parent;
            
            // Prevent infinite loop
            if (count($hierarchy) > 10) break;
        }
        
        return $hierarchy;
    }

    public function approve(): bool
    {
        return $this->update(['status' => 'approved']);
    }

    public function reject(): bool
    {
        return $this->update(['status' => 'rejected']);
    }

    public function markAsSpam(): bool
    {
        return $this->update(['status' => 'spam']);
    }
}