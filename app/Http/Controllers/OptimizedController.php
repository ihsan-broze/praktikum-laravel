<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;

class OptimizedController extends Controller
{
    // SEBELUM: N+1 Problem
    public function badExample()
    {
        $users = User::all(); // 1 query
        
        foreach ($users as $user) {
            // N queries tambahan (1 untuk setiap user)
            $user->posts; 
        }
    }
    
    // SESUDAH: Eager Loading
    public function goodExample()
    {
        // Hanya 2 queries total
        $users = User::with('posts')->get();
        
        foreach ($users as $user) {
            // Tidak ada query tambahan
            $user->posts;
        }
    }
    
    // Optimasi dengan Select Specific Columns
    public function optimizedWithSelect()
    {
        $users = User::select('id', 'name', 'email')
                    ->with(['posts' => function($query) {
                        $query->select('id', 'user_id', 'title', 'created_at');
                    }])
                    ->get();
        
        return response()->json($users);
    }
    
    // Optimasi dengan Lazy Eager Loading
    public function lazyEagerLoading()
    {
        $users = User::all();
        
        // Load relasi hanya ketika diperlukan
        $users->load('posts:id,user_id,title');
        
        return response()->json($users);
    }
}