<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Request $request, Post $post)
    {
        $comments = $post->comments()
            ->with(['user'])
            ->where('status', 'approved')
            ->whereNull('parent_id') // Only parent comments, not replies
            ->latest()
            ->paginate(10);

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'comments' => $comments
            ]);
        }

        // Return view for web requests
        return view('comments.index', compact('comments', 'post'));
    }

    public function getPostComments(Request $request, Post $post)
    {
        $comments = $post->comments()
            ->with(['user'])
            ->where('status', 'approved')
            ->whereNull('parent_id') // Only parent comments
            ->latest()
            ->paginate(10);

        return response()->json([
            'comments' => $comments
        ]);
    }

    // Method untuk halaman moderasi
    public function moderate()
    {
        // Manual role check untuk memberikan 403 yang benar
        if (!auth()->user() || !in_array(auth()->user()->role, ['moderator', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $pendingComments = Comment::pending()
            ->with(['post', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $spamComments = Comment::spam()
            ->with(['post', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('comments.moderate', compact('pendingComments', 'spamComments'));
    }

    // Method untuk approve comment
    public function approve(Comment $comment)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['moderator', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $comment->approve();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Comment approved successfully!',
                'comment' => $comment->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'Comment approved successfully!');
    }

    // Method untuk reject comment
    public function reject(Comment $comment)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['moderator', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $comment->reject();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Comment rejected successfully!',
                'comment' => $comment->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'Comment rejected successfully!');
    }

    // Method untuk mark as spam
    public function spam(Comment $comment)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['moderator', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $comment->markAsSpam();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Comment marked as spam!',
                'comment' => $comment->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'Comment marked as spam!');
    }

    // Method untuk bulk moderation
    public function bulkModerate(Request $request)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['moderator', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
            'action' => 'required|in:approve,reject,spam,delete'
        ]);

        $comments = Comment::whereIn('id', $request->comment_ids)->get();

        foreach ($comments as $comment) {
            switch ($request->action) {
                case 'approve':
                    $comment->approve();
                    break;
                case 'reject':
                    $comment->reject();
                    break;
                case 'spam':
                    $comment->markAsSpam();
                    break;
                case 'delete':
                    $comment->delete();
                    break;
            }
        }

        $message = $request->action === 'delete' ? 'deleted' : $request->action . 'd';
        return redirect()->back()->with('success', "Comments {$message} successfully!");
    }

    // Method untuk mendapatkan replies
    public function replies(Comment $comment)
    {
        $replies = $comment->children()
            ->approved()
            ->with(['user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'replies' => $replies,
            'total' => $replies->count()
        ]);
    }

    // Standard CRUD methods
    public function indexAll()
    {
        // Index untuk semua comments (route comments.index)
        $comments = Comment::approved()
            ->with(['user', 'post'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('comments.index', compact('comments'));
    }
    

    public function store(Request $request)
    {
        $rules = [
            'content' => 'required|string|max:1000',
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
        ];

        // Jika user tidak login, require author info
        if (!auth()->check()) {
            $rules['author_name'] = 'required|string|max:255';
            $rules['author_email'] = 'required|email|max:255';
        }

        $request->validate($rules);

        $comment = new Comment($request->only(['content', 'post_id', 'parent_id', 'author_name', 'author_email']));
        
        if (auth()->check()) {
            $comment->user_id = auth()->id();
            $comment->author_name = auth()->user()->name;
            $comment->author_email = auth()->user()->email;
            $comment->status = 'approved'; // Auto-approve for registered users
        } else {
            $comment->status = 'pending'; // Require moderation for guests
        }

        $comment->ip_address = $request->ip();
        $comment->user_agent = $request->userAgent();
        $comment->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Comment created successfully!',
                'comment' => $comment->load('user'),
                'status' => $comment->status
            ], 201);
        }

        return redirect()->back()->with('success', 'Comment created successfully!');
    }

    public function show(Comment $comment)
    {
        return view('comments.show', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        if (!$comment->canBeEditedBy(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment->update([
            'content' => $request->content,
            'status' => 'pending' // Require re-moderation after edit
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Comment updated successfully!',
                'comment' => $comment
            ]);
        }

        return redirect()->back()->with('success', 'Comment updated successfully!');
    }

    public function destroy(Comment $comment)
    {
        if (!$comment->canBeDeletedBy(auth()->user())) {
            abort(403);
        }

        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Comment deleted successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }
}