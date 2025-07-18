<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika user tidak login, return 403 langsung (tidak redirect)
        if (!auth()->check()) {
            abort(403, 'Authentication required.');
        }

        $userRole = auth()->user()->role;
        
        // Jika role user tidak sesuai, return 403 langsung (tidak redirect)
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}