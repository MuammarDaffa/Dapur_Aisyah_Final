<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::query();

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        if ($request->filled('status_pesanan')) {
            $query->where('status_pesanan', $request->status_pesanan);
        }

        if ($request->filled('period')) {
            switch ($request->period) {
                // case 'harian':
                //     $query->whereDate('created_at', today());
                //     break;
                case 'weekly':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'yearly':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Hitung summary sebelum paginate agar query builder tidak termodifikasi
        $summary = [
            'total_orders' => (clone $query)->count(),
            'total_revenue' => (clone $query)->where('status_pesanan', 'selesai')->sum('total'),
            'average_order' => (clone $query)->where('status_pesanan', 'selesai')->avg('total') ?? 0,
        ];

        $pesanan = $query->with(['user'])->latest()->paginate(20);

        return view('owner.laporan.index', compact('pesanan', 'summary'));
    }
}
