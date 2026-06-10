<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period')) {
            switch ($request->period) {
                case 'daily':
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

        // Hitung summary sebelum paginate agar query builder tidak termodifikasi
        $summary = [
            'total_orders' => (clone $query)->count(),
            'total_revenue' => (clone $query)->where('status', 'completed')->sum('total'),
            'average_order' => (clone $query)->where('status', 'completed')->avg('total') ?? 0,
        ];

        $orders = $query->with(['user', 'cateringService'])->latest()->paginate(20);

        return view('admin.reports.index', compact('orders', 'summary'));
    }
}
