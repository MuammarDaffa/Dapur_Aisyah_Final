<?php

function refactorFile($file, $replacements) {
    if(!file_exists($file)) return;
    $content = file_get_contents($file);
    $content = str_replace(array_keys($replacements), array_values($replacements), $content);
    file_put_contents($file, $content);
    echo "Refactored $file\n";
}

// 1. CateringController
refactorFile('app/Http/Controllers/Admin/CateringController.php', [
    '$isDaily' => '$isHarian',
    'isDaily()' => 'isHarian()',
    'isEvent()' => 'isAcara()',
]);

// 2. MenuPeriodController
refactorFile('app/Http/Controllers/Admin/MenuPeriodController.php', [
    'isDaily()' => 'isHarian()',
]);

// 3. DashboardController (Pelanggan)
refactorFile('app/Http/Controllers/Pelanggan/DashboardController.php', [
    'LayananKatering::daily()' => 'LayananKatering::harian()',
]);

// 4. KeranjangController (Pelanggan)
refactorFile('app/Http/Controllers/Pelanggan/KeranjangController.php', [
    '$dailyCarts' => '$keranjangHarian',
    '$eventCarts' => '$keranjangAcara',
    '$dailyGroups' => '$grupHarian',
    '$eventGroups' => '$grupAcara',
    'isDailyItem()' => 'isHarianItem()',
    'isEventItem()' => 'isAcaraItem()',
    'storeEventGroup' => 'storeGrupAcara',
    'Pesanan event' => 'Pesanan acara',
    'Katering Harian (Daily)' => 'Katering Harian',
    'Event' => 'Acara',
    'daily' => 'harian',
    'event' => 'acara'
]);

// 5. PembayaranController (Pelanggan)
refactorFile('app/Http/Controllers/Pelanggan/PembayaranController.php', [
    'isDailyItem()' => 'isHarianItem()',
    'isEventItem()' => 'isAcaraItem()',
    'showEventCheckout' => 'showAcaraCheckout',
    'checkoutEventGroup' => 'checkoutGrupAcara',
    '$eventGroups' => '$grupAcara',
    'event order' => 'pesanan acara',
    'Pesanan event' => 'Pesanan acara',
    'daily items' => 'item harian',
    'Hanya daily' => 'Hanya harian',
    'daily checkout' => 'checkout harian',
    'daily' => 'harian',
    'event' => 'acara'
]);

// 6. User Model
refactorFile('app/Models/User.php', [
    '$dailyCount' => '$jumlahHarian',
    '$eventCount' => '$jumlahAcara',
    'Katering Event' => 'Katering Acara',
    'Event' => 'Acara',
]);

echo "Done Controller Refactoring.";
