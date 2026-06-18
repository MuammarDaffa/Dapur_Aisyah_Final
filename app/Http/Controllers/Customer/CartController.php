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

        // Group event items by cart_group_id
        $eventGroups = $eventCarts->groupBy('cart_group_id');

        $dailySubtotal = $dailyCarts->sum(fn ($c) => $c->subtotal);

        // Active tab dari query param
        $activeTab = $request->get('tab', $dailyCarts->isNotEmpty() ? 'daily' : ($eventGroups->isNotEmpty() ? 'event' : 'daily'));

        return view('customer.cart', compact('dailyCarts', 'eventGroups', 'dailySubtotal', 'activeTab'));
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
                'extras' => empty($extras) ? null : $extras,
            ]);
        }

        return back()->with('success', 'Produk dan opsi berhasil ditambahkan ke keranjang!');
    }

    /**
     * Simpan seluruh konfigurasi event sekaligus sebagai satu group.
     * Mendukung mode Paket dan Custom.
     */
    public function storeEventGroup(Request $request)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'catering_package_id' => 'nullable|exists:catering_packages,id',
            'serving_type_id' => 'nullable|exists:custom_options,id',
            'items' => 'required|array|min:1',
            'items.*.custom_option_id' => 'required|exists:custom_options,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.item_type' => 'required|in:package_item,addition,custom_menu',
        ]);

        $user = auth()->user();
        $groupId = (string) Str::uuid();

        // Jika pakai paket, simpan entry paket dulu
        if (!empty($validated['catering_package_id'])) {
            $user->carts()->create([
                'catering_service_id' => $validated['catering_service_id'],
                'cart_group_id' => $groupId,
                'catering_package_id' => $validated['catering_package_id'],
                'quantity' => 1,
                'item_type' => 'package',
                'serving_type_id' => $validated['serving_type_id'] ?? null,
            ]);
        }

        // Simpan semua item (menu, extra, dll)
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

        return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Pesanan event berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update seluruh konfigurasi event group (dari modal edit).
     */
    public function updateEventGroup(Request $request, string $groupId)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'catering_package_id' => 'nullable|exists:catering_packages,id',
            'serving_type_id' => 'nullable|exists:custom_options,id',
            'items' => 'required|array|min:1',
            'items.*.custom_option_id' => 'required|exists:custom_options,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.item_type' => 'required|in:package_item,addition,custom_menu',
        ]);

        $user = auth()->user();

        // Pastikan group milik user ini
        $existingCount = $user->carts()->where('cart_group_id', $groupId)->count();
        abort_if($existingCount === 0, 404, 'Pesanan event tidak ditemukan.');

        // Hapus semua item lama dalam group
        $user->carts()->where('cart_group_id', $groupId)->delete();

        // Simpan ulang: paket header jika ada
        if (!empty($validated['catering_package_id'])) {
            $user->carts()->create([
                'catering_service_id' => $validated['catering_service_id'],
                'cart_group_id' => $groupId,
                'catering_package_id' => $validated['catering_package_id'],
                'quantity' => 1,
                'item_type' => 'package',
                'serving_type_id' => $validated['serving_type_id'] ?? null,
            ]);
        }

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

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function destroy(Cart $cart)
    {
        // Pastikan cart milik user yang login
        abort_if($cart->user_id !== auth()->id(), 403, 'Akses ditolak.');

        // Jika event item, hapus seluruh group
        if ($cart->cart_group_id) {
            auth()->user()->carts()->where('cart_group_id', $cart->cart_group_id)->delete();
            return redirect()->route('customer.cart', ['tab' => 'event'])->with('success', 'Pesanan event berhasil dihapus dari keranjang.');
        }

        $cart->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
