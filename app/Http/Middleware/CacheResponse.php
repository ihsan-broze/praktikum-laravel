<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheResponse
{
    public function handle(Request $request, Closure $next, $duration = 3600)
    {
        // Hanya cache untuk GET request
        if ($request->method() !== 'GET') {
            return $next($request);
        }
        
        $cacheKey = 'response.' . md5($request->fullUrl());
        
        // Cek apakah response sudah di-cache
        if (Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey))
                ->header('X-Cache-Status', 'HIT');
        }
        
        $response = $next($request);
        
        // Cache response jika status code 200
        if ($response->status() === 200) {
            Cache::put($cacheKey, $response->getContent(), $duration);
        }
        
        return $response->header('X-Cache-Status', 'MISS');
    }
}