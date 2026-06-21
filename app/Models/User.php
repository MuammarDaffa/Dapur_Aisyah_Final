<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'old_phone',
        'email',
        'password',
        'role',
        'status_suspend',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // === Role Checks ===

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function getDashboardRouteName(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'owner' => 'owner.dashboard',
            default => 'customer.dashboard',
        };
    }

    // === Suspend Status Checks ===

    public function isSuspended(): bool
    {
        return $this->status_suspend === 'suspended';
    }

    public function isPendingVerification(): bool
    {
        return $this->status_suspend === 'pending_verification';
    }

    /**
     * Apakah user sedang diblokir (suspended ATAU pending_verification)?
     */
    public function isBlocked(): bool
    {
        return in_array($this->status_suspend, ['suspended', 'pending_verification']);
    }

    // === Relationships ===

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
