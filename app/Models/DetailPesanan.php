<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanan';

    protected $fillable = [
        'pesanan_id',
        'menu_id',
        'minuman_id',
        'porsi',
        'subtotal',
        'tanggal_pengiriman',
        'is_rescheduled'
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function tambahanLaukPauk(): BelongsToMany
    {
        return $this->belongsToMany(TambahanLaukPauk::class, 'detail_tambahan_lauk_pauk', 'detail_pesanan_id', 'tambahan_lauk_pauk_id');
    }

    public function minuman(): BelongsTo
    {
        return $this->belongsTo(Minuman::class);
    }
}
