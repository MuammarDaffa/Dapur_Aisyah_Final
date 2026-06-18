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
    public function index()
    {
        $carts = auth()->user()->carts()
            ->with(['product.cateringService', 'customOption', 'cateringPackage'])
            ->get();

        // Group event items by cart_group_id
        $groupedCarts = $carts->groupBy(function ($cart) {
            return $cart->cart_group_id ?? 'ungrouped_' . $cart->id;
        });

        $subtotal = $carts->sum(fn ($cart) => $cart->subtotal);

        return view('customer.cart', compact('carts', 'groupedCarts', 'subtotal'));
    }

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

        // CartGuard: cegah mencampur Harian dan Event
        $existingCarts = $user->carts()->get();
        if ($existingCarts->isNotEmpty()) {
            $hasEventItems = $existingCarts->contains(fn ($c) => $c->isEventItem());
            if ($hasEventItems) {
                return back()->with('error', 'Selesaikan atau hapus pesanan event yang sedang dibuat terlebih dahulu.');
            }
        }

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
        $extrasJson = empty($extras) ? null : json_encode($extras);

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
     */
    public function storeEventGroup(Request $request)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'catering_package_id' => 'nullable|exists:catering_packages,id',
            'items' => 'required|array|min:1',
            'items.*.custom_option_id' => 'required|exists:custom_options,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.item_type' => 'required|in:package_item,addition',
        ]);

        $user = auth()->user();

        // CartGuard: cegah mencampur Harian dan Event atau multiple Event
        $existingCarts = $user->carts()->get();
        if ($existingCarts->isNotEmpty()) {
            $hasDailyItems = $existingCarts->contains(fn ($c) => !$c->isEventItem());
            if ($hasDailyItems) {
                return back()->with('error', 'Selesaikan checkout pesanan harian terlebih dahulu.');
            }

            $hasEventItems = $existingCarts->contains(fn ($c) => $c->isEventItem());
            if ($hasEventItems) {
                return back()->with('error', 'Selesaikan atau hapus pesanan event yang sedang dibuat terlebih dahulu.');
            }
        }

        $groupId = (string) Str::uuid();

        // Jika pakai paket, simpan entry paket dulu
        if (!empty($validated['catering_package_id'])) {
            $user->carts()->create([
                'cart_group_id' => $groupId,
                'catering_package_id' => $validated['catering_package_id'],
                'quantity' => 1,
                'item_type' => 'package',
            ]);
        }

        // Simpan semua item (menu, dekorasi, penyajian, extra)
        foreach ($validated['items'] as $item) {
            $user->carts()->create([
                'cart_group_id' => $groupId,
                'custom_option_id' => $item['custom_option_id'],
                'quantity' => $item['quantity'],
                'item_type' => $item['item_type'],
            ]);
        }

        return redirect()->route('customer.cart')->with('success', 'Konfigurasi event berhasil ditambahkan ke keranjang!');
    }

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
            return back()->with('success', 'Pesanan event berhasil dihapus dari keranjang.');
        }

        $cart->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
