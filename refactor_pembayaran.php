<?php

$file = 'app/Http/Controllers/Pelanggan/PembayaranController.php';
$content = file_get_contents($file);

$target = <<<PHP
        \$customMenuItems = \$groupItems->where('item_type', 'custom_menu');
        if (\$customMenuItems->isNotEmpty()) {
            \$service = \$groupItems->first()->layananKatering;
            \$customPortions = \$customMenuItems->sum('jumlah');
            if (\$service && \$service->maksimal_porsi && \$customPortions > \$service->maksimal_porsi) {
                if (\$request->expectsJson() || \$request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Total porsi melebihi batas maksimal ({\$service->maksimal_porsi} porsi)."
                    ], 422);
                }
                return back()->with('error', "Total porsi melebihi batas maksimal ({\$service->maksimal_porsi} porsi).");
            }
        }
PHP;

$content = str_replace($target, '', $content);
file_put_contents($file, $content);
echo "Replaced PembayaranController.";
