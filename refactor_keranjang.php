<?php

$file = 'app/Http/Controllers/Pelanggan/KeranjangController.php';
$content = file_get_contents($file);

// 1. Remove storeGrupAcara validation
$target1 = <<<PHP
        if (\$totalCustomPortions < \$service->min_portion) {
            return back()->with('error', "Total porsi minimal {\$service->min_portion} porsi.");
        }

        if (\$totalCustomPortions > \$service->maksimal_porsi) {
            return back()->with('error', "Total porsi melebihi batas maksimal ({\$service->maksimal_porsi} porsi).");
        }
PHP;
$content = str_replace($target1, '', $content);

// 2. Remove updateAcaraGroup min portion definition
$target2 = <<<PHP
        \$minPortion = \$service->min_portion;
PHP;
$content = str_replace($target2, '', $content);

// 3. Remove updateAcaraGroup validation
$target3 = <<<PHP
        if (\$totalCustomPortions < \$minPortion) {
            if (\$request->ajax() || \$request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Total porsi minimal {\$minPortion} porsi."], 422);
            }
            return back()->with('error', "Total porsi minimal {\$minPortion} porsi.");
        }

        if (\$totalCustomPortions > \$service->maksimal_porsi) {
            if (\$request->ajax() || \$request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Total porsi melebihi batas maksimal ({\$service->maksimal_porsi} porsi)."], 422);
            }
            return back()->with('error', "Total porsi melebihi batas maksimal ({\$service->maksimal_porsi} porsi).");
        }
PHP;
$content = str_replace($target3, '', $content);

file_put_contents($file, $content);
echo "Replaced KeranjangController.";
