<?php

$viewsToRename = [
    'resources/views/customer/event_checkout.blade.php' => 'resources/views/customer/acara_checkout.blade.php',
    'resources/views/customer/event_custom.blade.php' => 'resources/views/customer/acara_kustom.blade.php',
    'resources/views/customer/event_package.blade.php' => 'resources/views/customer/acara_paket.blade.php',
    'resources/views/customer/event_packages.blade.php' => 'resources/views/customer/acara_paket_list.blade.php', // avoid conflict
    'resources/views/customer/event_service.blade.php' => 'resources/views/customer/acara_layanan.blade.php',
];

$dir = __DIR__;

foreach ($viewsToRename as $old => $new) {
    $oldPath = $dir . '/' . $old;
    $newPath = $dir . '/' . $new;
    if (file_exists($oldPath)) {
        rename($oldPath, $newPath);
        echo "Renamed $old to $new\n";
    }
}

// Find and replace in controllers
$controllers = [
    $dir . '/app/Http/Controllers/Customer/CheckoutController.php',
    $dir . '/app/Http/Controllers/Customer/DashboardController.php'
];

foreach ($controllers as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace('customer.event_checkout', 'customer.acara_checkout', $content);
        $content = str_replace('customer.event_custom', 'customer.acara_kustom', $content);
        $content = str_replace('customer.event_package', 'customer.acara_paket', $content);
        $content = str_replace('customer.event_packages', 'customer.acara_paket_list', $content);
        $content = str_replace('customer.event_service', 'customer.acara_layanan', $content);
        file_put_contents($file, $content);
        echo "Updated references in $file\n";
    }
}
