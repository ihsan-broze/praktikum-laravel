<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserCacheService
{
    protected $cacheTime = 3600; // 1 jam
    
    public function getAllUsers()
    {
        return Cache::remember('users.all', $this->cacheTime, function () {
            return User::with('posts:id,user_id,title')
                      ->select('id', 'name', 'email', 'created_at')
                      ->get();
        });
    }
    
    public function getUserById($id)
    {
        $cacheKey = "user.{$id}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($id) {
            return User::with(['posts', 'profile'])
                      ->findOrFail($id);
        });
    }
    
    public function clearUserCache($userId = null)
    {
        if ($userId) {
            Cache::forget("user.{$userId}");
        } else {
            Cache::forget('users.all');
        }
    }
    
    public function getUserStats()
    {
        return Cache::remember('user.stats', $this->cacheTime, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
            ];
        });
    }
}