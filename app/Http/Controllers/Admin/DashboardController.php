<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Menu;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $stats = [
            'monthly_sales' => Pesanan::completed()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total'),
            'total_orders' => Pesanan::count(),
            'processing_orders' => Pesanan::where('status_pesanan', 'diproses')->count(),
            'completed_orders' => Pesanan::completed()->count(),
        ];

        $bestSellers = Menu::withCount('pesanan')
            ->orderByDesc('pesanan_count')
            ->take(5)
            ->get();

        $recentOrders = Pesanan::with(['user', 'layanan'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'bestSellers', 'recentOrders'));
    }
}
