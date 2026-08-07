<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

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

        return view('owner.pesanan.index', compact('pesanan'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'detailPesanans.menu', 'detailPesanans.minuman', 'detailPesanans.menuItems']);

        if ($pesanan->tipe_layanan === 'harian') {
            return view('owner.pesanan.show_harian', compact('pesanan'));
        }

        return view('owner.pesanan.show_acara', compact('pesanan'));
    }
}
