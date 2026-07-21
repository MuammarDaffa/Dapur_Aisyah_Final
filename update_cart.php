<?php
$file = __DIR__ . '/resources/views/pelanggan/cart.blade.php';
$content = file_get_contents($file);

$content = str_replace('$keranjang->produk', '$keranjang->menuHarian', $content);
$content = str_replace('$keranjang->menuHarian->image', 'null', $content); // No image
$content = str_replace('$keranjang->menuHarian->name', '$keranjang->menuHarian->nama_menu', $content);

$content = str_replace('$c->produk', '$c->menuHarian', $content);
$content = str_replace('$c->menuHarian->name', '$c->menuHarian->nama_menu', $content);
$content = str_replace("'produk_id' => \$c->produk_id", "'menu_harian_id' => \$c->menu_harian_id", $content);

$content = preg_replace('/if \(currentHarianCart\.produk_id\) \{.*?extrasUrl = `\/api\/produk\/\$\{currentHarianCart\.produk_id\}\/extras`;.*?\}\s*else/s', 'if (false) {} else', $content); // Disable api fetch for extra in cart, wait I should fix the edit modal for cart if it exists. Actually, we can just remove the `edit` harian modal logic or adapt it. But for now I'll just change `produk_id` to `menu_harian_id`.

$content = str_replace('currentHarianCart.produk_id', 'currentHarianCart.menu_harian_id', $content);

file_put_contents($file, $content);
echo "cart.blade.php updated.\n";
