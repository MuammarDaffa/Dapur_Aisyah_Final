<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period')) {
            switch ($request->period) {
                case 'harian':
                    $query->whereDate('created_at', today());
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
            'total_revenue' => (clone $query)->where('status', 'selesai')->sum('total'),
            'average_order' => (clone $query)->where('status', 'selesai')->avg('total') ?? 0,
        ];

        $pesanan = $query->with(['user', 'layananKatering'])->latest()->paginate(20);

        return view('admin.laporan.index', compact('pesanan', 'summary'));
    }
}
