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
            default => 'landing',
        };
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

    /**
     * Menghitung total jumlah item di keranjang.
     * Setiap produk Katering Harian dihitung sebagai 1 item (cart_group_id null).
     * Setiap Paket Event / Custom Menu dihitung sebagai 1 item (distinct cart_group_id).
     */
    public function cartItemsCount(): int
    {
        $dailyCount = $this->carts()->whereNull('cart_group_id')->count();
        $eventCount = $this->carts()->whereNotNull('cart_group_id')->distinct()->count('cart_group_id');
        return $dailyCount + $eventCount;
    }
}
