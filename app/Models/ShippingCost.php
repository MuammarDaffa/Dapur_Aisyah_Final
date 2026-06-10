<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public static function getCostByDistrict(?int $districtId): float
    {
        if (!$districtId) {
            return 0;
        }

        $shipping = static::where('district_id', $districtId)->first();
        return $shipping ? (float) $shipping->cost : 20000;
    }
}
