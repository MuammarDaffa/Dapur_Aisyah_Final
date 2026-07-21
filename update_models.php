<?php
// Script to create MenuHarian and MenuHarianExtra models, and update others.

// 1. Create MenuHarian Model
$menuHarianContent = <<<EOT
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuHarian extends Model
{
    use HasFactory;

    protected \$table = 'menu_harian';

    protected \$fillable = [
        'layanan_katering_id',
        'hari',
        'tanggal',
        'nama_menu',
        'harga',
        'stok_awal',
        'stok_tersisa',
    ];
    
    protected \$casts = [
        'tanggal' => 'date'
    ];

    public function layananKatering(): BelongsTo
    {
        return \$this->belongsTo(LayananKatering::class);
    }

    public function extras(): HasMany
    {
        return \$this->hasMany(MenuHarianExtra::class);
    }
}
EOT;
file_put_contents(__DIR__ . '/app/Models/MenuHarian.php', $menuHarianContent);

// 2. Create MenuHarianExtra Model
$menuHarianExtraContent = <<<EOT
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuHarianExtra extends Model
{
    use HasFactory;

    protected \$table = 'menu_harian_extra';

    protected \$fillable = [
        'menu_harian_id',
        'nama_extra',
        'harga',
    ];

    public function menuHarian(): BelongsTo
    {
        return \$this->belongsTo(MenuHarian::class);
    }
}
EOT;
file_put_contents(__DIR__ . '/app/Models/MenuHarianExtra.php', $menuHarianExtraContent);

// 3. Update Keranjang Model
$keranjangFile = __DIR__ . '/app/Models/Keranjang.php';
$keranjang = file_get_contents($keranjangFile);
$keranjang = str_replace(
    'public function produk(): BelongsTo',
    "public function menuHarian(): BelongsTo\n    {\n        return \$this->belongsTo(MenuHarian::class);\n    }\n\n    public function produk(): BelongsTo",
    $keranjang
);
file_put_contents($keranjangFile, $keranjang);

// 4. Update DetailPesanan Model
$detailPesananFile = __DIR__ . '/app/Models/DetailPesanan.php';
$detailPesanan = file_get_contents($detailPesananFile);
$detailPesanan = str_replace(
    'public function produk(): BelongsTo',
    "public function menuHarian(): BelongsTo\n    {\n        return \$this->belongsTo(MenuHarian::class);\n    }\n\n    public function produk(): BelongsTo",
    $detailPesanan
);
file_put_contents($detailPesananFile, $detailPesanan);

echo "Models updated.\n";
