<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Review menyimpan ulasan atau testimoni pelanggan terhadap pesanannya.
 * Ulasan ini dapat ditampilkan di halaman beranda sebagai bentuk kredibilitas layanan.
 */
class Review extends Model
{
    protected $fillable = ['user_id', 'order_id', 'comment'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
