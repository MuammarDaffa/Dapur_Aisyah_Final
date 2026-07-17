<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CustomOption;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check()) {
            Cart::cleanupInvalidAndExpiredItems(auth()->id());
        }

        $carts = auth()->user()->carts()
            ->with(['product.cateringService', 'customOption', 'cateringPackage', 'cateringService', 'servingType'])
            ->get();

        // Pisahkan Daily dan Event
        $dailyCarts = $carts->filter(fn ($c) => $c->isDailyItem());
        $eventCarts = $carts->filter(fn ($c) => $c->isEventItem());

        // Group daily items by menu_date
        $dailyGroups = $dailyCarts->groupBy(fn ($c) => $c->menu_date ? $c->menu_date->format('Y-m-d') : 'unknown');

        // Group event items by cart_group_id
        $eventGroups = $eventCarts->groupBy('cart_group_id');

        // Active tab dari query param
        $activeTab = $request->get('tab', $dailyGroups->isNotEmpty() ? 'daily' : ($eventGroups->isNotEmpty() ? 'event' : 'daily'));

        return view('customer.cart', compact('dailyGroups', 'eventGroups', 'activeTab'));
    }

    public function count(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            Cart::cleanupInvalidAndExpiredItems($user->id);
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
     * Store daily cart item (tidak berubah dari logic lama).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required_without:custom_option_id|exists:products,id',
            'custom_option_id' => 'required_without:product_id|exists:custom_options,id',
            'quantity' => 'required|integer|min:1',
            'menu_date' => 'nullable|date',
            'extras' => 'nullable|array',
            'extras.*.id' => 'exists:custom_options,id',
            'extras.*.qty' => 'integer|min:1',
        ]);

        if (!auth()->check()) {
            session([
                'pending_cart_item' => [
                    'type' => 'daily',
                    'data' => $request->all(),
                ]
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'require_auth' => true,
                    'redirect_url' => route('register'),
                    'message' => 'Silakan registrasi atau login terlebih dahulu untuk memasukkan pesanan ke keranjang.'
                ]);
            }

            return redirect()->route('register')->with('info', 'Silakan registrasi atau login terlebih dahulu untuk memasukkan pesanan ke keranjang.');
        }

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

        // Find existing cart to increment quantity
        $existing = $user->carts()
            ->where('product_id', $validated['product_id'] ?? null)
            ->where('custom_option_id', $validated['custom_option_id'] ?? null)
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
                'quantity' => $existing->quantity + $validated['quantity'],
                'extras' => empty($mergedExtras) ? null : $mergedExtras,
            ]);
        } else {
            $user->carts()->create([
                'product_id' => $validated['product_id'] ?? null,
                'custom_option_id' => $validated['custom_option_id'] ?? null,
                'quantity' => $validated['quantity'],
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
     * Simpan pesanan event group (Paket atau Custom) dari halaman konfigurasi.
     */
    public function storeEventGroup(Request $request)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'catering_package_id' => 'nullable|exists:catering_packages,id',
            'serving_type_id' => 'nullable|exists:custom_options,id',
            'items' => 'required_without:catering_package_id|array',
            'items.*.custom_option_id' => 'required_with:items|exists:custom_options,id',
            'items.*.quantity' => 'required_with:items|integer|min:0',
            'items.*.item_type' => 'required_with:items|in:package_item,addition,custom_menu,package_extra',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (!auth()->check()) {
            session([
                'pending_cart_item' => [
                    'type' => 'event_group',
                    'data' => $request->all(),
                ]
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'require_auth' => true,
                    'redirect_url' => route('register'),
                    'message' => 'Silakan registrasi atau login terlebih dahulu untuk memasukkan pesanan ke keranjang.'
                ]);
            }

            return redirect()->route('register')->with('info', 'Silakan registrasi atau login terlebih dahulu untuk memasukkan pesanan ke keranjang.');
        }

        $service = \App\Models\CateringService::findOrFail($validated['catering_service_id']);
        $user = auth()->user();

        // Validasi: Cek apakah ada item event di keranjang yang belum di-checkout dari layanan (catering_service_id) yang berbeda
        $existingEventCart = $user->carts()
            ->whereNotNull('cart_group_id')
            ->first();

        if ($existingEventCart && (int) $existingEventCart->catering_service_id !== (int) $service->id) {
            $errorMessage = 'Anda masih memiliki pesanan Event yang belum di-checkout. Selesaikan checkout pesanan tersebut terlebih dahulu sebelum memesan layanan Event lainnya.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'is_conflict' => true,
                ], 422);
            }
            return back()->with('event_conflict_error', $errorMessage);
        }

        $groupId = (string) Str::uuid();

        // 1. Jika pesanan berupa Paket Katering Tetap (Fixed Package)
        if (!empty($validated['catering_package_id'])) {
            $package = \App\Models\CateringPackage::with('customOptions')->findOrFail($validated['catering_package_id']);
            if ($package->catering_service_id !== $service->id || !$package->is_active) {
                return back()->with('error', 'Paket tidak valid atau tidak aktif.');
            }

            $servingTypeId = $validated['serving_type_id'] ?? $package->getIncludedServingTypes()->first()?->id;
            $notes = trim((string)($validated['notes'] ?? ''));

            // Check existing package in cart for the same service, package_id, serving_type, notes
            $existingPackageGroup = $user->carts()
                ->where('catering_service_id', $service->id)
                ->where('item_type', 'package')
                ->where('catering_package_id', $package->id)
                ->get()
                ->first(function ($c) use ($servingTypeId, $notes) {
                    return (int)($c->serving_type_id ?? 0) === (int)($servingTypeId ?? 0)
                        && trim((string)($c->notes ?? '')) === $notes;
                });

            if ($existingPackageGroup) {
                $matchedGroupId = $existingPackageGroup->cart_group_id;
                $oldQty = $existingPackageGroup->quantity;
                $newQty = $oldQty + 1;

                $existingPackageGroup->update(['quantity' => $newQty]);

                // Update quantity of all package_item rows belonging to this group
                $user->carts()
                    ->where('cart_group_id', $matchedGroupId)
                    ->where('item_type', 'package_item')
                    ->update(['quantity' => $package->total_portions * $newQty]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Paket katering berhasil ditambahkan ke keranjang!',
                        'cart_count' => $this->getCartCount($user),
                    ]);
                }
                return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Paket katering berhasil ditambahkan ke keranjang!');
            }

            $groupId = (string) Str::uuid();

            // Simpan entry paket (header)
            $user->carts()->create([
                'catering_service_id' => $service->id,
                'cart_group_id' => $groupId,
                'catering_package_id' => $package->id,
                'quantity' => 1,
                'item_type' => 'package',
                'serving_type_id' => $servingTypeId,
                'notes' => $notes ?: null,
            ]);

            // Simpan semua menu dari Master Data Menu yang termasuk dalam paket
            foreach ($package->getIncludedMenus() as $menu) {
                $user->carts()->create([
                    'catering_service_id' => $service->id,
                    'cart_group_id' => $groupId,
                    'custom_option_id' => $menu->id,
                    'quantity' => $package->total_portions,
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
            return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Paket katering berhasil ditambahkan ke keranjang!');
        }

        // 2. Jika pesanan berupa Custom Menu
        // Cek apakah sudah ada item Custom Menu pada layanan ini di keranjang (selain paket)
        $existingCustomCarts = $user->carts()
            ->whereNotNull('cart_group_id')
            ->where('catering_service_id', $service->id)
            ->whereNotIn('item_type', ['package', 'package_item', 'package_extra'])
            ->get();

        $menuMap = [];
        $extraMap = [];
        $groupId = null;
        $existingNotes = null;

        if ($existingCustomCarts->isNotEmpty()) {
            $groupId = $existingCustomCarts->first()->cart_group_id;
            $existingNotes = $existingCustomCarts->firstWhere('notes', '!=', null)?->notes;

            foreach ($existingCustomCarts as $c) {
                if ($c->item_type === 'custom_menu') {
                    $optId = (int) $c->custom_option_id;
                    $menuMap[$optId] = ($menuMap[$optId] ?? 0) + (int) $c->quantity;
                } elseif ($c->item_type === 'addition') {
                    $optId = (int) $c->custom_option_id;
                    $extraMap[$optId] = true;
                }
            }
        } else {
            $groupId = (string) Str::uuid();
        }

        // Gabungkan dengan item yang baru masuk
        foreach ($validated['items'] ?? [] as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty > 0) {
                $type = $item['item_type'] ?? 'custom_menu';
                $optId = (int) $item['custom_option_id'];
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

        if ($totalCustomPortions < $service->min_portion) {
            return back()->with('error', "Total porsi minimal {$service->min_portion} porsi.");
        }

        if ($totalCustomPortions > $service->max_portion) {
            return back()->with('error', "Total porsi melebihi batas maksimal ({$service->max_portion} porsi).");
        }

        // Penyajian -> gunakan pilihan Penyajian yang terakhir dipilih pelanggan sehingga hanya ada satu Penyajian pada Custom Menu.
        $servingTypeId = !empty($validated['serving_type_id']) ? $validated['serving_type_id'] : ($existingCustomCarts->firstWhere('serving_type_id', '!=', null)?->serving_type_id ?? null);
        $notes = trim((string)($validated['notes'] ?? ($existingNotes ?? '')));

        // Hapus item Custom Menu lama pada layanan ini agar tidak terjadi duplikasi atau multi-grup
        if ($existingCustomCarts->isNotEmpty()) {
            $user->carts()->whereIn('id', $existingCustomCarts->pluck('id'))->delete();
        }

        // Buat atau perbarui satu grup Custom Menu dengan data yang sudah digabung
        $user->carts()->create([
            'catering_service_id' => $service->id,
            'cart_group_id' => $groupId,
            'quantity' => 1,
            'item_type' => 'custom_header',
            'serving_type_id' => $servingTypeId,
            'notes' => $notes ?: null,
        ]);

        foreach ($menuMap as $optId => $qty) {
            if ($qty > 0) {
                $user->carts()->create([
                    'catering_service_id' => $service->id,
                    'cart_group_id' => $groupId,
                    'custom_option_id' => $optId,
                    'quantity' => $qty,
                    'item_type' => 'custom_menu',
                    'serving_type_id' => $servingTypeId,
                    'notes' => $notes ?: null,
                ]);
            }
        }

        // Extra yang sama -> tetap satu item dan jumlahnya mengikuti Total Porsi setelah penggabungan.
        foreach (array_keys($extraMap) as $optId) {
            $user->carts()->create([
                'catering_service_id' => $service->id,
                'cart_group_id' => $groupId,
                'custom_option_id' => $optId,
                'quantity' => $totalCustomPortions,
                'item_type' => 'addition',
                'serving_type_id' => $servingTypeId,
                'notes' => $notes ?: null,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan event berhasil ditambahkan ke keranjang!',
                'cart_count' => $this->getCartCount($user),
            ]);
        }

        return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Pesanan event berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update seluruh konfigurasi event group (dari modal edit).
     */
    public function updateEventGroup(Request $request, string $groupId)
    {
        $user = auth()->user();

        // Pastikan group milik user ini
        $existingGroup = $user->carts()->where('cart_group_id', $groupId)->get();
        abort_if($existingGroup->isEmpty(), 404, 'Pesanan event tidak ditemukan.');

        // 1. Jika edit untuk Paket Katering Tetap (Fixed Package)
        if ($existingGroup->contains('item_type', 'package')) {
            $validatedPkg = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);
            $newQty = (int) $validatedPkg['quantity'];

            $packageHeader = $existingGroup->firstWhere('item_type', 'package');
            if (!$packageHeader || !$packageHeader->cateringPackage) {
                abort(404, 'Paket tidak ditemukan');
            }

            $package = $packageHeader->cateringPackage;
            $service = $packageHeader->cateringService;
            $totalPortions = $package->total_portions * $newQty;

            $packageHeader->update(['quantity' => $newQty]);
            $user->carts()
                ->where('cart_group_id', $groupId)
                ->where('item_type', 'package_item')
                ->update(['quantity' => $package->total_portions * $newQty]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paket berhasil diperbarui.',
                    'cart_count' => $this->getCartCount($user),
                ]);
            }
            return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Paket berhasil diperbarui.');
        }

        // 2. Jika edit untuk Custom Menu
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'serving_type_id' => 'nullable|exists:custom_options,id',
            'sets_quantity' => 'nullable|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.custom_option_id' => 'required|exists:custom_options,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.item_type' => 'required|in:addition,custom_menu',
        ]);

        $service = \App\Models\CateringService::findOrFail($validated['catering_service_id']);

        $existingOtherEventCart = $user->carts()
            ->whereNotNull('cart_group_id')
            ->where('cart_group_id', '!=', $groupId)
            ->first();

        if ($existingOtherEventCart && (int) $existingOtherEventCart->catering_service_id !== (int) $service->id) {
            $errorMessage = 'Anda masih memiliki pesanan Event yang belum di-checkout. Selesaikan checkout pesanan tersebut terlebih dahulu sebelum memesan layanan Event lainnya.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'is_conflict' => true,
                ], 422);
            }
            return back()->with('event_conflict_error', $errorMessage);
        }

        $minPortion = $service->min_portion;
        $totalCustomPortions = 0;

        foreach ($validated['items'] as $item) {
            if (($item['quantity'] ?? 0) > 0 && ($item['item_type'] ?? '') === 'custom_menu') {
                $totalCustomPortions += $item['quantity'];
            }
        }

        if ($totalCustomPortions <= 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Silakan pilih minimal 1 porsi menu."], 422);
            }
            return back()->with('error', "Silakan pilih minimal 1 porsi menu.");
        }

        if ($totalCustomPortions > $service->max_portion) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Total porsi melebihi batas maksimal ({$service->max_portion} porsi)."], 422);
            }
            return back()->with('error', "Total porsi melebihi batas maksimal ({$service->max_portion} porsi).");
        }

        $setsQty = max(1, (int) ($validated['sets_quantity'] ?? 1));
        $servingTypeId = $validated['serving_type_id'] ?? null;
        $notes = $existingGroup->firstWhere('item_type', 'custom_header')?->notes ?? ($existingGroup->first()->notes ?? null);

        // Hapus semua item Custom Menu untuk layanan ini agar dipastikan hanya ada 1 grup Custom Menu di keranjang
        $user->carts()
            ->whereNotNull('cart_group_id')
            ->where('catering_service_id', $service->id)
            ->whereNotIn('item_type', ['package', 'package_item', 'package_extra'])
            ->delete();

        // Simpan custom_header
        $user->carts()->create([
            'catering_service_id' => $validated['catering_service_id'],
            'cart_group_id' => $groupId,
            'quantity' => 1,
            'item_type' => 'custom_header',
            'serving_type_id' => $servingTypeId,
            'notes' => $notes ?: null,
        ]);

        foreach ($validated['items'] as $item) {
            if (($item['quantity'] ?? 0) > 0) {
                $type = $item['item_type'] ?? 'custom_menu';
                $qty = $type === 'addition' ? $totalCustomPortions : (int) $item['quantity'];
                $user->carts()->create([
                    'catering_service_id' => $validated['catering_service_id'],
                    'cart_group_id' => $groupId,
                    'custom_option_id' => $item['custom_option_id'],
                    'quantity' => $qty,
                    'item_type' => $type,
                    'serving_type_id' => $servingTypeId,
                    'notes' => $notes ?: null,
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

        return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Custom menu berhasil diperbarui.');
    }

    /**
     * Update daily cart item (tidak berubah).
     */
    public function update(Request $request, Cart $cart)
    {
        // Pastikan cart milik user yang login
        abort_if($cart->user_id !== auth()->id(), 403, 'Akses ditolak.');

        if ($cart->cart_group_id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Item paket event tidak dapat diubah secara individual.'], 400);
            }
            return back()->with('error', 'Item paket event tidak dapat diubah secara individual.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'extras' => 'nullable|array',
            'extras.*.id' => 'exists:custom_options,id',
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
        $cart->update([
            'quantity' => $validated['quantity'],
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

    public function destroy(Cart $cart)
    {
        // Pastikan cart milik user yang login
        abort_if($cart->user_id !== auth()->id(), 403, 'Akses ditolak.');

        // Jika event item, hapus seluruh group
        if ($cart->cart_group_id) {
            auth()->user()->carts()->where('cart_group_id', $cart->cart_group_id)->delete();
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan event berhasil dihapus dari keranjang.',
                    'cart_count' => $this->getCartCount(auth()->user()),
                ]);
            }
            return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Pesanan event berhasil dihapus dari keranjang.');
        }

        $cart->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang.',
                'cart_count' => $this->getCartCount(auth()->user()),
            ]);
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Memulihkan pesanan yang disimpan di session sebelum login / registrasi.
     */
    public static function restorePendingCart(Request $request)
    {
        if (!$request->session()->has('pending_cart_item') || !auth()->check()) {
            return null;
        }

        $pending = $request->session()->pull('pending_cart_item');
        if (!is_array($pending) || empty($pending['type']) || empty($pending['data'])) {
            return null;
        }

        $controller = app(self::class);
        $req = Request::create('/', 'POST', $pending['data']);
        $req->headers->set('X-Requested-With', ''); // Non-ajax

        if ($pending['type'] === 'daily') {
            $controller->store($req);
            Cart::cleanupInvalidAndExpiredItems(auth()->id());
            return redirect()->route('customer.cart', ['tab' => 'daily'])->with('success', 'Produk dan opsi berhasil ditambahkan ke keranjang!');
        } elseif ($pending['type'] === 'event_group') {
            $controller->storeEventGroup($req);
            Cart::cleanupInvalidAndExpiredItems(auth()->id());
            return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Pesanan event berhasil ditambahkan ke keranjang!');
        }

        return null;
    }
}
