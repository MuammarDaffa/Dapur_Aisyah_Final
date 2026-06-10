<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'daily_sales' => Order::completed()->whereDate('created_at', $today)->sum('total'),
            'weekly_sales' => Order::completed()->where('created_at', '>=', $today->copy()->subDays(7))->sum('total'),
            'monthly_sales' => Order::completed()->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->sum('total'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending_payment')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
        ];

        $bestSellers = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        $recentOrders = Order::with(['user', 'cateringService'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'bestSellers', 'recentOrders'));
    }
}
