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
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $carts = $user->carts()->with(['product.cateringService', 'customOption', 'cateringPackage'])->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart')
                ->with('error', 'Keranjang belanja kosong.');
        }

        // Group event items
        $groupedCarts = $carts->groupBy(function ($cart) {
            return $cart->cart_group_id ?? 'ungrouped_' . $cart->id;
        });

        $districts = District::with('villages')->get();
        $subtotal = $carts->sum(fn ($cart) => $cart->subtotal);

        return view('customer.checkout', compact('carts', 'groupedCarts', 'districts', 'subtotal', 'user'));
    }

    public function store(CheckoutRequest $request)
    {
        $validated = $request->validated();

        $user = auth()->user();
        $carts = $user->carts()->with(['product.cateringService', 'customOption', 'cateringPackage'])->get();

        if ($carts->isEmpty()) {
            return back()->with('error', 'Keranjang belanja kosong.');
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
                'serving_type' => $validated['serving_type'] ?? null,
                'portion' => $validated['portion'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid',
                'status' => $validated['payment_method'] === 'cod' ? 'processing' : 'pending_payment',
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

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'custom_option_id' => $cart->custom_option_id,
                    'item_name' => $itemName,
                    'quantity' => $cart->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $cart->quantity,
                ]);
            }

            // Buat invoice
            InvoiceService::createInvoice($order);

            // Hapus keranjang
            $user->carts()->delete();

            // Jika transfer, buat Midtrans snap token
            if ($validated['payment_method'] === 'transfer') {
                try {
                    $snapToken = PaymentService::createSnapToken($order);
                    $order->update(['midtrans_snap_token' => $snapToken]);
                } catch (\Exception $e) {
                    \Log::error('Midtrans error: ' . $e->getMessage());
                }
            }

            // COD langsung status processing
            if ($validated['payment_method'] === 'cod') {
                $order->update(['payment_status' => 'paid']);
            }

            // Kirim notifikasi pesanan dibuat
            NotificationService::notifyOrderCreated($order);

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat!');
        });
    }
}
