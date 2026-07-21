<?php
$file = __DIR__ . '/app/Http/Controllers/Pelanggan/PembayaranController.php';
$content = file_get_contents($file);

$content = str_replace(
    "['produk.layananKatering'",
    "['menuHarian.layananKatering'",
    $content
);
$content = str_replace(
    '$firstCart->produk ? $firstCart->produk->layananKatering',
    '$firstCart->menuHarian ? $firstCart->menuHarian->layananKatering',
    $content
);
$content = str_replace(
    'if ($firstCart->produk) {',
    'if ($firstCart->menuHarian) {',
    $content
);
$content = str_replace(
    '$firstCart->produk->layanan_katering_id',
    '$firstCart->menuHarian->layanan_katering_id',
    $content
);
$content = str_replace(
    'if ($keranjang->produk) {',
    'if ($keranjang->menuHarian) {',
    $content
);
$content = str_replace(
    '$keranjang->produk->harga',
    '$keranjang->menuHarian->harga',
    $content
);
$content = str_replace(
    '$keranjang->produk->name',
    '$keranjang->menuHarian->nama_menu',
    $content
);
$content = str_replace(
    "'produk_id' => \$keranjang->produk_id,",
    "'menu_harian_id' => \$keranjang->menu_harian_id,",
    $content
);

file_put_contents($file, $content);
echo "PembayaranController updated.\n";
