<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Debug: Log login attempt
        Log::info('Login attempt', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        // Get user for debugging and role checking
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            Log::warning('User not found', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        Log::info('User found', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role ?? 'no role'
        ]);

        // Check if account is active (if you have status field)
        if (isset($user->status) && $user->status === 'inactive') {
            Log::warning('Inactive account login attempt', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact administrator.',
            ])->onlyInput('email');
        }

        // Debug: Check password manually
        $passwordCheck = Hash::check($request->password, $user->password);
        Log::info('Password check', [
            'email' => $request->email,
            'password_match' => $passwordCheck
        ]);

        try {
            // Use Laravel's built-in authentication
            $request->authenticate();
            Log::info('Authentication successful', ['email' => $request->email]);
        } catch (\Exception $e) {
            Log::error('Authentication failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        $request->session()->regenerate();

        // Redirect based on user role
        $authenticatedUser = Auth::user();
        
        Log::info('Redirecting user', [
            'user_id' => $authenticatedUser->id,
            'role' => $authenticatedUser->role
        ]);

        if ($authenticatedUser->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        // ✅ PERUBAHAN: Gunakan 'dashboard' bukan 'user.dashboard'
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Alternative manual authentication method
     * Uncomment this method and comment out the above store() method if you prefer manual authentication
     */
    /*
    public function storeManual(LoginRequest $request): RedirectResponse
    {
        // Manual authentication approach
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            Log::warning('User not found', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'User not found.',
            ])->onlyInput('email');
        }
        
        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Password mismatch', ['email' => $request->email]);
            return back()->withErrors([
                'password' => 'Password incorrect.',
            ])->onlyInput('email');
        }
        
        if (isset($user->status) && $user->status === 'inactive') {
            return back()->withErrors([
                'email' => 'Account is inactive.',
            ])->onlyInput('email');
        }
        
        // Login user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        
        Log::info('Manual login successful', ['user_id' => $user->id]);
        
        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return redirect()->intended(route('user.dashboard'));
    }
    */

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

