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

        // Cek apakah item sudah ada di keranjang
        $existing = $user->carts()
            ->where('product_id', $validated['product_id'] ?? null)
            ->where('custom_option_id', $validated['custom_option_id'] ?? null)
            ->whereNull('cart_group_id')
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + $validated['quantity'],
            ]);
        } else {
            $user->carts()->create($validated);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
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

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->update($validated);

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
