<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Query LAMBAT - Contoh yang perlu dioptimasi
    public function index()
    {
        // Query N+1 Problem
        $users = User::all();
        
        foreach ($users as $user) {
            // Setiap iterasi akan membuat query baru
            echo $user->posts->count();
        }
        
        return view('users.index', compact('users'));
    }
    
    // Query LAMBAT - Tanpa indexing
    public function slowSearch(Request $request)
    {
        $users = User::where('email', 'like', '%' . $request->search . '%')
                    ->where('status', 'active')
                    ->get();
        
        return view('users.search', compact('users'));
    }
    
    // Query OPTIMIZED - Setelah optimasi
    public function optimizedIndex()
    {
        // Menggunakan eager loading untuk menghindari N+1
        $users = User::with(['posts' => function($query) {
            $query->select('id', 'user_id', 'title');
        }])->get();
        
        return view('users.index', compact('users'));
    }
    
    // Query OPTIMIZED - Dengan caching
    public function cachedUsers()
    {
        $users = cache()->remember('users_with_posts', 3600, function () {
            return User::with('posts:id,user_id,title,created_at')
                      ->select('id', 'name', 'email', 'created_at')
                      ->get();
        });
        
        return view('users.index', compact('users'));
    }
}