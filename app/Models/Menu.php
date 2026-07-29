<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = [
        'layanan_id',
        'nama_menu',
        'deskripsi',
        'harga',
        'status',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class);
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
