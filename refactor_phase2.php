<?php

$dir = __DIR__;

// 1. Rename Controllers
$controllersToRename = [
    'app/Http/Controllers/Admin/CustomerController.php' => 'app/Http/Controllers/Admin/PelangganController.php',
    'app/Http/Controllers/Admin/OrderController.php' => 'app/Http/Controllers/Admin/PesananController.php',
    'app/Http/Controllers/Admin/PackageController.php' => 'app/Http/Controllers/Admin/PaketKateringController.php',
    'app/Http/Controllers/Admin/ProductController.php' => 'app/Http/Controllers/Admin/ProdukController.php',
    'app/Http/Controllers/Admin/ReportController.php' => 'app/Http/Controllers/Admin/LaporanController.php',
    'app/Http/Controllers/Admin/ReviewController.php' => 'app/Http/Controllers/Admin/UlasanController.php',
    'app/Http/Controllers/Admin/ShippingController.php' => 'app/Http/Controllers/Admin/OngkosKirimController.php',
    
    'app/Http/Controllers/Customer' => 'app/Http/Controllers/Pelanggan',
    // After folder rename:
    'app/Http/Controllers/Pelanggan/CartController.php' => 'app/Http/Controllers/Pelanggan/KeranjangController.php',
    'app/Http/Controllers/Pelanggan/CheckoutController.php' => 'app/Http/Controllers/Pelanggan/PembayaranController.php',
    'app/Http/Controllers/Pelanggan/OrderController.php' => 'app/Http/Controllers/Pelanggan/PesananController.php',
    'app/Http/Controllers/Pelanggan/ProfileController.php' => 'app/Http/Controllers/Pelanggan/ProfilController.php',
    'app/Http/Controllers/Pelanggan/ReviewController.php' => 'app/Http/Controllers/Pelanggan/UlasanController.php',
];

foreach ($controllersToRename as $old => $new) {
    $oldPath = $dir . '/' . $old;
    $newPath = $dir . '/' . $new;
    if (file_exists($oldPath)) {
        rename($oldPath, $newPath);
        echo "Renamed $old -> $new\n";
    }
}

// 2. Rename Views Folders
$viewsToRename = [
    'resources/views/customer' => 'resources/views/pelanggan',
    'resources/views/admin/orders' => 'resources/views/admin/pesanan',
    'resources/views/admin/products' => 'resources/views/admin/produk',
    'resources/views/admin/packages' => 'resources/views/admin/paket_katering',
    'resources/views/admin/reports' => 'resources/views/admin/laporan',
    'resources/views/admin/reviews' => 'resources/views/admin/ulasan',
    'resources/views/admin/shipping' => 'resources/views/admin/ongkos_kirim',
    // Inside pelanggan (formerly customer)
    'resources/views/pelanggan/orders' => 'resources/views/pelanggan/pesanan',
];

foreach ($viewsToRename as $old => $new) {
    $oldPath = $dir . '/' . $old;
    $newPath = $dir . '/' . $new;
    if (file_exists($oldPath)) {
        rename($oldPath, $newPath);
        echo "Renamed $old -> $new\n";
    }
}

// 3. Find and Replace strings in all files
$directories = [
    $dir . '/app',
    $dir . '/resources/views',
    $dir . '/routes',
    $dir . '/config'
];

