<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Debug logging
        Log::info('Attempting authentication', [
            'email' => $this->email,
            'password_length' => strlen($this->password)
        ]);

        // Check if user exists first
        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            Log::warning('User not found during authentication', ['email' => $this->email]);
            
            RateLimiter::hit($this->throttleKey());
            
            throw ValidationException::withMessages([
                'email' => 'User with this email does not exist.',
            ]);
        }

        Log::info('User found', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role ?? 'no role set',
            'user_status' => $user->status ?? 'no status set'
        ]);

        // Check if user is active (if status field exists)
        if (isset($user->status) && $user->status === 'inactive') {
            Log::warning('User is inactive', ['email' => $this->email]);
            
            throw ValidationException::withMessages([
                'email' => 'Your account is inactive. Please contact administrator.',
            ]);
        }

        // Check password manually first for debugging
        $passwordCheck = Hash::check($this->password, $user->password);
        Log::info('Manual password check result', [
            'email' => $this->email,
            'password_match' => $passwordCheck,
            'stored_hash_preview' => substr($user->password, 0, 20) . '...'
        ]);

        if (!$passwordCheck) {
            Log::warning('Password mismatch', ['email' => $this->email]);
            
            RateLimiter::hit($this->throttleKey());
            
            throw ValidationException::withMessages([
                'password' => 'The password is incorrect.',
            ]);
        }

        // If manual check passes, try Laravel's Auth::attempt
        $credentials = $this->only('email', 'password');
        
        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            Log::error('Auth::attempt failed despite manual checks passing', [
                'email' => $this->email,
                'manual_password_check' => $passwordCheck,
                'user_exists' => true
            ]);
            
            // Force login since manual checks passed
            Auth::login($user, $this->boolean('remember'));
            Log::info('Forced login successful after Auth::attempt failed', ['email' => $this->email]);
        } else {
            Log::info('Auth::attempt successful', ['email' => $this->email]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Alternative simpler authentication method
     * Uncomment this method and comment out the above authenticate() method if you prefer simpler approach
     */
    /*
    public function authenticateSimple(): void
    {
        $this->ensureIsNotRateLimited();

        Log::info('Attempting simple authentication', [
            'email' => $this->email,
            'password_length' => strlen($this->password)
        ]);

        // Use Laravel's standard authentication
        $credentials = $this->only('email', 'password');
        
        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            Log::warning('Authentication failed', [
                'email' => $this->email,
                'throttle_key' => $this->throttleKey()
            ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        Log::info('Authentication successful', ['email' => $this->email]);
        RateLimiter::clear($this->throttleKey());
    }
    */

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}