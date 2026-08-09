<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TambahanLaukPauk extends Model
{
    use HasFactory;

    protected $table = 'tambahan_lauk_pauk';

    protected $fillable = [
        'menu_id',
        'nama',
        'harga',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function detailPesanans()
    {
        return $this->belongsToMany(DetailPesanan::class, 'detail_tambahan_lauk_pauk', 'tambahan_lauk_pauk_id', 'detail_pesanan_id');
    }
}