$replacements = [
    // Controllers & Namespaces
    'CustomerController' => 'PelangganController',
    'OrderController' => 'PesananController',
    'ProductController' => 'ProdukController',
    'PackageController' => 'PaketKateringController',
    'ReportController' => 'LaporanController',
    'ReviewController' => 'UlasanController',
    'ShippingController' => 'OngkosKirimController',
    'CartController' => 'KeranjangController',
    'CheckoutController' => 'PembayaranController',
    'App\Http\Controllers\Customer' => 'App\Http\Controllers\Pelanggan',
    
    // View paths
    "'customer." => "'pelanggan.",
    '"customer.' => '"pelanggan.',
    "'admin.orders." => "'admin.pesanan.",
    '"admin.orders.' => '"admin.pesanan.',
    "'admin.products." => "'admin.produk.",
    '"admin.products.' => '"admin.produk.',
    "'admin.packages." => "'admin.paket_katering.",
    '"admin.packages.' => '"admin.paket_katering.',
    "'admin.reports." => "'admin.laporan.",
    '"admin.reports.' => '"admin.laporan.',
    "'admin.reviews." => "'admin.ulasan.",
    '"admin.reviews.' => '"admin.ulasan.',
    "'admin.shipping." => "'admin.ongkos_kirim.",
    '"admin.shipping.' => '"admin.ongkos_kirim.',
    "'pelanggan.orders." => "'pelanggan.pesanan.",
    '"pelanggan.orders.' => '"pelanggan.pesanan.',
    
    // Route names
    "'customer." => "'pelanggan.",
    '"customer.' => '"pelanggan.',
    "'orders." => "'pesanan.",
    '"orders.' => '"pesanan.',
    "'products." => "'produk.",
    '"products.' => '"produk.',
    "'packages." => "'paket_katering.",
    '"packages.' => '"paket_katering.',
    "'reports." => "'laporan.",
    '"reports.' => '"laporan.',
    "'reviews." => "'ulasan.",
    '"reviews.' => '"ulasan.',
    "'shipping." => "'ongkos_kirim.",
    '"shipping.' => '"ongkos_kirim.',
    "'cart." => "'keranjang.",
    '"cart.' => '"keranjang.',
    "'checkout." => "'pembayaran.",
    '"checkout.' => '"pembayaran.',

    // Route groups prefix
    "'prefix' => 'customer'" => "'prefix' => 'pelanggan'",
    '"prefix" => "customer"' => '"prefix" => "pelanggan"',

    // Basic URLs
    '/customer/' => '/pelanggan/',
    '/orders' => '/pesanan',
    '/products' => '/produk',
    '/packages' => '/paket_katering',
    '/reports' => '/laporan',
    '/reviews' => '/ulasan',
    '/shipping' => '/ongkos_kirim',
    '/cart' => '/keranjang',
    '/checkout' => '/pembayaran',
];

$regexReplacements = [
    // Variables
    '/\$order\b/' => '$pesanan',
    '/\$orders\b/' => '$pesanans', // We'll fix to plural later or just use $pesanan
    '/\$product\b/' => '$produk',
    '/\$products\b/' => '$produks',
    '/\$customer\b/' => '$pelanggan',
    '/\$customers\b/' => '$pelanggans',
    '/\$cart\b/' => '$keranjang',
    '/\$carts\b/' => '$keranjangs',
    '/\$package\b/' => '$paket',
    '/\$packages\b/' => '$pakets',
    '/\$review\b/' => '$ulasan',
    '/\$reviews\b/' => '$ulasans',
    '/\$shippingCost\b/' => '$ongkosKirim',
    '/\$shippingCosts\b/' => '$ongkosKirims',
    '/\$report\b/' => '$laporan',
    '/\$reports\b/' => '$laporans',
    
    // UI Texts (Blade syntax) - Case sensitive matching
    '/>\s*Orders\s*</' => '>Pesanan<',
    '/>\s*Order\s*</' => '>Pesanan<',
    '/>\s*Products\s*</' => '>Produk<',
    '/>\s*Product\s*</' => '>Produk<',
    '/>\s*Customers\s*</' => '>Pelanggan<',
    '/>\s*Customer\s*</' => '>Pelanggan<',
    '/>\s*Shipping Cost\s*</' => '>Ongkos Kirim<',
    '/>\s*Cart\s*</' => '>Keranjang<',
    '/>\s*Checkout\s*</' => '>Lanjut Ke Pembayaran<',
    '/>\s*Reviews\s*</' => '>Ulasan<',
    '/>\s*Review\s*</' => '>Ulasan<',
    '/>\s*Reports\s*</' => '>Laporan<',
    '/>\s*Report\s*</' => '>Laporan<',
    '/>\s*Packages\s*</' => '>Paket Katering<',
    '/>\s*Package\s*</' => '>Paket Katering<',
    
    // Some common labels in blade
    '/\bOrders\b/' => 'Pesanan',
    '/\bOrder\b/' => 'Pesanan',
    '/\bProducts\b/' => 'Produk',
    '/\bProduct\b/' => 'Produk',
    '/\bCustomers\b/' => 'Pelanggan',
    '/\bCustomer\b/' => 'Pelanggan',
    '/\bShipping Cost\b/' => 'Ongkos Kirim',
    '/\bCheckout\b/' => 'Lanjut Ke Pembayaran',
    '/\bCart\b/' => 'Keranjang',
    '/\bPackages\b/' => 'Paket Katering',
    '/\bPackage\b/' => 'Paket Katering',
    '/\bReviews\b/' => 'Ulasan',
    '/\bReview\b/' => 'Ulasan',
    '/\bReports\b/' => 'Laporan',
    '/\bReport\b/' => 'Laporan',
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
                $content = str_replace($search, $replace, $content);
            }

            foreach ($regexReplacements as $pattern => $replace) {
                $content = preg_replace($pattern, $replace, $content);
            }

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

echo "Phase 2 Refactoring completed.\n";

