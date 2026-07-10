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
        
        $orderDate = $carts->min('menu_date') ? $carts->min('menu_date')->format('Y-m-d') : date('Y-m-d');
        
        // Pass cutoff data if available
        $firstCart = $carts->first();
        $service = $firstCart->product ? $firstCart->product->cateringService : ($firstCart->customOption ? $firstCart->customOption->cateringService : null);

        return view('customer.checkout', compact('carts', 'groupedCarts', 'districts', 'subtotal', 'user', 'orderDate', 'service'));
    }

    /**
     * Store daily checkout.
     */
    public function store(CheckoutRequest $request, $menu_date = null)
    {
        $validated = $request->validated();
        
        $finalAddressDetail = $validated['address_detail'] ?? null;
        if (!empty($validated['osm_address']) && $finalAddressDetail) {
            $finalAddressDetail = $validated['osm_address'] . "\nDetail Patokan: " . $finalAddressDetail;
        } else if (!empty($validated['osm_address'])) {
            $finalAddressDetail = $validated['osm_address'];
        }

        $user = auth()->user();
        $cartsQuery = $user->carts()
            ->whereNull('cart_group_id') // Hanya daily
            ->with(['product.cateringService', 'customOption', 'cateringPackage']);
            
        $carts = $cartsQuery->get();

        if ($carts->isEmpty()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Keranjang belanja kosong atau tanggal tidak valid.'], 400);
            }
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

        return DB::transaction(function () use ($request, $validated, $user, $carts, $cateringServiceId, $packageId, $menu_date, $finalAddressDetail) {
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
                'order_date' => $carts->min('menu_date') ? $carts->min('menu_date')->format('Y-m-d') : date('Y-m-d'),
                'pickup_method' => $validated['pickup_method'],
                'district_id' => $validated['district_id'] ?? null,
                'village_id' => $validated['village_id'] ?? null,
                'address_detail' => $finalAddressDetail,
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
                    // Package items & extras → price 0 (sudah termasuk harga paket)
                    $unitPrice = in_array($cart->item_type, ['package_item', 'package_extra'])
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
                
                // Append menu date specifically for daily items since they are checked out together
                if ($cart->menu_date) {
                    $itemName .= " [Kirim: " . $cart->menu_date->format('d M Y') . "]";
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

            // Hapus semua keranjang daily yang sudah dicheckout
            $user->carts()->whereNull('cart_group_id')->delete();

            // Jika transfer, buat Midtrans snap token
            try {
                $snapToken = PaymentService::createSnapToken($order);
                $order->update(['midtrans_snap_token' => $snapToken]);
            } catch (\Exception $e) {
                \Log::error('Midtrans error: ' . $e->getMessage());
            }

            // Kirim notifikasi pesanan dibuat
            NotificationService::notifyOrderCreated($order);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'snap_token' => $order->midtrans_snap_token,
                    'redirect_url' => route('customer.orders.show', $order),
                ]);
            }

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat!');
        });
    }

    /**
     * Tampilkan halaman checkout untuk event group (atau semua event group jika $groupId === 'all').
     */
    public function showEventCheckout(string $groupId)
    {
        $user = auth()->user();
        if ($groupId === 'all') {
            $groupItems = $user->carts()
                ->whereNotNull('cart_group_id')
                ->with(['customOption', 'cateringPackage', 'cateringService', 'servingType'])
                ->get();
        } else {
            $groupItems = $user->carts()
                ->where('cart_group_id', $groupId)
                ->with(['customOption', 'cateringPackage', 'cateringService', 'servingType'])
                ->get();
        }

        if ($groupItems->isEmpty()) {
            return redirect()->route('customer.cart', ['tab' => 'event'])
                ->with('error', 'Pesanan event tidak ditemukan.');
        }

        $eventGroups = $groupItems->groupBy('cart_group_id');
        $packageItem = $groupItems->firstWhere('item_type', 'package');
        $menuItems = $groupItems->whereIn('item_type', ['package_item', 'custom_menu']);
        $additionItems = $groupItems->where('item_type', 'addition');
        $service = $groupItems->first()->cateringService;
        $servingType = $groupItems->first()->servingType;
        $minDays = $groupItems->max(fn ($item) => $item->cateringService?->minimal_order_days ?? 3) ?? 3;

        // Hitung subtotal
        $subtotal = $groupItems->sum(fn ($c) => $c->subtotal);

        $districts = District::with('villages')->get();

        return view('customer.event_checkout', compact(
            'groupId', 'groupItems', 'eventGroups', 'packageItem', 'menuItems',
            'additionItems', 'service', 'servingType', 'subtotal', 'districts', 'minDays'
        ));
    }

    /**
     * Checkout per event group atau seluruh event group sekaligus.
     */
    public function checkoutEventGroup(Request $request, string $groupId)
    {
        $validated = $request->validate([
            'order_date' => 'required|date|after:today',
            'pickup_method' => 'required|in:delivery,pickup',
            'district_id' => 'required_if:pickup_method,delivery|nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'address_detail' => 'nullable|string',
            'osm_address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'payment_method' => 'required|in:transfer',
            'notes' => 'nullable|string',
        ]);

        if ($validated['pickup_method'] === 'delivery') {
            $locValidation = \App\Services\LocationService::validateLocation(
                $validated['latitude'] ?? null,
                $validated['longitude'] ?? null,
                $validated['district_id'] ?? null,
                null,
                $validated['osm_address'] ?? null
            );

            if (!$locValidation['is_in_pontianak']) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $locValidation['message'] ?? 'Lokasi berada di luar wilayah Pontianak.'
                    ], 422);
                }
                return back()->with('error', $locValidation['message'] ?? 'Lokasi berada di luar wilayah Pontianak.');
            }
        }

        $finalAddressDetail = $validated['address_detail'] ?? null;
        if (!empty($validated['osm_address']) && $finalAddressDetail) {
            $finalAddressDetail = $validated['osm_address'] . "\nDetail Patokan: " . $finalAddressDetail;
        } else if (!empty($validated['osm_address'])) {
            $finalAddressDetail = $validated['osm_address'];
        }

        $user = auth()->user();
        if ($groupId === 'all') {
            $groupItems = $user->carts()
                ->whereNotNull('cart_group_id')
                ->with(['customOption', 'cateringPackage', 'cateringService', 'servingType'])
                ->get();
        } else {
            $groupItems = $user->carts()
                ->where('cart_group_id', $groupId)
                ->with(['customOption', 'cateringPackage', 'cateringService', 'servingType'])
                ->get();
        }

        if ($groupItems->isEmpty()) {
            return back()->with('error', 'Pesanan event tidak ditemukan.');
        }

        // Tentukan catering service dan package
        $cateringServiceId = $groupItems->first()->catering_service_id;
        $packageItem = $groupItems->firstWhere('item_type', 'package');
        $packageId = $packageItem?->catering_package_id;

        // Validasi H-3
        OrderService::validateOrderDate($validated['order_date'], $cateringServiceId);

        // Hitung total porsi & validasi batas maksimal custom menu
        $menuItems = $groupItems->whereIn('item_type', ['package_item', 'custom_menu']);
        $totalPortions = 0;
        foreach ($groupItems->groupBy('cart_group_id') as $gId => $gItems) {
            $pkg = $gItems->firstWhere('item_type', 'package');
            if ($pkg && $pkg->cateringPackage) {
                $totalPortions += $pkg->cateringPackage->total_portions;
            } else {
                $totalPortions += $gItems->whereIn('item_type', ['package_item', 'custom_menu'])->sum('quantity');
            }
        }

        $customMenuItems = $groupItems->where('item_type', 'custom_menu');
        if ($customMenuItems->isNotEmpty()) {
            $service = $groupItems->first()->cateringService;
            $customPortions = $customMenuItems->sum('quantity');
            if ($service && $service->max_portion && $customPortions > $service->max_portion) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Total porsi melebihi batas maksimal ({$service->max_portion} porsi)."
                    ], 422);
                }
                return back()->with('error', "Total porsi melebihi batas maksimal ({$service->max_portion} porsi).");
            }
        }

        // Hitung penyajian
        $servingType = $groupItems->first()->servingType;

        return DB::transaction(function () use ($request, $validated, $user, $groupItems, $groupId, $cateringServiceId, $packageId, $packageItem, $totalPortions, $servingType, $finalAddressDetail) {
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
                'pickup_method' => $validated['pickup_method'],
                'district_id' => $validated['district_id'] ?? null,
                'village_id' => $validated['village_id'] ?? null,
                'address_detail' => $finalAddressDetail,
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
                } elseif ($cart->item_type === 'package_extra') {
                    // Termasuk dalam paket, harga 0
                    $unitPrice = 0;
                    $itemName = 'Extra: ' . $itemName;
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
            if ($groupId === 'all') {
                $user->carts()->whereNotNull('cart_group_id')->delete();
            } else {
                $user->carts()->where('cart_group_id', $groupId)->delete();
            }

            // Kirim notifikasi
            NotificationService::notifyOrderCreated($order);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'snap_token' => $order->midtrans_snap_token,
                    'redirect_url' => route('customer.orders.show', $order),
                ]);
            }

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan event berhasil dibuat! Silakan lakukan pembayaran.');
        });
    }
}
