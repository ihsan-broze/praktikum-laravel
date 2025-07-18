<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'status',
        'profile_qr',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Default values for attributes
     */
    protected $attributes = [
        'role' => 'user',
        'status' => 'active',
    ];

    /**
     * Boot method untuk auto-generate QR code
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->generateQrCode();
        });

        static::updated(function ($user) {
            // Regenerate QR jika data penting berubah
            if ($user->isDirty(['name', 'email', 'role'])) {
                $user->generateQrCode();
            }
        });

        static::deleted(function ($user) {
            $user->deleteQrCode();
        });
    }

    /**
     * Generate QR Code untuk user
     */
    public function generateQrCode()
    {
        try {
            // Data yang akan di-encode dalam QR
            $qrData = [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'generated_at' => now()->toISOString()
            ];

            // Generate QR Code
            $qrCode = QrCode::format('png')
                           ->size(300)
                           ->margin(2)
                           ->generate(json_encode($qrData));

            // Nama file
            $fileName = 'qr_codes/user_' . $this->id . '_' . time() . '.png';

            // Simpan ke storage
            Storage::disk('public')->put($fileName, $qrCode);

            // Hapus QR lama jika ada
            if ($this->profile_qr && Storage::disk('public')->exists($this->profile_qr)) {
                Storage::disk('public')->delete($this->profile_qr);
            }

            // Update database
            $this->update(['profile_qr' => $fileName]);

        } catch (\Exception $e) {
            \Log::error('Error generating QR code for user ' . $this->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Delete QR Code file
     */
    public function deleteQrCode()
    {
        if ($this->profile_qr && Storage::disk('public')->exists($this->profile_qr)) {
            Storage::disk('public')->delete($this->profile_qr);
        }
    }

    /**
     * Get QR Code URL
     */
    public function getQrCodeUrlAttribute()
    {
        if ($this->profile_qr) {
            return Storage::disk('public')->url($this->profile_qr);
        }
        return null;
    }

    /**
     * Get QR Code Path
     */
    public function getQrCodePathAttribute()
    {
        if ($this->profile_qr) {
            return storage_path('app/public/' . $this->profile_qr);
        }
        return null;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute()
    {
        $roles = [
            'admin' => 'Administrator',
            'user' => 'User'
        ];

        return $roles[$this->role] ?? 'Unknown';
    }

    /**
     * Get status label with HTML badge
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? 
            '<span class="badge bg-success">Active</span>' : 
            '<span class="badge bg-danger">Inactive</span>';
    }

    /**
     * Get role badge HTML
     */
    public function getRoleBadgeAttribute()
    {
        return $this->role === 'admin' ? 
            '<span class="badge bg-danger">Administrator</span>' : 
            '<span class="badge bg-primary">User</span>';
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for specific role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for regular users
     */
    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Get QR data as array
     */
    public function getQrDataAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'qr_generated_at' => $this->updated_at->toISOString()
        ];
    }

    /**
     * Force regenerate QR code
     */
    public function regenerateQrCode()
    {
        $this->generateQrCode();
        return $this;
    }

    /**
     * Download QR Code
     */
    public function downloadQrCode()
    {
        if (!$this->profile_qr || !Storage::disk('public')->exists($this->profile_qr)) {
            $this->generateQrCode();
        }

        return response()->download($this->qr_code_path, 'qr_' . $this->name . '.png');
    }
}