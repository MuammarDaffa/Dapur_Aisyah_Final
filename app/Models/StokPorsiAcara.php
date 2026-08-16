<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokPorsiAcara extends Model
{
    protected $table = 'stok_porsi_acara';

    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'stok',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}
