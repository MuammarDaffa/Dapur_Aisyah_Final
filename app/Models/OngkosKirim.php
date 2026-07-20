<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model OngkosKirim menyimpan konfigurasi biaya pengiriman (ongkir).
 * Biaya ini dikelompokkan berdasarkan wilayah Kecamatan (Kecamatan) tujuan pengiriman.
 */
class OngkosKirim extends Model
{
    protected $table = 'ongkos_kirim';

    protected $fillable = ['kecamatan_id', 'cost', 'catatan'];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
        ];
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Mendapatkan ongkos kirim berdasarkan kecamatan, atau default Rp 20.000
     */
    /**
         * Mendapatkan tarif ongkos kirim dinamis berdasarkan ID Kecamatan.
         * Alur bisnis: Jika kecamatan tidak ditemukan dalam database ongkir khusus,
         * maka akan dikenakan tarif *default/flat* sebesar Rp 20.000.
         * @param int|null $districtId
         * @return float
         */
    public static function getCostByDistrict(?int $districtId): float
    {
        if (!$districtId) {
            return 0;
        }

        $shipping = static::where('kecamatan_id', $districtId)->first();
        return $shipping ? (float) $shipping->cost : 20000;
    }
}
