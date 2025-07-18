<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Existing relationship: User has many courses (created by this user)
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    /**
     * Existing relationship: User has many transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * New relationship: User has many posts (for blog)
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * New relationship: User has many comments (for blog)
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'preferences',
        'role',
        'profile_qr', // Added for QR code filename
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array',
    ];

    /**
     * The attributes that should have default values.
     * REMOVED preferences from here since we handle it in boot()
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'user',
    ];

    /**
     * Get the avatar URL with caching.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return Cache::remember("user.{$this->id}.avatar_url", 3600, function () {
            if ($this->avatar) {
                return asset('storage/' . $this->avatar);
            }
            return null;
        });
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is moderator
     */
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user has specific role(s)
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        
        return false;
    }

    /**
     * Check if user has verified email (for comment auto-approval)
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get user role display name
     */
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'moderator' => 'Moderator',
            'user' => 'User',
            default => ucfirst($this->role)
        };
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for moderator users
     */
    public function scopeModerators($query)
    {
        return $query->where('role', 'moderator');
    }

    /**
     * Scope for regular users
     */
    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope for active users (not soft deleted)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Get QR code URL for user profile
     */
    public function getQrCodeUrl(): ?string
    {
        return $this->profile_qr ? asset('storage/qr_codes/' . $this->profile_qr) : null;
    }

    /**
     * Check if user has QR code
     */
    public function hasQrCode(): bool
    {
        return !empty($this->profile_qr);
    }

    /**
     * Generate QR code for user profile
     */
    public function generateProfileQR(): void
    {
        try {
            // Create QR code content (you can customize this)
            $qrContent = "User ID: {$this->id}\nName: {$this->name}\nEmail: {$this->email}";
            
            // Generate unique filename
            $filename = 'user_' . $this->id . '_' . time() . '.png';
            
            // Create QR code
            $qrCode = QrCode::format('png')
                ->size(300)
                ->generate($qrContent);
            
            // Save QR code to storage
            Storage::disk('public')->put('qr_codes/' . $filename, $qrCode);
            
            // Update user with QR filename (without triggering observer again)
            $this->updateQuietly(['profile_qr' => $filename]);
            
        } catch (\Exception $e) {
            // Handle error silently or log it
            \Log::error('Failed to generate QR code for user ' . $this->id . ': ' . $e->getMessage());
        }
    }
    
    public function getPreference($key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return data_get($preferences, $key, $default);
    }

    // Helper method to set preference value
    public function setPreference($key, $value)
    {
        $preferences = $this->preferences ?? [];
        data_set($preferences, $key, $value);
        $this->preferences = $preferences;
        return $this;
    }

    public function setAvatarAttribute($value)
    {
        // This might be defaulting to 'avatars/default.jpg'
        $this->attributes['avatar'] = $value ?: 'avatars/default.jpg';
    }

    /**
     * Get cached user by ID
     */
    public static function findCached($id)
    {
        return Cache::remember("user.{$id}", 3600, function () use ($id) {
            return static::with(['courses', 'posts', 'comments'])->find($id);
        });
    }

    /**
     * Get cached user by email
     */
    public static function findByEmailCached($email)
    {
        return Cache::remember("user.email.{$email}", 3600, function () use ($email) {
            return static::where('email', $email)->first();
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set default preferences if null when creating
        static::creating(function ($user) {
            if (empty($user->preferences)) {
                $user->preferences = [
                    'theme' => 'light',
                    'language' => 'en',
                    'notifications' => [
                        'email' => true,
                        'push' => true,
                        'sms' => false,
                    ],
                ];
            }
            
            // Set default role if not set
            if (empty($user->role)) {
                $user->role = 'user';
            }
        });
    }
}
