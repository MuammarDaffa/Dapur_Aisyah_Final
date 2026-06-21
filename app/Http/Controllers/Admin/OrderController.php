<?php

namespace App\Http\Controllers\Admin;

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
        $query = Order::with(['user', 'cateringService'])
            ->orderBy('order_date', 'asc')
            ->orderBy('created_at', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('service')) {
            $query->where('catering_service_id', $request->service);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'));
            });
        }
        if ($request->filled('filter_date')) {
            $dateField = $request->date_type === 'created_at' ? 'created_at' : 'order_date';
            $query->whereDate($dateField, $request->filter_date);
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'cateringService', 'invoice', 'district', 'village']);
        
        // Sync dengan Midtrans jika masih pending/unpaid (berguna untuk testing local tanpa webhook)
        if ($order->payment_status === 'unpaid' && $order->midtrans_snap_token) {
            PaymentService::checkAndSyncStatus($order);
            $order->refresh();
        }

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending_payment,processing,on_delivery,completed,cancelled',
        ]);

        // Gunakan OrderService untuk update status + trigger notifikasi
        OrderService::updateStatus($order, $validated['status']);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // Admin bisa batalkan kapan saja
        OrderService::updateStatus($order, 'cancelled', $validated['cancellation_reason']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
