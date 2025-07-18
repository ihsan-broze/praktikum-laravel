<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine if the user can view the comment.
     */
    public function view(?User $user, Comment $comment): bool
    {
        // Anyone can view approved comments
        if ($comment->status === 'approved') {
            return true;
        }

        // Only authenticated users can view their own non-approved comments
        if ($user && $user->id === $comment->user_id) {
            return true;
        }

        // Moderators and admins can view any comment
        if ($user && in_array($user->role, ['moderator', 'admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Users can only update their own comments
        return $user->id === $comment->user_id;
    }

    /**
     * Determine if the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Users can delete their own comments
        if ($user->id === $comment->user_id) {
            return true;
        }

        // Admins and moderators can delete any comment
        return in_array($user->role, ['moderator', 'admin']);
    }

    /**
     * Determine if the user can moderate comments.
     */
    public function moderate(User $user): bool
    {
        return in_array($user->role, ['moderator', 'admin']);
    }

    /**
     * Determine if the user can view the moderation interface.
     */
    public function viewModeration(User $user): bool
    {
        return in_array($user->role, ['moderator', 'admin']);
    }
}