<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\OpsiKustom;
use App\Models\MenuHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KeranjangController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check()) {
            Keranjang::cleanupInvalidAndExpiredItems(auth()->id());
        }

        $keranjang = auth()->user()->keranjang()
            ->with(['menuHarian.layananKatering', 'opsiKustom', 'paketKatering', 'layananKatering', 'servingType'])
            ->get();

        // Pisahkan Daily dan Acara
        $keranjangHarian = $keranjang->filter(fn ($c) => $c->isHarianItem());
        $keranjangAcara = $keranjang->filter(fn ($c) => $c->isAcaraItem());

        // Group harian items by menu_date
        $grupHarian = $keranjangHarian->groupBy(fn ($c) => $c->menu_date ? $c->menu_date->format('Y-m-d') : 'unknown');

        // Group acara items by cart_group_id
        $grupAcara = $keranjangAcara->groupBy('cart_group_id');

        // Active tab dari query param
        $activeTab = $request->get('tab', $grupHarian->isNotEmpty() ? 'harian' : ($grupAcara->isNotEmpty() ? 'acara' : 'harian'));

        return view('pelanggan.keranjang', compact('harianGroups', 'acaraGroups', 'activeTab'));
    }

    public function count(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            Keranjang::cleanupInvalidAndExpiredItems($user->id, false);
        }
        return response()->json([
            'success' => true,
            'cart_count' => $user ? $this->getCartCount($user) : 0,
        ]);
    }

    private function getCartCount($user): int
    {
        if (!$user) return 0;
        return $user->cartItemsCount();
    }

    /**
     * Store harian keranjang item (tidak berubah dari logic lama).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_harian_id' => 'required_without:opsi_kustom_id|exists:menu_harian,id',
            'opsi_kustom_id' => 'required_without:menu_harian_id|exists:opsi_kustom,id',
            'jumlah' => 'required|integer|min:1',
            'menu_date' => 'nullable|date',
            'extras' => 'nullable|array',
            'extras.*.id' => 'exists:opsi_kustom,id',
            'extras.*.qty' => 'integer|min:1',
        ]);



        $user = auth()->user();

        // Clean and Sort extras array so identical selections match in JSON string comparison
        $extras = [];
        if (!empty($validated['extras'])) {
            foreach ($validated['extras'] as $extra) {
                if (!empty($extra['id']) && !empty($extra['qty']) && $extra['qty'] > 0) {
                    $extras[] = [
                        'id' => (int) $extra['id'],
                        'qty' => (int) $extra['qty']
                    ];
                }
            }
            // Sort by id for deterministic JSON
            usort($extras, fn($a, $b) => $a['id'] <=> $b['id']);
        }

        // Find existing keranjang to increment jumlah
        $existing = $user->keranjang()
            ->where('menu_harian_id', $validated['menu_harian_id'] ?? null)
            ->where('opsi_kustom_id', $validated['opsi_kustom_id'] ?? null)
            ->where('menu_date', $validated['menu_date'] ?? null)
            ->whereNull('cart_group_id')
            ->first();

        if ($existing) {
            $existingExtras = $existing->extras ?? [];
            $mergedExtrasMap = [];
            
            // Masukkan ekstra yang sudah ada
            foreach ($existingExtras as $ex) {
                $mergedExtrasMap[$ex['id']] = $ex['qty'];
            }
            
            // Tambahkan ekstra baru
            foreach ($extras as $ex) {
                if (isset($mergedExtrasMap[$ex['id']])) {
                    $mergedExtrasMap[$ex['id']] += $ex['qty'];
                } else {
                    $mergedExtrasMap[$ex['id']] = $ex['qty'];
                }
            }
            
            $mergedExtras = [];
            foreach ($mergedExtrasMap as $id => $qty) {
                $mergedExtras[] = ['id' => $id, 'qty' => $qty];
            }
            usort($mergedExtras, fn($a, $b) => $a['id'] <=> $b['id']);

            $existing->update([
                'jumlah' => $existing->jumlah + $validated['jumlah'],
                'extras' => empty($mergedExtras) ? null : $mergedExtras,
            ]);
        } else {
            $user->keranjang()->create([
                'menu_harian_id' => $validated['menu_harian_id'] ?? null,
                'opsi_kustom_id' => $validated['opsi_kustom_id'] ?? null,
                'jumlah' => $validated['jumlah'],
                'menu_date' => $validated['menu_date'] ?? null,
                'extras' => empty($extras) ? null : $extras,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk dan opsi berhasil ditambahkan ke keranjang!',
                'cart_count' => $this->getCartCount($user),
            ]);
        }

        return back()->with('success', 'Produk dan opsi berhasil ditambahkan ke keranjang!');
    }

    /**
     * Simpan pesanan acara group (Paket atau Custom) dari halaman konfigurasi.
     */
    public function storeGrupAcara(Request $request)
    {
        if ($request->has('items') && is_array($request->input('items'))) {
            $cleanItems = array_values(array_filter($request->input('items'), function ($item) {
                return is_array($item) && isset($item['opsi_kustom_id']) && isset($item['jumlah']) && (int) $item['jumlah'] > 0;
            }));
            $request->merge(['items' => $cleanItems]);
        }

        $validated = $request->validate([
            'layanan_katering_id' => 'required|exists:layanan_katering,id',
            'catering_package_id' => 'nullable|exists:paket_katering,id',
            'serving_type_id' => 'nullable|exists:opsi_kustom,id',
            'items' => 'required_without:catering_package_id|array',
            'items.*.opsi_kustom_id' => 'required_with:items|exists:opsi_kustom,id',
            'items.*.jumlah' => 'required_with:items|integer|min:0',
            'items.*.item_type' => 'required_with:items|in:package_item,addition,custom_menu,package_extra',
            'catatan' => 'nullable|string|max:1000',
        ]);



        $service = \App\Models\LayananKatering::findOrFail($validated['layanan_katering_id']);
        $user = auth()->user();

        // Validasi: Cek apakah ada item acara di keranjang yang belum di-checkout dari layanan (layanan_katering_id) yang berbeda
        $existingAcaraCart = $user->keranjang()
            ->whereNotNull('cart_group_id')
            ->first();

        if ($existingAcaraCart && (int) $existingAcaraCart->layanan_katering_id !== (int) $service->id) {
            $errorMessage = 'Anda masih memiliki pesanan Acara yang belum di-checkout. Selesaikan checkout pesanan tersebut terlebih dahulu sebelum memesan layanan Acara lainnya.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'is_conflict' => true,
                ], 422);
            }
            return back()->with('acara_conflict_error', $errorMessage);
        }

        $groupId = (string) Str::uuid();

        // 1. Jika pesanan berupa Paket Katering Tetap (Fixed Paket Katering)
        if (!empty($validated['catering_package_id'])) {
            $paket = \App\Models\PaketKatering::with('opsiKustom')->findOrFail($validated['catering_package_id']);
            if ($paket->layanan_katering_id !== $service->id || !$paket->is_active) {
                return back()->with('error', 'Paket tidak valid atau tidak aktif.');
            }

            $servingTypeId = $validated['serving_type_id'] ?? $paket->getIncludedServingTypes()->first()?->id;
            $catatan = trim((string)($validated['catatan'] ?? ''));

            // Check existing package in keranjang for the same service, paket_katering_id, tipe_penyajian, catatan
            $existingPackageGroup = $user->keranjang()
                ->where('layanan_katering_id', $service->id)
                ->where('item_type', 'package')
                ->where('catering_package_id', $paket->id)
                ->get()
                ->first(function ($c) use ($servingTypeId, $catatan) {
                    return (int)($c->serving_type_id ?? 0) === (int)($servingTypeId ?? 0)
                        && trim((string)($c->catatan ?? '')) === $catatan;
                });

            if ($existingPackageGroup) {
                $matchedGroupId = $existingPackageGroup->cart_group_id;
                $oldQty = $existingPackageGroup->jumlah;
                $newQty = $oldQty + 1;

                $existingPackageGroup->update(['jumlah' => $newQty]);

                // Update jumlah of all package_item rows belonging to this group
                $user->keranjang()
                    ->where('cart_group_id', $matchedGroupId)
                    ->where('item_type', 'package_item')
                    ->update(['jumlah' => $paket->total_portions * $newQty]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Paket katering berhasil ditambahkan ke keranjang!',
                        'cart_count' => $this->getCartCount($user),
                    ]);
                }
                return redirect()->route('pelanggan.keranjang', ['tab' => 'acara'])->with('success', 'Paket katering berhasil ditambahkan ke keranjang!');
            }

            $groupId = (string) Str::uuid();

            // Simpan entry paket (header)
            $user->keranjang()->create([
                'layanan_katering_id' => $service->id,
                'cart_group_id' => $groupId,
                'catering_package_id' => $paket->id,
                'jumlah' => 1,
                'item_type' => 'package',
                'serving_type_id' => $servingTypeId,
                'catatan' => $catatan ?: null,
            ]);

            // Simpan semua menu dari Master Data Menu yang termasuk dalam paket
            foreach ($paket->getIncludedMenus() as $menu) {
                $user->keranjang()->create([
                    'layanan_katering_id' => $service->id,
                    'cart_group_id' => $groupId,
                    'opsi_kustom_id' => $menu->id,
                    'jumlah' => $paket->total_portions,
                    'item_type' => 'package_item',
                    'serving_type_id' => $servingTypeId,
                ]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paket katering berhasil ditambahkan ke keranjang!',
                    'cart_count' => $this->getCartCount($user),
                ]);
            }
            return redirect()->route('pelanggan.keranjang', ['tab' => 'acara'])->with('success', 'Paket katering berhasil ditambahkan ke keranjang!');
        }

        // 2. Jika pesanan berupa Custom Menu
        // Cek apakah sudah ada item Custom Menu pada layanan ini di keranjang (selain paket)
        $existingCustomCarts = $user->keranjang()
            ->whereNotNull('cart_group_id')
            ->where('layanan_katering_id', $service->id)
            ->whereNotIn('item_type', ['package', 'package_item', 'package_extra'])
            ->get();

        $menuMap = [];
        $extraMap = [];
        $groupId = null;
        $existingNotes = null;

        if ($existingCustomCarts->isNotEmpty()) {
            $groupId = $existingCustomCarts->first()->cart_group_id;
            $existingNotes = $existingCustomCarts->firstWhere('catatan', '!=', null)?->catatan;

            foreach ($existingCustomCarts as $c) {
                if ($c->item_type === 'custom_menu') {
                    $optId = (int) $c->opsi_kustom_id;
                    $menuMap[$optId] = ($menuMap[$optId] ?? 0) + (int) $c->jumlah;
                } elseif ($c->item_type === 'addition') {
                    $optId = (int) $c->opsi_kustom_id;
                    $extraMap[$optId] = true;
                }
            }
        } else {
            $groupId = (string) Str::uuid();
        }

        // Gabungkan dengan item yang baru masuk
        foreach ($validated['items'] ?? [] as $item) {
            $qty = (int) ($item['jumlah'] ?? 0);
            if ($qty > 0) {
                $type = $item['item_type'] ?? 'custom_menu';
                $optId = (int) $item['opsi_kustom_id'];
                if ($type === 'custom_menu') {
                    // Menu yang sama -> jumlah porsinya dijumlahkan. Menu yang berbeda -> tambahkan ke daftar menu.
                    $menuMap[$optId] = ($menuMap[$optId] ?? 0) + $qty;
                } elseif ($type === 'addition') {
                    // Extra yang berbeda -> tambahkan ke daftar Extra. Extra yang sama -> tetap satu item.
                    $extraMap[$optId] = true;
                }
            }
        }

        // Hitung total porsi setelah penggabungan
        $totalCustomPortions = array_sum($menuMap);



        // Penyajian -> gunakan pilihan Penyajian yang terakhir dipilih pelanggan sehingga hanya ada satu Penyajian pada Custom Menu.
        $servingTypeId = !empty($validated['serving_type_id']) ? $validated['serving_type_id'] : ($existingCustomCarts->firstWhere('serving_type_id', '!=', null)?->serving_type_id ?? null);
        $catatan = trim((string)($validated['catatan'] ?? ($existingNotes ?? '')));

        // Hapus item Custom Menu lama pada layanan ini agar tidak terjadi duplikasi atau multi-grup
        if ($existingCustomCarts->isNotEmpty()) {
            $user->keranjang()->whereIn('id', $existingCustomCarts->pluck('id'))->delete();
        }

        // Buat atau perbarui satu grup Custom Menu dengan data yang sudah digabung
        $user->keranjang()->create([
            'layanan_katering_id' => $service->id,
            'cart_group_id' => $groupId,
            'jumlah' => 1,
            'item_type' => 'custom_header',
            'serving_type_id' => $servingTypeId,
            'catatan' => $catatan ?: null,
        ]);

        foreach ($menuMap as $optId => $qty) {
            if ($qty > 0) {
                $user->keranjang()->create([
                    'layanan_katering_id' => $service->id,
                    'cart_group_id' => $groupId,
                    'opsi_kustom_id' => $optId,
                    'jumlah' => $qty,
                    'item_type' => 'custom_menu',
                    'serving_type_id' => $servingTypeId,
                    'catatan' => $catatan ?: null,
                ]);
            }
        }

        // Extra yang sama -> tetap satu item dan jumlahnya mengikuti Total Porsi setelah penggabungan.
        foreach (array_keys($extraMap) as $optId) {
            $user->keranjang()->create([
                'layanan_katering_id' => $service->id,
                'cart_group_id' => $groupId,
                'opsi_kustom_id' => $optId,
                'jumlah' => $totalCustomPortions,
                'item_type' => 'addition',
                'serving_type_id' => $servingTypeId,
                'catatan' => $catatan ?: null,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan acara berhasil ditambahkan ke keranjang!',
                'cart_count' => $this->getCartCount($user),
            ]);
        }

        return redirect()->route('pelanggan.keranjang', ['tab' => 'acara'])->with('success', 'Pesanan acara berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update seluruh konfigurasi acara group (dari modal edit).
     */
    public function updateAcaraGroup(Request $request, string $groupId)
    {
        $user = auth()->user();

        // Pastikan group milik user ini
        $existingGroup = $user->keranjang()->where('cart_group_id', $groupId)->get();
        abort_if($existingGroup->isEmpty(), 404, 'Pesanan acara tidak ditemukan.');

        // 1. Jika edit untuk Paket Katering Tetap (Fixed Paket Katering)
        if ($existingGroup->contains('item_type', 'package')) {
            $validatedPkg = $request->validate([
                'jumlah' => 'required|integer|min:1',
            ]);
            $newQty = (int) $validatedPkg['jumlah'];

            $packageHeader = $existingGroup->firstWhere('item_type', 'package');
            if (!$packageHeader || !$packageHeader->paketKatering) {
                abort(404, 'Paket tidak ditemukan');
            }

            $paket = $packageHeader->paketKatering;
            $service = $packageHeader->layananKatering;
            $totalPortions = $paket->total_portions * $newQty;

            $packageHeader->update(['jumlah' => $newQty]);
            $user->keranjang()
                ->where('cart_group_id', $groupId)
                ->where('item_type', 'package_item')
                ->update(['jumlah' => $paket->total_portions * $newQty]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paket berhasil diperbarui.',
                    'cart_count' => $this->getCartCount($user),
                ]);
            }
            return redirect()->route('pelanggan.keranjang', ['tab' => 'acara'])->with('success', 'Paket berhasil diperbarui.');
        }

        // 2. Jika edit untuk Custom Menu
        if ($request->has('items') && is_array($request->input('items'))) {
            $cleanItems = array_values(array_filter($request->input('items'), function ($item) {
                return is_array($item) && isset($item['opsi_kustom_id']) && isset($item['jumlah']) && (int) $item['jumlah'] > 0;
            }));
            $request->merge(['items' => $cleanItems]);
        }

        $validated = $request->validate([
            'layanan_katering_id' => 'required|exists:layanan_katering,id',
            'serving_type_id' => 'nullable|exists:opsi_kustom,id',
            'sets_quantity' => 'nullable|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.opsi_kustom_id' => 'required|exists:opsi_kustom,id',
            'items.*.jumlah' => 'required|integer|min:0',
            'items.*.item_type' => 'required|in:addition,custom_menu',
        ]);

        $service = \App\Models\LayananKatering::findOrFail($validated['layanan_katering_id']);

        $existingOtherAcaraCart = $user->keranjang()
            ->whereNotNull('cart_group_id')
            ->where('cart_group_id', '!=', $groupId)
            ->first();

        if ($existingOtherAcaraCart && (int) $existingOtherAcaraCart->layanan_katering_id !== (int) $service->id) {
            $errorMessage = 'Anda masih memiliki pesanan Acara yang belum di-checkout. Selesaikan checkout pesanan tersebut terlebih dahulu sebelum memesan layanan Acara lainnya.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'is_conflict' => true,
                ], 422);
            }
            return back()->with('acara_conflict_error', $errorMessage);
        }


        $totalCustomPortions = 0;

        foreach ($validated['items'] as $item) {
            if (($item['jumlah'] ?? 0) > 0 && ($item['item_type'] ?? '') === 'custom_menu') {
                $totalCustomPortions += $item['jumlah'];
            }
        }



        $setsQty = max(1, (int) ($validated['sets_quantity'] ?? 1));
        $servingTypeId = $validated['serving_type_id'] ?? null;
        $catatan = $existingGroup->firstWhere('item_type', 'custom_header')?->catatan ?? ($existingGroup->first()->catatan ?? null);

        // Hapus semua item Custom Menu untuk layanan ini agar dipastikan hanya ada 1 grup Custom Menu di keranjang
        $user->keranjang()
            ->whereNotNull('cart_group_id')
            ->where('layanan_katering_id', $service->id)
            ->whereNotIn('item_type', ['package', 'package_item', 'package_extra'])
            ->delete();

        // Simpan custom_header
        $user->keranjang()->create([
            'layanan_katering_id' => $validated['layanan_katering_id'],
            'cart_group_id' => $groupId,
            'jumlah' => 1,
            'item_type' => 'custom_header',
            'serving_type_id' => $servingTypeId,
            'catatan' => $catatan ?: null,
        ]);

        foreach ($validated['items'] as $item) {
            if (($item['jumlah'] ?? 0) > 0) {
                $type = $item['item_type'] ?? 'custom_menu';
                $qty = $type === 'addition' ? $totalCustomPortions : (int) $item['jumlah'];
                $user->keranjang()->create([
                    'layanan_katering_id' => $validated['layanan_katering_id'],
                    'cart_group_id' => $groupId,
                    'opsi_kustom_id' => $item['opsi_kustom_id'],
                    'jumlah' => $qty,
                    'item_type' => $type,
                    'serving_type_id' => $servingTypeId,
                    'catatan' => $catatan ?: null,
                ]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Custom menu berhasil diperbarui.',
                'cart_count' => $this->getCartCount($user),
            ]);
        }

        return redirect()->route('pelanggan.keranjang', ['tab' => 'acara'])->with('success', 'Custom menu berhasil diperbarui.');
    }

    /**
     * Update harian keranjang item (tidak berubah).
     */
    public function update(Request $request, Keranjang $keranjang)
    {
        // Pastikan keranjang milik user yang login
        abort_if($keranjang->user_id !== auth()->id(), 403, 'Akses ditolak.');

        if ($keranjang->cart_group_id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Item paket acara tidak dapat diubah secara individual.'], 400);
            }
            return back()->with('error', 'Item paket acara tidak dapat diubah secara individual.');
        }

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
            'extras' => 'nullable|array',
            'extras.*.id' => 'exists:opsi_kustom,id',
            'extras.*.qty' => 'integer|min:1',
        ]);

        $extras = [];
        if (!empty($validated['extras'])) {
            foreach ($validated['extras'] as $extra) {
                if (!empty($extra['id']) && !empty($extra['qty']) && $extra['qty'] > 0) {
                    $extras[] = [
                        'id' => (int) $extra['id'],
                        'qty' => (int) $extra['qty']
                    ];
                }
            }
            usort($extras, fn($a, $b) => $a['id'] <=> $b['id']);
        }
        $keranjang->update([
            'jumlah' => $validated['jumlah'],
            'extras' => empty($extras) ? null : $extras,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diperbarui.',
                'cart_count' => $this->getCartCount(auth()->user()),
            ]);
        }

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function destroy(Keranjang $keranjang)
    {
        // Pastikan keranjang milik user yang login
        abort_if($keranjang->user_id !== auth()->id(), 403, 'Akses ditolak.');

        // Jika acara item, hapus seluruh group
        if ($keranjang->cart_group_id) {
            auth()->user()->keranjang()->where('cart_group_id', $keranjang->cart_group_id)->delete();
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan acara berhasil dihapus dari keranjang.',
                    'cart_count' => $this->getCartCount(auth()->user()),
                ]);
            }
            return redirect()->route('pelanggan.keranjang', ['tab' => 'acara'])->with('success', 'Pesanan acara berhasil dihapus dari keranjang.');
        }

        $keranjang->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang.',
                'cart_count' => $this->getCartCount(auth()->user()),
            ]);
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }}
