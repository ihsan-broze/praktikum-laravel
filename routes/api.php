<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Course;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Hash;

// Add this to your routes/api.php for general comment listing
Route::get('/comments', [CommentController::class, 'indexAll'])->name('api.comments.index');
Route::post('/comments', [CommentController::class, 'store'])->name('api.comments.store');
Route::get('/comments/{comment}/replies', [CommentController::class, 'replies'])->name('api.comments.replies');

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/comments/{comment}', [CommentController::class, 'show'])->name('api.comments.show');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('api.comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('api.comments.destroy');
    
    // Moderation routes (require moderator role)
    Route::middleware('can:moderate-comments')->group(function () {
        Route::get('/moderator/comments', [CommentController::class, 'moderate'])->name('api.moderator.comments.moderate');
        Route::patch('/moderator/comments/{comment}/approve', [CommentController::class, 'approve'])->name('api.moderator.comments.approve');
        Route::patch('/moderator/comments/{comment}/reject', [CommentController::class, 'reject'])->name('api.moderator.comments.reject');
        Route::patch('/moderator/comments/{comment}/spam', [CommentController::class, 'spam'])->name('api.moderator.comments.spam');
        Route::post('/moderator/comments/bulk-moderate', [CommentController::class, 'bulkModerate'])->name('api.moderator.comments.bulk-moderate');
    });
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    return response()->json([
        'token' => $user->createToken('api-token')->plainTextToken
    ]);
});

Route::get('/courses', function () {
    return Course::all();
});
