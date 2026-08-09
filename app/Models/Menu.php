<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = [
        'tipe_layanan',
        'nama_menu',
        'deskripsi',
        'gambar',
        'harga',
        'status',
    ];

    public function tambahanLaukPauk()
    {
        return $this->hasMany(TambahanLaukPauk::class);
    }

    public function pesanan()
    {
        return $this->belongsToMany(Pesanan::class, 'detail_pesanan');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalMenu::class);
    }
}
