<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'daily_revenue' => Order::completed()->whereDate('created_at', $today)->sum('total'),
            'weekly_revenue' => Order::completed()->where('created_at', '>=', $today->copy()->subDays(7))->sum('total'),
            'monthly_revenue' => Order::completed()->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->sum('total'),
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        // Status pesanan untuk pie chart
        $orderStatuses = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $bestSellers = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(10)
            ->get();

        $recentReviews = Review::with(['user', 'order.cateringService'])
            ->latest()
            ->take(10)
            ->get();

        return view('owner.dashboard', compact('stats', 'orderStatuses', 'bestSellers', 'recentReviews'));
    }

    public function bestSellers()
    {
        $bestSellers = Product::withCount('orderItems')
            ->with('cateringService')
            ->orderByDesc('order_items_count')
            ->paginate(20);

        return view('owner.best-sellers', compact('bestSellers'));
    }

    public function customers()
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(10)
            ->get();

        return view('owner.customers', compact('customers'));
    }

    public function reviews()
    {
        $reviews = Review::with(['user', 'order.cateringService'])
            ->latest()
            ->paginate(20);

        return view('owner.reviews', compact('reviews'));
    }

    public function reports(Request $request)
    {
        $query = Order::completed();

        if ($request->filled('period')) {
            switch ($request->period) {
                case 'daily':
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
