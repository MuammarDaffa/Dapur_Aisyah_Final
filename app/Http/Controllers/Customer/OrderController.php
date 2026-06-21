<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->orders()->with('cateringService')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Pastikan order milik user yang login
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items', 'cateringService', 'invoice', 'review', 'district', 'village']);

        // Sync dengan Midtrans jika masih pending/unpaid (berguna untuk testing local tanpa webhook)
        if ($order->payment_status === 'unpaid' && $order->midtrans_snap_token) {
            PaymentService::checkAndSyncStatus($order);
            // Refresh model setelah sync
            $order->refresh();
        }

        return view('customer.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // Validasi pembatalan menggunakan OrderService
        try {
            OrderService::validateCancellation($order);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', $e->validator->errors()->first());
        }

        // Update status pesanan
        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        // Kirim notifikasi
        NotificationService::notifyStatusChanged($order->fresh());

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
