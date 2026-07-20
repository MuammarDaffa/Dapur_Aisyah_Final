<?php

$file = 'app/Http/Controllers/Admin/CateringController.php';
$content = file_get_contents($file);

// Replace store method
$newStore = <<<PHP
    public function store(Request \$request)
    {
        \$isHarian = \$request->input('catering_type') === 'harian';
        \$validated = \$request->validate([
            'name' => 'required|string|max:100',
            'catering_type' => 'required|in:harian,acara',
            'minimal_order_days' => \$isHarian ? 'nullable' : 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if (\$isHarian) {
            \$validated['minimal_order_days'] = null;
        }

        // Set fitur_tersedia berdasarkan tipe
        \$validated['fitur_tersedia'] = \$request->catering_type === 'harian'
            ? ['menu_harian']
            : ['paket', 'kustom_penuh'];

        \$validated['slug'] = Str::slug(\$validated['name']);
        \$validated['is_active'] = \$request->boolean('is_active');

        if (\$request->hasFile('image')) {
            \$validated['image'] = \$request->file('image')->store('services', 'public');
        }

        unset(\$validated['catering_type']);

        LayananKatering::create(\$validated);

        return redirect()->route('admin.catering.index')->with('success', 'Katering berhasil ditambahkan.');
    }
PHP;

// Find store method
$storeRegex = '/public function store\(Request \$request\).*?return redirect\(\)->route\(\'admin\.catering\.index\'\)->with\(\'success\', \'Katering berhasil ditambahkan\.\'\);\s*\}/s';
$content = preg_replace($storeRegex, $newStore, $content);

// Replace update method
$newUpdate = <<<PHP
    public function update(Request \$request, LayananKatering \$catering)
    {
        \$isHarian = \$catering->isHarian();
        \$validated = \$request->validate([
            'name' => 'required|string|max:100',
            'minimal_order_days' => \$isHarian ? 'nullable' : 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if (\$isHarian) {
            \$validated['minimal_order_days'] = null;
        }

        // Tipe katering tidak boleh diubah — pertahankan fitur_tersedia yang ada
        \$validated['is_active'] = \$request->boolean('is_active');

        if (\$request->hasFile('image')) {
            if (\$catering->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(\$catering->image);
            }
            \$validated['image'] = \$request->file('image')->store('services', 'public');
        }

        \$catering->update(\$validated);

        if (!\$catering->wasChanged()) {
            return redirect()->route('admin.catering.index');
        }

        return redirect()->route('admin.catering.index')->with('success', 'Katering berhasil diperbarui.');
    }
PHP;

$updateRegex = '/public function update\(Request \$request, LayananKatering \$catering\).*?return redirect\(\)->route\(\'admin\.catering\.index\'\)->with\(\'success\', \'Katering berhasil diperbarui\.\'\);\s*\}/s';
$content = preg_replace($updateRegex, $newUpdate, $content);

file_put_contents($file, $content);
echo "Replaced CateringController methods.";
