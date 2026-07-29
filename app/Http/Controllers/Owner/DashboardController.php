<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Ulasan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'daily_revenue' => Pesanan::completed()->whereDate('created_at', $today)->sum('total'),
            'weekly_revenue' => Pesanan::completed()->where('created_at', '>=', $today->copy()->subDays(7))->sum('total'),
            'monthly_revenue' => Pesanan::completed()->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->sum('total'),
            'total_orders' => Pesanan::count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        // Status pesanan untuk pie chart
        $orderStatuses = Pesanan::selectRaw('status_pesanan, COUNT(*) as count')
            ->groupBy('status_pesanan')
            ->pluck('count', 'status_pesanan')
            ->toArray();

        $recentReviews = Ulasan::with(['user'])
            ->latest()
            ->take(10)
            ->get();

        return view('owner.dashboard', compact('stats', 'orderStatuses', 'recentReviews'));
    }

    public function customers()
    {
        $pelanggans = User::where('role', 'customer')
            ->withCount('pesanan')
            ->orderByDesc('pesanan_count')
            ->take(10)
            ->get();

        return view('owner.customers', compact('pelanggans'));
    }

    public function ulasan()
    {
        $ulasan = Ulasan::with(['user'])
            ->latest()
            ->paginate(20);

        return view('owner.reviews', compact('ulasan'));
    }

    public function reports(Request $request)
    {
        $query = Pesanan::completed();

        if ($request->filled('period')) {
            switch ($request->period) {
                case 'harian':
                    $query->whereDate('created_at', today());
                    break;
                case 'monthly':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'yearly':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        $summary = [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'average_order' => $query->avg('total') ?? 0,
        ];

        return view('owner.reports', compact('summary'));
    }
}
