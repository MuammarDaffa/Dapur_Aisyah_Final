<?php

$dir = __DIR__;

$directories = [
    $dir . '/app',
    $dir . '/database/migrations',
    $dir . '/database/seeders',
    $dir . '/database/factories',
    $dir . '/resources/views',
    $dir . '/routes',
    $dir . '/config',
];

$replacements = [
    // --- Models (Singular) ---
    'CateringPackage' => 'PaketKatering',
    'CateringService' => 'LayananKatering',
    'CustomOption' => 'OpsiKustom',
    'MenuPeriodItem' => 'ItemPeriodeMenu',
    'MenuPeriod' => 'PeriodeMenu',
    'OrderItem' => 'DetailPesanan',
    'ShippingCost' => 'OngkosKirim',
    'Cart' => 'Keranjang',
    'District' => 'Kecamatan',
    'Invoice' => 'Tagihan',
    'Order' => 'Pesanan',
    'Product' => 'Produk',
    'Review' => 'Ulasan',
    'Village' => 'Desa',

    // --- Models (Plural) ---
    'CateringPackages' => 'PaketKatering',
    'CateringServices' => 'LayananKatering',
    'CustomOptions' => 'OpsiKustom',
    'MenuPeriodItems' => 'ItemPeriodeMenu',
    'MenuPeriods' => 'PeriodeMenu',
    'OrderItems' => 'DetailPesanan',
    'ShippingCosts' => 'OngkosKirim',
    'Carts' => 'Keranjang',
    'Districts' => 'Kecamatan',
    'Invoices' => 'Tagihan',
    'Orders' => 'Pesanan',
    'Products' => 'Produk',
    'Reviews' => 'Ulasan',
    'Villages' => 'Desa',

    // --- Variables (camelCase) ---
    'cateringPackage' => 'paketKatering',
    'cateringService' => 'layananKatering',
    'customOption' => 'opsiKustom',
    'menuPeriodItem' => 'itemPeriodeMenu',
    'menuPeriod' => 'periodeMenu',
    'orderItem' => 'detailPesanan',
    'shippingCost' => 'ongkosKirim',
    'cart' => 'keranjang',
    'district' => 'kecamatan',
    'invoice' => 'tagihan',
    'order' => 'pesanan',
    'product' => 'produk',
    'review' => 'ulasan',
    'village' => 'desa',

    // Plural variables
    'cateringPackages' => 'paketKatering',
    'cateringServices' => 'layananKatering',
    'customOptions' => 'opsiKustom',
    'menuPeriodItems' => 'itemPeriodeMenu',
    'menuPeriods' => 'periodeMenu',
    'orderItems' => 'detailPesanan',
    'shippingCosts' => 'ongkosKirim',
    'carts' => 'keranjang',
    'districts' => 'kecamatan',
    'invoices' => 'tagihan',
    'orders' => 'pesanan',
    'products' => 'produk',
    'reviews' => 'ulasan',
    'villages' => 'desa',

    // --- Tables and Columns (snake_case) ---
    'catering_packages' => 'paket_katering',
    'catering_services' => 'layanan_katering',
    'custom_options' => 'opsi_kustom',
    'menu_period_items' => 'item_periode_menu',
    'menu_periods' => 'periode_menu',
    'order_items' => 'detail_pesanan',
    'shipping_costs' => 'ongkos_kirim',
    'carts' => 'keranjang',
    'districts' => 'kecamatan',
    'invoices' => 'tagihan',
    'orders' => 'pesanan',
    'products' => 'produk',
    'reviews' => 'ulasan',
    'villages' => 'desa',
    
    // Singular tables/columns
    'catering_package' => 'paket_katering',
    'catering_service' => 'layanan_katering',
    'custom_option' => 'opsi_kustom',
    'menu_period_item' => 'item_periode_menu',
    'menu_period' => 'periode_menu',
    'order_item' => 'detail_pesanan',
    'shipping_cost' => 'ongkos_kirim',
    
    // Specific IDs and common columns
    'catering_service_id' => 'layanan_katering_id',
    'package_id' => 'paket_katering_id',
    'district_id' => 'kecamatan_id',
    'village_id' => 'desa_id',
    'order_id' => 'pesanan_id',
    'product_id' => 'produk_id',
    'custom_option_id' => 'opsi_kustom_id',
    'menu_period_id' => 'periode_menu_id',
    'order_number' => 'nomor_pesanan',
    'invoice_number' => 'nomor_tagihan',
    'payment_method' => 'metode_pembayaran',
    'payment_status' => 'status_pembayaran',
    'cancellation_reason' => 'alasan_pembatalan',
    'cancelled_at' => 'dibatalkan_pada',
    'notes' => 'catatan',
    'description' => 'deskripsi',
    'min_order' => 'minimal_pesanan',
    'max_portion' => 'maksimal_porsi',
    'cutoff_time' => 'batas_waktu_pemesanan',
    'available_features' => 'fitur_tersedia',
    'serving_type' => 'tipe_penyajian',
    'portion' => 'porsi',
    'order_date' => 'tanggal_pesanan',
    'pickup_method' => 'metode_pengambilan',
    'address_detail' => 'detail_alamat',
];

