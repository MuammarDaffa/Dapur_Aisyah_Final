<?php
$file = __DIR__ . '/resources/views/pelanggan/products.blade.php';
$content = file_get_contents($file);

// Replace $items loop with $menus loop
$content = str_replace('@if($items->isNotEmpty())', '@if($menus->isNotEmpty())', $content);
$content = str_replace('@foreach($items as $item)', '@foreach($menus as $menu)', $content);
$content = preg_replace('/@php\s+\$produk = \$item->produk;\s+\$canOrder = \$item->canOrder\(\) && \$item->isAvailable\(\);\s+@endphp/s', 
    '@php
        $canOrder = $menu->stok_tersisa > 0;
    @endphp', $content);

$content = str_replace('$produk->image', 'null', $content); // MenuHarian has no image
$content = str_replace('$produk->name', '$menu->nama_menu', $content);
$content = str_replace('$produk->deskripsi', '', $content);
$content = str_replace('$produk->layananKatering->name', '$menu->layananKatering->name', $content);
$content = str_replace('$item->day_name', '$menu->hari', $content);
$content = str_replace('$produk->formatted_price', 'Rp \' . number_format($menu->harga, 0, \',\', \'.\')', $content);

// Update JS data
$jsDataOld = <<<EOT
    // Build produk data from all menu items
    const productsData = {
        @foreach(\$items as \$item)
        {{ \$item->produk->id }}: {
            id: {{ \$item->produk->id }},
            name: @json(\$item->produk->name),
            harga: {{ (float) \$item->produk->harga }},
            service_id: {{ \$item->produk->layanan_katering_id }},
        },
        @endforeach
    };
EOT;

$jsDataNew = <<<EOT
    // Build data
    const productsData = {
        @foreach(\$menus as \$menu)
        {{ \$menu->id }}: {
            id: {{ \$menu->id }},
            name: @json(\$menu->nama_menu),
            harga: {{ (float) \$menu->harga }},
            service_id: {{ \$menu->layanan_katering_id }},
            extras: @json(\$menu->extras)
        },
        @endforeach
    };
EOT;
$content = str_replace($jsDataOld, $jsDataNew, $content);
$content = str_replace('{{ $produk->id }}', '{{ $menu->id }}', $content);
$content = str_replace('{{ $item->menu_date->format(\'Y-m-d\') }}', '{{ $menu->tanggal->format(\'Y-m-d\') }}', $content);

$content = str_replace('name="produk_id"', 'name="menu_harian_id"', $content);

file_put_contents($file, $content);
echo "products.blade.php updated.\n";
