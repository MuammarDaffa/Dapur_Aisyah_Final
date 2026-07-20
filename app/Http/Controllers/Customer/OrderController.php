<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->pesanan()->with('layananKatering')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pesanan = $query->paginate(10);

        return view('customer.pesanan.index', compact('pesanan'));
    }

    public function show(Pesanan $pesanan)
    {
        // Pastikan pesanan milik user yang login
        abort_unless($pesanan->user_id === auth()->id(), 403);

        $pesanan->load(['items', 'layananKatering', 'tagihan', 'ulasan', 'kecamatan', 'desa']);

        // Sync dengan Midtrans jika masih pending/unpaid (berguna untuk testing local tanpa webhook)
        if ($pesanan->status_pembayaran === 'belum_dibayar' && $pesanan->midtrans_snap_token) {
            PaymentService::checkAndSyncStatus($pesanan);
            // Refresh model setelah sync
            $pesanan->refresh();
        }

        return view('customer.pesanan.show', compact('pesanan'));
    }

    public function cancel(Request $request, Pesanan $pesanan)
    {
        abort_unless($pesanan->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'alasan_pembatalan' => 'required|string|max:500',
        ]);

        // Validasi pembatalan menggunakan OrderService
        try {
            OrderService::validateCancellation($pesanan);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', $e->validator->errors()->first());
        }

        // Gunakan OrderService untuk update status agar logika refund_status berjalan
        OrderService::updateStatus($pesanan, 'dibatalkan', $validated['alasan_pembatalan']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