$regexReplacements = [
    // Status Enum Orders
    "/'pending_payment'/" => "'menunggu_pembayaran'",
    '/"pending_payment"/' => '"menunggu_pembayaran"',
    "/'processing'/" => "'diproses'",
    '/"processing"/' => '"diproses"',
    "/'on_delivery'/" => "'dikirim'",
    '/"on_delivery"/' => '"dikirim"',
    "/'completed'/" => "'selesai'",
    '/"completed"/' => '"selesai"',
    "/'cancelled'/" => "'dibatalkan'",
    '/"cancelled"/' => '"dibatalkan"',
    
    // Status Enum Payment
    "/'unpaid'/" => "'belum_dibayar'",
    '/"unpaid"/' => '"belum_dibayar"',
    "/'paid'/" => "'sudah_dibayar'",
    '/"paid"/' => '"sudah_dibayar"',
    "/'failed'/" => "'gagal'",
    '/"failed"/' => '"gagal"',

    // Type Enum
    "/'daily'/" => "'harian'",
    '/"daily"/' => '"harian"',
    "/'event'/" => "'acara'",
    '/"event"/' => '"acara"',

    // Type variants
    "/\bshow-daily\b/" => "show-harian",
    "/\bshow-event\b/" => "show-acara",
    "/\bdaily-extras\b/" => "ekstra-harian",
    "/\bDailyExtras\b/" => "EkstraHarian",
    "/\bDailyExtra\b/" => "EkstraHarian",
    "/\bshowDaily\b/" => "showHarian",
    "/\bshowEvent\b/" => "showAcara",
    
    // Blade suffixes
    "/show-daily\.blade\.php/" => "show-harian.blade.php",
    "/show-event\.blade\.php/" => "show-acara.blade.php",

    // Common words
    "/\bprice\b/" => "harga",
    "/\bquantity\b/" => "jumlah",
];

function processDirectory($path) {
    global $replacements, $regexReplacements;
    if (!is_dir($path)) return;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
            $content = file_get_contents($file->getPathname());
            $originalContent = $content;

            foreach ($replacements as $search => $replace) {
                // Word boundary check
                $content = preg_replace("/\b" . preg_quote($search, '/') . "\b/", $replace, $content);
            }

            foreach ($regexReplacements as $pattern => $replace) {
                $content = preg_replace($pattern, $replace, $content);
            }

            $content = str_replace("['transfer', 'cod']", "['transfer']", $content);
            $content = preg_replace("/in:transfer,cod/", "in:transfer", $content);
            // In case it's ['cod', 'transfer']
            $content = str_replace("['cod', 'transfer']", "['transfer']", $content);

            if ($content !== $originalContent) {
                file_put_contents($file->getPathname(), $content);
                echo "Updated: " . $file->getPathname() . "\n";
            }
        }
    }
}

foreach ($directories as $d) {
    processDirectory($d);
}

// RENAME FILES (Models, Controllers, Views)
$renames = [
    'app/Models/Cart.php' => 'app/Models/Keranjang.php',
    'app/Models/CateringPackage.php' => 'app/Models/PaketKatering.php',
    'app/Models/CateringService.php' => 'app/Models/LayananKatering.php',
    'app/Models/CustomOption.php' => 'app/Models/OpsiKustom.php',
    'app/Models/District.php' => 'app/Models/Kecamatan.php',
    'app/Models/Invoice.php' => 'app/Models/Tagihan.php',
    'app/Models/MenuPeriod.php' => 'app/Models/PeriodeMenu.php',
    'app/Models/MenuPeriodItem.php' => 'app/Models/ItemPeriodeMenu.php',
    'app/Models/Order.php' => 'app/Models/Pesanan.php',
    'app/Models/OrderItem.php' => 'app/Models/DetailPesanan.php',
    'app/Models/Product.php' => 'app/Models/Produk.php',
    'app/Models/Review.php' => 'app/Models/Ulasan.php',
    'app/Models/ShippingCost.php' => 'app/Models/OngkosKirim.php',
    'app/Models/Village.php' => 'app/Models/Desa.php',
    
    // Seeders that might need rename
    'database/seeders/CateringServiceSeeder.php' => 'database/seeders/LayananKateringSeeder.php',
    'database/seeders/CateringPackageSeeder.php' => 'database/seeders/PaketKateringSeeder.php',
    'database/seeders/ProductSeeder.php' => 'database/seeders/ProdukSeeder.php',
    'database/seeders/DistrictSeeder.php' => 'database/seeders/KecamatanSeeder.php',
    'database/seeders/VillageSeeder.php' => 'database/seeders/DesaSeeder.php',
    'database/seeders/DailyExtraSeeder.php' => 'database/seeders/EkstraHarianSeeder.php',

    // Views
    'resources/views/admin/catering/show-daily.blade.php' => 'resources/views/admin/catering/show-harian.blade.php',
    'resources/views/admin/catering/show-event.blade.php' => 'resources/views/admin/catering/show-acara.blade.php',
];

foreach ($renames as $old => $new) {
    $oldPath = __DIR__ . '/' . $old;
    $newPath = __DIR__ . '/' . $new;
    if (file_exists($oldPath)) {
        rename($oldPath, $newPath);
        echo "Renamed: $old -> $new\n";
    }
}

echo "Refactoring completed.\n";

