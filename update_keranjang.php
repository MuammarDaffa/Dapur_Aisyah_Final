<?php
$file = __DIR__ . '/app/Http/Controllers/Pelanggan/KeranjangController.php';
$content = file_get_contents($file);

$content = str_replace('use App\Models\Produk;', 'use App\Models\MenuHarian;', $content);
$content = str_replace("['produk.layananKatering'", "['menuHarian.layananKatering'", $content);
$content = str_replace(
    "'produk_id' => 'required_without:opsi_kustom_id|exists:produk,id',",
    "'menu_harian_id' => 'required_without:opsi_kustom_id|exists:menu_harian,id',",
    $content
);
$content = str_replace(
    "'opsi_kustom_id' => 'required_without:produk_id|exists:opsi_kustom,id',",
    "'opsi_kustom_id' => 'required_without:menu_harian_id|exists:opsi_kustom,id',",
    $content
);
$content = str_replace(
    "->where('produk_id', \$validated['produk_id'] ?? null)",
    "->where('menu_harian_id', \$validated['menu_harian_id'] ?? null)",
    $content
);
$content = str_replace(
    "'produk_id' => \$validated['produk_id'] ?? null,",
    "'menu_harian_id' => \$validated['menu_harian_id'] ?? null,",
    $content
);

file_put_contents($file, $content);
echo "KeranjangController updated.\n";
