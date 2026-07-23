<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    public $timestamps = false;

    protected $fillable = [
        'pesanan_id',
        'menu_harian_id',
        'jadwal_menu_id',
        'extra_harian_id',
        'menu_acara_id',
        'extra_acara_id',
        'item_name',
        'jumlah',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function menuHarian(): BelongsTo
    {
        return $this->belongsTo(MenuHarian::class, 'menu_harian_id');
    }

    public function jadwalMenu(): BelongsTo
    {
        return $this->belongsTo(JadwalMenu::class, 'jadwal_menu_id');
    }

    public function extraHarian(): BelongsTo
    {
        return $this->belongsTo(ExtraHarian::class, 'extra_harian_id');
    }

    public function menuAcara(): BelongsTo
    {
        return $this->belongsTo(MenuAcara::class, 'menu_acara_id');
    }

    public function extraAcara(): BelongsTo
    {
        return $this->belongsTo(ExtraAcara::class, 'extra_acara_id');
    }
}
