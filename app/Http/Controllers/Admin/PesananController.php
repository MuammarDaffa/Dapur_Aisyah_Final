<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\NotificationService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        if ($request->filled('status_pesanan')) {
            $query->where('status_pesanan', $request->status_pesanan);
        }
        if ($request->filled('service')) {
            $query->where('tipe_layanan', $request->service);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_pesanan', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'));
            });
        }
        if ($request->filled('filter_date')) {
            $dateField = $request->date_type === 'created_at' ? 'created_at' : 'tanggal_pesanan';
            $query->whereDate($dateField, $request->filter_date);
        }

        $pesanan = $query->paginate(15);

        return view('admin.pesanan.index', compact('pesanan'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'detailPesanans.menu', 'detailPesanans.menuItems', 'detailPesanans.minuman']);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'status_pembayaran' => 'nullable|in:belum_dibayar,dp,lunas',
            'status_pesanan' => 'nullable|in:diproses,dibatalkan,selesai',
        ]);

        OrderService::updateStatus($pesanan, $validated['status_pembayaran'] ?? null, $validated['status_pesanan'] ?? null);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function cancel(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'alasan_pembatalan' => 'required|string|max:500',
        ]);

        OrderService::updateStatus($pesanan, null, Pesanan::PESANAN_DIBATALKAN, $validated['alasan_pembatalan']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function destroy(Request $request, Pesanan $pesanan)
    {
        DB::transaction(function () use ($pesanan) {
            $pesanan->ulasan()->delete();

            // Hapus notifikasi di tabel notifications yang merujuk ke pesanan_id ini
            DB::table('notifications')
                ->where('data', 'like', '%"pesanan_id":' . $pesanan->id . '%')
                ->orWhere('data', 'like', '%"pesanan_id": ' . $pesanan->id . '%')
                ->delete();

            // Hapus data utama pesanan
            $pesanan->delete();
        });

        // Jika request dari halaman detail pesanan yang baru saja dihapus, redirect ke index
        if (str_contains(url()->previous(), '/pesanan/' . $pesanan->id)) {
            return redirect()->route('admin.pesanan')->with('success', 'Pesanan berhasil dihapus.');
        }

        return back()->with('success', 'Pesanan berhasil dihapus.');
    }
}
