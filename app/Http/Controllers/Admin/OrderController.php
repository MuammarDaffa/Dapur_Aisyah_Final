<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'cateringService'])
            ->orderBy('created_at', 'desc');

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
            'status' => 'required|in:processing,on_delivery,completed,cancelled',
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

    public function destroy(Request $request, Order $order)
    {
        DB::transaction(function () use ($order) {
            // Hapus file PDF invoice jika ada di storage agar tidak menyisakan orphan file
            if ($order->invoice && $order->invoice->pdf_path && Storage::disk('public')->exists($order->invoice->pdf_path)) {
                Storage::disk('public')->delete($order->invoice->pdf_path);
            }

            // Hapus data relasi langsung agar bersih
            $order->items()->delete();
            $order->invoice()->delete();
            $order->review()->delete();

            // Hapus notifikasi di tabel notifications yang merujuk ke order_id ini
            DB::table('notifications')
                ->where('data', 'like', '%"order_id":' . $order->id . '%')
                ->orWhere('data', 'like', '%"order_id": ' . $order->id . '%')
                ->delete();

            // Hapus data utama pesanan
            $order->delete();
        });

        // Jika request dari halaman detail pesanan yang baru saja dihapus, redirect ke index
        if (str_contains(url()->previous(), '/orders/' . $order->id)) {
            return redirect()->route('admin.orders')->with('success', 'Pesanan berhasil dihapus.');
        }

        return back()->with('success', 'Pesanan berhasil dihapus.');
    }
}
