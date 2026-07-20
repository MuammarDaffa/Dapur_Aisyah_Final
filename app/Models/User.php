<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model User merepresentasikan data pengguna sistem (Pelanggan, Admin, maupun Pemilik).
 * Model ini mengelola proses autentikasi (login/register) dan otorisasi role.
 */
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

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function ulasan(): HasMany
    {
        return $this->hasMany(Ulasan::class);
    }

    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class);
    }

    /**
     * Menghitung total jumlah item di keranjang.
     * Setiap produk Katering Harian dihitung sebagai 1 item (cart_group_id null).
     * Setiap Paket Event / Custom Menu dihitung sebagai 1 item (distinct cart_group_id).
     */
    /**
         * Menghitung total entitas yang masuk ke dalam keranjang belanja pelanggan.
         * Alur bisnis:
         * - Menu Katering Harian dihitung masing-masing sebagai 1 item.
         * - Katering Event (paket/prasmanan) dihitung sebagai 1 kesatuan grup, 
         *   meskipun terdiri dari banyak sub-menu/ekstra.
         * @return int
         */
    public function cartItemsCount(): int
    {
        $dailyCount = $this->keranjang()->whereNull('cart_group_id')->count();
        $eventCount = $this->keranjang()->whereNotNull('cart_group_id')->distinct()->count('cart_group_id');
        return $dailyCount + $eventCount;
    }
}
