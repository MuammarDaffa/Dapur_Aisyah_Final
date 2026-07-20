<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LayananKatering;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ViewErrorBag;

echo "--- Testing Views ---\n";

// mock session and errors
$errors = new ViewErrorBag;
View::share('errors', $errors);
session()->start();

$harian = LayananKatering::harian()->first();
if ($harian) {
    try {
        $catering = $harian;
        $produk = $harian->produk()->latest()->paginate(10);
        $allProducts = $harian->produk()->active()->get();
        $extras = $harian->opsiKustom()->where('type', 'extra')->get();
        $currentSchedule = $harian->periodeMenu()
            ->with(['items' => fn($q) => $q->orderBy('menu_date')])
            ->withCount('items')
            ->latest('start_date')
            ->first();
            
        $view = View::make('admin.catering.show-harian', compact('catering', 'produk', 'allProducts', 'extras', 'currentSchedule'))->render();
        echo "SUCCESS: Harian view rendered.\n";
    } catch (\Exception $e) {
        echo "ERROR Harian: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
} else {
    echo "No Harian service found to test.\n";
}

$acara = LayananKatering::acara()->first();
if ($acara) {
    try {
        $catering = $acara;
        $pakets = $acara->packages()->with('opsiKustom')->latest()->paginate(10);
        $menus = $acara->opsiKustom()->where('type', 'menu')->get();
        $servings = \App\Models\OpsiKustom::where('type', 'tipe_penyajian')->get();
        $extras = $acara->opsiKustom()->where('type', 'extra')->get();
        
        $view = View::make('admin.catering.show-acara', compact('catering', 'pakets', 'menus', 'servings', 'extras'))->render();
        echo "SUCCESS: Acara view rendered.\n";
    } catch (\Exception $e) {
        echo "ERROR Acara: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
} else {
    echo "No Acara service found to test.\n";
}

echo "--- Test Completed ---\n";
