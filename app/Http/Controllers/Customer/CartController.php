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
        return response()->json([
            'success' => true,
            'cart_count' => $user ? $user->carts()->count() : 0,
        ]);
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
                'cart_count' => $user->carts()->count(),
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

        $service = \App\Models\CateringService::findOrFail($validated['catering_service_id']);
        $user = auth()->user();

        // Validasi: Cek apakah ada item event di keranjang yang belum di-checkout dari layanan (catering_service_id) yang berbeda
        $existingEventCart = $user->carts()
            ->whereNotNull('cart_group_id')
            ->first();

        if ($existingEventCart && (int) $existingEventCart->catering_service_id !== (int) $service->id) {
            $errorMessage = 'Masih ada pesanan Event dari layanan lain yang belum di-checkout. Silakan selesaikan checkout layanan tersebut terlebih dahulu sebelum memesan layanan Event yang berbeda.';
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

            // Simpan entry paket (header)
            $user->carts()->create([
                'catering_service_id' => $service->id,
                'cart_group_id' => $groupId,
                'catering_package_id' => $package->id,
                'quantity' => 1,
                'item_type' => 'package',
                'serving_type_id' => $validated['serving_type_id'] ?? $package->getIncludedServingTypes()->first()?->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Simpan semua menu dari Master Data Menu yang termasuk dalam paket
            foreach ($package->getIncludedMenus() as $menu) {
                $user->carts()->create([
                    'catering_service_id' => $service->id,
                    'cart_group_id' => $groupId,
                    'custom_option_id' => $menu->id,
                    'quantity' => $package->total_portions,
                    'item_type' => 'package_item',
                    'serving_type_id' => $validated['serving_type_id'] ?? $package->getIncludedServingTypes()->first()?->id,
                ]);
            }

            return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Paket katering berhasil ditambahkan ke keranjang!');
        }

        // 2. Jika pesanan berupa Custom Menu
        $minPortion = $service->min_portion;
        $totalCustomPortions = 0;

        foreach ($validated['items'] ?? [] as $item) {
            if (($item['quantity'] ?? 0) > 0 && ($item['item_type'] ?? '') === 'custom_menu') {
                $totalCustomPortions += $item['quantity'];
            }
        }

        if ($totalCustomPortions < $minPortion) {
            return back()->with('error', "Total porsi minimal {$minPortion} porsi.");
        }

        if ($totalCustomPortions > $service->max_portion) {
            return back()->with('error', "Total porsi melebihi batas maksimal ({$service->max_portion} porsi).");
        }

        foreach ($validated['items'] as $item) {
            if (($item['quantity'] ?? 0) > 0) {
                $user->carts()->create([
                    'catering_service_id' => $validated['catering_service_id'],
                    'cart_group_id' => $groupId,
                    'custom_option_id' => $item['custom_option_id'],
                    'quantity' => $item['quantity'],
                    'item_type' => $item['item_type'],
                    'serving_type_id' => $validated['serving_type_id'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan event berhasil ditambahkan ke keranjang!',
                'cart_count' => $user->carts()->count(),
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

        // Jangan izinkan ubah isi pesanan paket katering tetap (fixed package)
        if ($existingGroup->contains('item_type', 'package') || $request->filled('catering_package_id')) {
            return back()->with('error', 'Paket katering tetap (fixed package) tidak dapat diubah isinya. Silakan hapus pesanan ini dan pesan ulang jika ingin memilih paket lain.');
        }

        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'serving_type_id' => 'nullable|exists:custom_options,id',
            'items' => 'required|array|min:1',
            'items.*.custom_option_id' => 'required|exists:custom_options,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.item_type' => 'required|in:addition,custom_menu',
        ]);

        $service = \App\Models\CateringService::findOrFail($validated['catering_service_id']);

        // Validasi: Cek apakah ada item event dari layanan berbeda yang belum di-checkout (selain group ini)
        $existingOtherEventCart = $user->carts()
            ->whereNotNull('cart_group_id')
            ->where('cart_group_id', '!=', $groupId)
            ->first();

        if ($existingOtherEventCart && (int) $existingOtherEventCart->catering_service_id !== (int) $service->id) {
            $errorMessage = 'Masih ada pesanan Event dari layanan lain yang belum di-checkout. Silakan selesaikan checkout layanan tersebut terlebih dahulu sebelum memesan layanan Event yang berbeda.';
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

        if ($totalCustomPortions < $minPortion) {
            return back()->with('error', "Total porsi minimal {$minPortion} porsi.");
        }

        if ($totalCustomPortions > $service->max_portion) {
            return back()->with('error', "Total porsi melebihi batas maksimal ({$service->max_portion} porsi).");
        }

        // Hapus semua item lama dalam group
        $user->carts()->where('cart_group_id', $groupId)->delete();

        // Simpan ulang items
        foreach ($validated['items'] as $item) {
            if ($item['quantity'] > 0) {
                $user->carts()->create([
                    'catering_service_id' => $validated['catering_service_id'],
                    'cart_group_id' => $groupId,
                    'custom_option_id' => $item['custom_option_id'],
                    'quantity' => $item['quantity'],
                    'item_type' => $item['item_type'],
                    'serving_type_id' => $validated['serving_type_id'] ?? null,
                ]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan event berhasil diperbarui!',
                'cart_count' => $user->carts()->count(),
            ]);
        }

        return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Pesanan event berhasil diperbarui!');
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
                'cart_count' => auth()->user()->carts()->count(),
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
                    'cart_count' => auth()->user()->carts()->count(),
                ]);
            }
            return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Pesanan event berhasil dihapus dari keranjang.');
        }

        $cart->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang.',
                'cart_count' => auth()->user()->carts()->count(),
            ]);
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
