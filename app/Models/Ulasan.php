<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Ulasan menyimpan ulasan atau testimoni pelanggan.
 * Ulasan ini dapat ditampilkan di halaman beranda sebagai bentuk kredibilitas layanan.
 */
class Ulasan extends Model
{
    protected $table = 'ulasan';

    protected $fillable = ['user_id', 'komentar'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
