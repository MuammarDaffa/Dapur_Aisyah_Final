<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'daily_sales' => Pesanan::completed()->whereDate('created_at', $today)->sum('total'),
            'weekly_sales' => Pesanan::completed()->where('created_at', '>=', $today->copy()->subDays(7))->sum('total'),
            'monthly_sales' => Pesanan::completed()->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->sum('total'),
            'total_orders' => Pesanan::count(),
            'processing_orders' => Pesanan::where('status', 'diproses')->count(),
        ];

        $bestSellers = Produk::withCount('detailPesanan')
            ->orderByDesc('detail_pesanan_count')
            ->take(5)
            ->get();

        $recentOrders = Pesanan::with(['user', 'layananKatering'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'bestSellers', 'recentOrders'));
    }
}
