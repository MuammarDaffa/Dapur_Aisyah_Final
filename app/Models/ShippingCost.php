<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ShippingCost menyimpan konfigurasi biaya pengiriman (ongkir).
 * Biaya ini dikelompokkan berdasarkan wilayah Kecamatan (District) tujuan pengiriman.
 */
class ShippingCost extends Model
{
    protected $fillable = ['district_id', 'cost', 'notes'];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
        ];
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
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

        $shipping = static::where('district_id', $districtId)->first();
        return $shipping ? (float) $shipping->cost : 20000;
    }
}
