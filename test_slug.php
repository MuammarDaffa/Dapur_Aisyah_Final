<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LayananKatering;
use Illuminate\Support\Str;

echo "--- Testing Slug Generation ---\n";

// Clear previous test data
LayananKatering::where('name', 'like', 'Test Katering%')->delete();

// 1. Add data with different names
$k1 = LayananKatering::create([
    'name' => 'Test Katering Harian A',
    'fitur_tersedia' => ['menu_harian'],
    'is_active' => true,
]);
echo "Created: {$k1->name} -> Slug: {$k1->slug}\n";

$k2 = LayananKatering::create([
    'name' => 'Test Katering Harian B',
    'fitur_tersedia' => ['menu_harian'],
    'is_active' => true,
]);
echo "Created: {$k2->name} -> Slug: {$k2->slug}\n";

// 2. Add data with the same name
$k3 = LayananKatering::create([
    'name' => 'Test Katering Harian A',
    'fitur_tersedia' => ['menu_harian'],
    'is_active' => true,
]);
echo "Created: {$k3->name} -> Slug: {$k3->slug}\n";

$k4 = LayananKatering::create([
    'name' => 'Test Katering Harian A',
    'fitur_tersedia' => ['menu_harian'],
    'is_active' => true,
]);
echo "Created: {$k4->name} -> Slug: {$k4->slug}\n";

// 3. Edit data (slug should remain the same)
$oldSlug = $k1->slug;
$k1->name = 'Test Katering Harian A Berubah';
$k1->save();
echo "Updated K1 Name: {$k1->name} -> Slug: {$k1->slug} (Expected: {$oldSlug})\n";

if ($k1->slug !== $oldSlug) {
    echo "ERROR: Slug changed on update!\n";
} else {
    echo "SUCCESS: Slug remained valid and unchanged on update.\n";
}

// 4. Verify uniqueness
$slugs = [$k1->slug, $k2->slug, $k3->slug, $k4->slug];
$uniqueSlugs = array_unique($slugs);
if (count($slugs) === count($uniqueSlugs)) {
    echo "SUCCESS: All slugs are unique.\n";
} else {
    echo "ERROR: Duplicate slugs found!\n";
    print_r($slugs);
}

// Cleanup
LayananKatering::where('name', 'like', 'Test Katering%')->delete();

echo "--- Test Completed ---\n";
