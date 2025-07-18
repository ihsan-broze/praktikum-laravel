<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminDashboardController;

// Landing page
Route::get('/', function () {
    return view('welcome');
});

// ✅ DASHBOARD ROUTE - PALING PENTING, TARUH DI ATAS
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Home redirect logic
Route::get('/home', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard'); // ✅ Gunakan 'dashboard'
    }
    return redirect('/');
})->middleware('auth')->name('home');

// ==========================
// COMMENT ROUTES
// ==========================
Route::get('/comments', [CommentController::class, 'indexAll'])->name('comments.index');
Route::get('/comments/{comment}', [CommentController::class, 'show'])->name('comments.show');
Route::get('/comments/{comment}/replies', [CommentController::class, 'replies'])->name('comments.replies');
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');

Route::middleware(['auth'])->group(function () {
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// ==========================
// POSTS ROUTES
// ==========================
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my-posts');
});

Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/api/posts/{post}/comments', [CommentController::class, 'index'])->name('posts.comments');

Route::middleware(['auth'])->group(function () {
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::patch('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::patch('/posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('posts.unpublish');
});

// ==========================
// ADMIN ROUTES
// ==========================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/realtime', [AdminDashboardController::class, 'getRealtimeData'])->name('dashboard.realtime');
    
    Route::resource('courses', CourseController::class)->names([
        'index' => 'courses.index',
        'create' => 'courses.create',
        'store' => 'courses.store',
        'show' => 'courses.show',
        'edit' => 'courses.edit',
        'update' => 'courses.update',
        'destroy' => 'courses.destroy',
    ]);
    
    Route::resource('users', AdminUserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);

    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateUserRole'])->name('users.update-role');
    Route::post('/users/{user}/generate-qr', [AdminUserController::class, 'generateUserQR'])->name('users.generate-qr');
    Route::get('/qr-codes', [AdminUserController::class, 'userQRCodes'])->name('qr-codes');
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');

    Route::get('/comments/moderate', [CommentController::class, 'moderate'])->name('comments.moderate');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');
    Route::patch('/comments/{comment}/spam', [CommentController::class, 'spam'])->name('comments.spam');
    Route::post('/comments/bulk-moderate', [CommentController::class, 'bulkModerate'])->name('comments.bulk-moderate');
});

// ==========================
// MODERATOR ROUTES
// ==========================
Route::middleware(['auth'])->group(function () {
    Route::prefix('moderator')->name('moderator.')->group(function () {
        Route::get('/comments/moderate', [CommentController::class, 'moderate'])->name('comments.moderate');
        Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::patch('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');
        Route::patch('/comments/{comment}/spam', [CommentController::class, 'spam'])->name('comments.spam');
        Route::post('/comments/bulk-moderate', [CommentController::class, 'bulkModerate'])->name('comments.bulk-moderate');
    });
});

// ==========================
// SHARED AUTHENTICATED ROUTES
// ==========================
Route::middleware('auth')->group(function () {
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendation.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::patch('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
});

// ==========================
// Transaction API
// ==========================
Route::middleware('auth:sanctum')->post('/transactions', function (Request $request) {
    $request->validate([
        'course_id' => 'required|exists:courses,id',
    ]);

    return Transaction::create([
        'user_id' => $request->user()->id,
        'course_id' => $request->course_id,
    ]);
})->name('transactions.store');

// Auth routes
require __DIR__.'/auth.php';