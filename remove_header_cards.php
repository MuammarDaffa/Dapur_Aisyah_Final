<?php

// show-acara.blade.php
$file = 'resources/views/admin/catering/show-acara.blade.php';
$content = file_get_contents($file);

// Use regex to remove <div class="row"> ... <!-- Header Card --> ... </div>
// since we know it's a specific block, we can just replace everything between <div class="row"> \n <!-- Header Card --> and the next <div class="row">.
$content = preg_replace('/<div class="row">\s*<!-- Header Card -->.*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*/s', '', $content);

file_put_contents($file, $content);

// show-harian.blade.php
$file = 'resources/views/admin/catering/show-harian.blade.php';
$content = file_get_contents($file);

$content = preg_replace('/<div class="row">\s*<!-- Header Card -->.*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*/s', '', $content);

file_put_contents($file, $content);

echo "Replaced header cards in show-acara and show-harian.\n";
