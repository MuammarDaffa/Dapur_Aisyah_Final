<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::query()
            ->where('status_pembayaran', 'lunas')
            ->where('status_pesanan', 'selesai');

        if ($request->filled('tipe_layanan')) {
            $query->where('tipe_layanan', $request->tipe_layanan);
        }

        if ($request->filled('period')) {
            switch ($request->period) {
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

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Hitung summary sebelum paginate agar query builder tidak termodifikasi
        $summary = [
            'total_orders' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total'),
            'average_order' => (clone $query)->avg('total') ?? 0,
        ];

        $pesanan = $query->with(['user'])->latest()->paginate(20);

        return view('owner.laporan.index', compact('pesanan', 'summary'));
    }

    public function cetakPdf(Request $request)
    {
        $query = Pesanan::query()
            ->with(['user'])
            ->where('status_pembayaran', 'lunas')
            ->where('status_pesanan', 'selesai');

        if ($request->filled('tipe_layanan')) {
            $query->where('tipe_layanan', $request->tipe_layanan);
        }

        if ($request->filled('period')) {
            switch ($request->period) {
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

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $pesanan = $query->latest()->get();
        $totalPemasukan = $pesanan->sum('total');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.laporan.pdf', compact('pesanan', 'totalPemasukan', 'request'));
        return $pdf->download('Laporan_Pemasukan.pdf');
    }
}
