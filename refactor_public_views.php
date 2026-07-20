<?php

// 1. landing.blade.php
$file = 'resources/views/public/landing.blade.php';
$content = file_get_contents($file);
$content = preg_replace('/<div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">\s*<div class="text-primary fw-bold">\s*Mulai Rp \{\{ number_format\(\$service->base_price, 0, \',\', \'\.\'\) \}\}\s*<\/div>\s*<a href="[^"]+" class="btn btn-outline-primary btn-sm rounded-pill">\s*Pesan Sekarang\s*<i class="fa-solid fa-arrow-right ms-1"><\/i>\s*<\/a>\s*<\/div>/s', '<div class="mt-3 pt-3 border-top text-end"><a href="{{ route(\'login\') }}" class="btn btn-outline-primary btn-sm rounded-pill">Pesan Sekarang <i class="fa-solid fa-arrow-right ms-1"></i></a></div>', $content);
$content = preg_replace('/<div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">\s*<div class="text-primary fw-bold">\s*Mulai Rp \{\{ number_format\(\$service->base_price, 0, \',\', \'\.\'\) \}\}\s*<\/div>\s*<a href="\{\{ route\(\'pelanggan.keranjang\'\) \}\}" class="btn btn-outline-primary btn-sm rounded-pill">\s*Pesan Sekarang\s*<i class="fa-solid fa-arrow-right ms-1"><\/i>\s*<\/a>\s*<\/div>/s', '<div class="mt-3 pt-3 border-top text-end"><a href="{{ route(\'pelanggan.keranjang\') }}" class="btn btn-outline-primary btn-sm rounded-pill">Pesan Sekarang <i class="fa-solid fa-arrow-right ms-1"></i></a></div>', $content);

file_put_contents($file, $content);

// 2. service-card.blade.php
$file = 'resources/views/components/service-card.blade.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    $content = preg_replace('/<div class="mt-4 pt-3 border-top">\s*<div class="d-flex justify-content-between align-items-center">\s*<div>\s*<span class="d-block text-muted small">Mulai dari<\/span>\s*<span class="fs-5 fw-bold text-primary">\s*Rp \{\{ number_format\(\$service->base_price, 0, \',\', \'\.\'\) \}\}\s*<\/span>\s*<\/div>\s*<button class="btn btn-primary rounded-pill px-4 shadow-sm">\s*Pilih Layanan\s*<\/button>\s*<\/div>\s*<\/div>/s', '<div class="mt-4 pt-3 border-top text-end"><button class="btn btn-primary rounded-pill px-4 shadow-sm">Pilih Layanan</button></div>', $content);
    file_put_contents($file, $content);
}

echo "Replaced landing and service card views.";
