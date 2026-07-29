<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesananMinuman extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanan_minumans';

    protected $fillable = [
        'pesanan_id',
        'minuman_id',
        'jumlah',
        'subtotal',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function minuman()
    {
        return $this->belongsTo(Minuman::class);
    }
}
