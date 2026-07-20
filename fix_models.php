<?php

$dir = __DIR__;

$models = [
    'Keranjang' => 'keranjang',
    'PaketKatering' => 'paket_katering',
    'LayananKatering' => 'layanan_katering',
    'OpsiKustom' => 'opsi_kustom',
    'Kecamatan' => 'kecamatan',
    'Tagihan' => 'tagihan',
    'PeriodeMenu' => 'periode_menu',
    'ItemPeriodeMenu' => 'item_periode_menu',
    'Pesanan' => 'pesanan',
    'DetailPesanan' => 'detail_pesanan',
    'Produk' => 'produk',
    'Ulasan' => 'ulasan',
    'OngkosKirim' => 'ongkos_kirim',
    'Desa' => 'desa',
];

foreach ($models as $model => $table) {
    $path = $dir . '/app/Models/' . $model . '.php';
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // Cek apakah sudah ada protected $table
        if (strpos($content, 'protected $table') === false) {
            // Sisipkan setelah class Name { atau use HasFactory; dll
            // Cari posisi '{' pertama setelah 'class '
            $classPos = strpos($content, 'class ' . $model);
            if ($classPos !== false) {
                $bracePos = strpos($content, '{', $classPos);
                if ($bracePos !== false) {
                    $insert = "\n    protected \$table = '$table';\n";
                    $content = substr_replace($content, $insert, $bracePos + 1, 0);
                    file_put_contents($path, $content);
                    echo "Added protected \$table = '$table' to $model\n";
                }
            }
        }
    }
}

// Fix EkstraHarianSeeder class name
$ekstraPath = $dir . '/database/seeders/EkstraHarianSeeder.php';
if (file_exists($ekstraPath)) {
    $ekstraContent = file_get_contents($ekstraPath);
    $ekstraContent = str_replace('class DailyExtraSeeder', 'class EkstraHarianSeeder', $ekstraContent);
    file_put_contents($ekstraPath, $ekstraContent);
}

// Update DatabaseSeeder to use EkstraHarianSeeder if it was using DailyExtraSeeder
$dbSeederPath = $dir . '/database/seeders/DatabaseSeeder.php';
if (file_exists($dbSeederPath)) {
    $dbContent = file_get_contents($dbSeederPath);
    $dbContent = str_replace('DailyExtraSeeder', 'EkstraHarianSeeder', $dbContent);
    file_put_contents($dbSeederPath, $dbContent);
}

