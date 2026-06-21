<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingCost;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Checkout page untuk Daily.
     */
    public function index($menu_date = null)
    {
        $user = auth()->user();
        $cartsQuery = $user->carts()
            ->whereNull('cart_group_id') // Hanya daily
            ->with(['product.cateringService', 'customOption', 'cateringPackage']);
            
        if ($menu_date && $menu_date !== 'unknown') {
            $cartsQuery->whereDate('menu_date', $menu_date);
        } else {
            $cartsQuery->whereNull('menu_date');
        }
        
        $carts = $cartsQuery->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart')
                ->with('error', 'Keranjang harian kosong atau tanggal tidak valid.');
        }

        // Group (untuk daily, setiap item = 1 group)
        $groupedCarts = $carts->groupBy(function ($cart) {
            return 'ungrouped_' . $cart->id;
        });

        $districts = District::with('villages')->get();
        $subtotal = $carts->sum(fn ($cart) => $cart->subtotal);
        
        $orderDate = $menu_date && $menu_date !== 'unknown' ? $menu_date : date('Y-m-d');
        
        // Pass cutoff data if available
        $firstCart = $carts->first();
        $service = $firstCart->product ? $firstCart->product->cateringService : ($firstCart->customOption ? $firstCart->customOption->cateringService : null);

        return view('customer.checkout', compact('carts', 'groupedCarts', 'districts', 'subtotal', 'user', 'orderDate', 'menu_date', 'service'));
    }

    /**
     * Store daily checkout.
     */
    public function store(CheckoutRequest $request, $menu_date = null)
    {
        $validated = $request->validated();

        $user = auth()->user();
        $cartsQuery = $user->carts()
            ->whereNull('cart_group_id') // Hanya daily
            ->with(['product.cateringService', 'customOption', 'cateringPackage']);
            
        if ($menu_date && $menu_date !== 'unknown') {
            $cartsQuery->whereDate('menu_date', $menu_date);
        } else {
            $cartsQuery->whereNull('menu_date');
        }
        
        $carts = $cartsQuery->get();

        if ($carts->isEmpty()) {
            return back()->with('error', 'Keranjang belanja kosong atau tanggal tidak valid.');
        }

        // Tentukan catering service dan package
        $firstCart = $carts->first();
        $cateringServiceId = null;
        $packageId = null;

        if ($firstCart->product) {
            $cateringServiceId = $firstCart->product->catering_service_id;
        } elseif ($firstCart->customOption) {
            $cateringServiceId = $firstCart->customOption->catering_service_id;
        }

        // Cek apakah ada paket dalam keranjang
        $packageCart = $carts->firstWhere('item_type', 'package');
        if ($packageCart && $packageCart->cateringPackage) {
            $packageId = $packageCart->catering_package_id;
            $cateringServiceId = $packageCart->cateringPackage->catering_service_id;
        }

        // Validasi tanggal pemesanan berdasarkan jenis layanan
        OrderService::validateOrderDate($validated['order_date'], $cateringServiceId);

        return DB::transaction(function () use ($validated, $user, $carts, $cateringServiceId, $packageId) {
            // Hitung biaya
            $subtotal = $carts->sum(fn ($cart) => $cart->subtotal);
            $shippingCost = $validated['pickup_method'] === 'delivery'
                ? ShippingCost::getCostByDistrict($validated['district_id'])
                : 0;
            $total = $subtotal + $shippingCost;

            // Buat order
            $order = Order::create([
                'order_number' => OrderService::generateOrderNumber(),
                'user_id' => $user->id,
                'catering_service_id' => $cateringServiceId,
                'package_id' => $packageId,
                'order_date' => $validated['order_date'],
                'pickup_method' => $validated['pickup_method'],
                'district_id' => $validated['district_id'] ?? null,
                'village_id' => $validated['village_id'] ?? null,
                'address_detail' => $validated['address_detail'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'serving_type' => $validated['serving_type'] ?? null,
                'portion' => $validated['portion'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => 'transfer',
                'payment_status' => 'unpaid',
                'status' => 'pending_payment',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Buat order items
            foreach ($carts as $cart) {
                // Skip package entry (hanya marker, bukan item fisik)
                if ($cart->item_type === 'package') {
                    continue;
                }

                // Tentukan harga snapshot
                $unitPrice = 0;
                $itemName = 'Item';

                if ($cart->product) {
                    $unitPrice = (float) $cart->product->price;
                    $itemName = $cart->product->name;
                } elseif ($cart->customOption) {
                    // Package items → price 0 (sudah termasuk harga paket)
                    $unitPrice = $cart->item_type === 'package_item'
                        ? 0
                        : (float) $cart->customOption->price;
                    $itemName = $cart->customOption->name;
                }

                $extrasPrice = 0;
                if (!empty($cart->extras)) {
                    $extraIds = array_column($cart->extras, 'id');
                    $options = \App\Models\CustomOption::whereIn('id', $extraIds)->get()->keyBy('id');
                    $extraNames = [];
                    foreach ($cart->extras as $extraData) {
                        if ($opt = $options->get($extraData['id'])) {
                            $exPrice = (float) $opt->price * $extraData['qty'];
                            $extrasPrice += $exPrice;
                            $extraNames[] = $opt->name . ' ' . $extraData['qty'] . 'x (+Rp' . number_format($exPrice, 0, ',', '.') . ')';
                        }
                    }
                    if (!empty($extraNames)) {
                        $itemName .= " (Extra: " . implode(', ', $extraNames) . ")";
                    }
                }

                $subtotal = ($unitPrice * $cart->quantity) + $extrasPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'custom_option_id' => $cart->custom_option_id,
                    'item_name' => $itemName,
                    'quantity' => $cart->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);
            }

            // Buat invoice
            InvoiceService::createInvoice($order);

            // Hapus keranjang daily saja yang sudah dicheckout
            if ($menu_date && $menu_date !== 'unknown') {
                $user->carts()->whereNull('cart_group_id')->whereDate('menu_date', $menu_date)->delete();
            } else {
                $user->carts()->whereNull('cart_group_id')->whereNull('menu_date')->delete();
            }

            // Jika transfer, buat Midtrans snap token
            try {
                $snapToken = PaymentService::createSnapToken($order);
                $order->update(['midtrans_snap_token' => $snapToken]);
            } catch (\Exception $e) {
                \Log::error('Midtrans error: ' . $e->getMessage());
            }

            // Kirim notifikasi pesanan dibuat
            NotificationService::notifyOrderCreated($order);

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat!');
        });
    }

    /**
     * Tampilkan halaman checkout untuk 1 event group.
     */
    public function showEventCheckout(string $groupId)
    {
        $user = auth()->user();
        $groupItems = $user->carts()
            ->where('cart_group_id', $groupId)
            ->with(['customOption', 'cateringPackage', 'cateringService', 'servingType'])
            ->get();

        if ($groupItems->isEmpty()) {
            return redirect()->route('customer.cart', ['tab' => 'event'])
                ->with('error', 'Pesanan event tidak ditemukan.');
        }

        $packageItem = $groupItems->firstWhere('item_type', 'package');
        $menuItems = $groupItems->whereIn('item_type', ['package_item', 'custom_menu']);
        $additionItems = $groupItems->where('item_type', 'addition');
        $service = $groupItems->first()->cateringService;
        $servingType = $groupItems->first()->servingType;

        // Hitung subtotal
        $subtotal = $groupItems->sum(fn ($c) => $c->subtotal);

        $districts = District::with('villages')->get();

        return view('customer.event_checkout', compact(
            'groupId', 'groupItems', 'packageItem', 'menuItems',
            'additionItems', 'service', 'servingType', 'subtotal', 'districts'
        ));
    }

    /**
     * Checkout per event group.
     */
    public function checkoutEventGroup(Request $request, string $groupId)
    {
        $validated = $request->validate([
            'order_date' => 'required|date|after:today',
            'event_start_time' => 'required|date_format:H:i',
            'pickup_method' => 'required|in:delivery,pickup',
            'district_id' => 'required_if:pickup_method,delivery|nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'address_detail' => 'required_if:pickup_method,delivery|nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'payment_method' => 'required|in:transfer',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        $groupItems = $user->carts()
            ->where('cart_group_id', $groupId)
            ->with(['customOption', 'cateringPackage', 'cateringService', 'servingType'])
            ->get();

        if ($groupItems->isEmpty()) {
            return back()->with('error', 'Pesanan event tidak ditemukan.');
        }

        // Tentukan catering service dan package
        $cateringServiceId = $groupItems->first()->catering_service_id;
        $packageItem = $groupItems->firstWhere('item_type', 'package');
        $packageId = $packageItem?->catering_package_id;

        // Validasi H-3
        OrderService::validateOrderDate($validated['order_date'], $cateringServiceId);

        // Hitung total porsi
        $menuItems = $groupItems->whereIn('item_type', ['package_item', 'custom_menu']);
        $totalPortions = $menuItems->sum('quantity');

        // Hitung penyajian
        $servingType = $groupItems->first()->servingType;

        return DB::transaction(function () use ($validated, $user, $groupItems, $groupId, $cateringServiceId, $packageId, $packageItem, $totalPortions, $servingType) {
            // Hitung subtotal
            $subtotal = $groupItems->sum(fn ($c) => $c->subtotal);
            $shippingCost = $validated['pickup_method'] === 'delivery'
                ? ShippingCost::getCostByDistrict($validated['district_id'])
                : 0;
            $total = $subtotal + $shippingCost;

            // Buat order
            $order = Order::create([
                'order_number' => OrderService::generateOrderNumber(),
                'user_id' => $user->id,
                'catering_service_id' => $cateringServiceId,
                'package_id' => $packageId,
                'order_date' => $validated['order_date'],
                'event_start_time' => $validated['event_start_time'],
                'pickup_method' => $validated['pickup_method'],
                'district_id' => $validated['district_id'] ?? null,
                'village_id' => $validated['village_id'] ?? null,
                'address_detail' => $validated['address_detail'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'serving_type' => $servingType?->name,
                'portion' => $totalPortions,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => 'transfer',
                'payment_status' => 'unpaid',
                'status' => 'pending_payment',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Buat OrderItems
            foreach ($groupItems as $cart) {
                // Skip package marker
                if ($cart->item_type === 'package') {
                    // Simpan sebagai OrderItem paket header
                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_name' => 'Paket: ' . ($cart->cateringPackage?->name ?? 'Paket'),
                        'quantity' => 1,
                        'unit_price' => (float) ($cart->cateringPackage?->price ?? 0),
                        'subtotal' => (float) ($cart->cateringPackage?->price ?? 0),
                    ]);
                    continue;
                }

                $unitPrice = 0;
                $itemName = $cart->customOption?->name ?? 'Item';

                if ($cart->item_type === 'package_item') {
                    // Termasuk dalam paket, harga 0
                    $unitPrice = 0;
                    $itemName = 'Menu: ' . $itemName;
                } elseif ($cart->item_type === 'custom_menu') {
                    $unitPrice = (float) ($cart->customOption?->price ?? 0);
                    $itemName = 'Menu: ' . $itemName;
                } elseif ($cart->item_type === 'addition') {
                    $unitPrice = (float) ($cart->customOption?->price ?? 0);
                    $itemName = 'Extra: ' . $itemName;
                }

                $itemSubtotal = $unitPrice * $cart->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'custom_option_id' => $cart->custom_option_id,
                    'item_name' => $itemName,
                    'quantity' => $cart->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            // Simpan penyajian sebagai OrderItem
            if ($servingType) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'custom_option_id' => $servingType->id,
                    'item_name' => 'Penyajian: ' . $servingType->name,
                    'quantity' => $totalPortions,
                    'unit_price' => 0,
                    'subtotal' => 0,
                ]);
            }

            // Buat invoice
            InvoiceService::createInvoice($order);

            // Buat Midtrans snap token
            try {
                $snapToken = PaymentService::createSnapToken($order);
                $order->update(['midtrans_snap_token' => $snapToken]);
            } catch (\Exception $e) {
                \Log::error('Midtrans error: ' . $e->getMessage());
            }

            // Hapus cart group setelah order dibuat
            // (items tetap di keranjang jika pembayaran gagal - handled by PaymentController callback)
            $user->carts()->where('cart_group_id', $groupId)->delete();

            // Kirim notifikasi
            NotificationService::notifyOrderCreated($order);

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan event berhasil dibuat! Silakan lakukan pembayaran.');
        });
    }
}
