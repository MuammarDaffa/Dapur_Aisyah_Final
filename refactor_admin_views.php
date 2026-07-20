<?php

// 1. index.blade.php
$file = 'resources/views/admin/catering/index.blade.php';
$content = file_get_contents($file);

// Remove Harga Mulai header
$content = str_replace('<th class="text-center">Harga Mulai</th>', '', $content);
// Remove deskripsi
$content = preg_replace('/@if\(\$c->deskripsi\).*?@endif/s', '', $content);
// Remove Harga Mulai column
$content = preg_replace('/<td class="align-middle text-center fw-bold text-success">\s*Rp \{\{ number_format\(\$c->base_price, 0, \',\', \'\.\'\) \}\}\s*<\/td>/s', '', $content);
// Fix colspan for empty row (5 to 4)
$content = str_replace('<td colspan="5"', '<td colspan="4"', $content);

file_put_contents($file, $content);

// 2. show-harian.blade.php
$file = 'resources/views/admin/catering/show-harian.blade.php';
$content = file_get_contents($file);

// Remove deskripsi block
$content = preg_replace('/<div class="mb-3">\s*<h6 class="text-muted fw-bold mb-1">Deskripsi<\/h6>\s*<p class="mb-0">\{\{ \$catering->deskripsi \}\}<\/p>\s*<\/div>/s', '', $content);
// Remove Harga Dasar block
$content = preg_replace('/<div class="col-sm-6">\s*<div class="mb-3">\s*<h6 class="text-muted fw-bold mb-1">Harga Dasar<\/h6>\s*<strong class="fs-5 text-primary">Rp \{\{ number_format\(\$catering->base_price, 0, \',\', \'\.\'\) \}\}<\/strong>\s*<\/div>\s*<\/div>/s', '', $content);

file_put_contents($file, $content);

// 3. show-acara.blade.php
$file = 'resources/views/admin/catering/show-acara.blade.php';
$content = file_get_contents($file);

// Remove deskripsi block
$content = preg_replace('/<div class="mb-3">\s*<h6 class="text-muted fw-bold mb-1">Deskripsi<\/h6>\s*<p class="mb-0">\{\{ \$catering->deskripsi \}\}<\/p>\s*<\/div>/s', '', $content);
// Remove Harga Dasar block
$content = preg_replace('/<div class="col-sm-6">\s*<div class="mb-3">\s*<h6 class="text-muted fw-bold mb-1">Harga Dasar<\/h6>\s*<strong class="fs-5 text-primary">Rp \{\{ number_format\(\$catering->base_price, 0, \',\', \'\.\'\) \}\}<\/strong>\s*<\/div>\s*<\/div>/s', '', $content);

file_put_contents($file, $content);

echo "Replaced index and show blade files.";
