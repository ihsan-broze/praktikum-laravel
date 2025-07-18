<?php
    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Symfony\Component\HttpFoundation\Response;

    class RoleMiddleware
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next, string $role): Response
        {
            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            // Check if user has the required role
            if ($user->role !== $role) {
                // Redirect based on user's actual role
                if ($user->role === 'admin') {
                    return redirect('/admin/dashboard')->with('error', 'Access denied.');
                } else {
                    return redirect('/dashboard')->with('error', 'Access denied.');
                }
            }

            return $next($request);
        }
    }