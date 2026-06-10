@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['daily_sales'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">💰</div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penjualan Bulanan</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['monthly_sales'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">📊</div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pesanan Pending</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-2xl">⏳</div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sedang Diproses</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['processing_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl">🔄</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-900">Pesanan Terbaru</h3>
                <a href="{{ route('admin.orders') }}" class="text-sm text-orange-500 font-medium">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentOrders->take(5) as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->name ?? '-' }} · {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium
                            {{ match($order->status) {
                                'pending_payment' => 'bg-yellow-100 text-yellow-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'on_delivery' => 'bg-purple-100 text-purple-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            } }}">
                            {{ $order->status_label }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Best Sellers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Produk Terlaris</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($bestSellers as $i => $product)
                    <div class="flex items-center justify-between p-4">
                        <div class="flex items-center space-x-3">
                            <span class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center text-sm font-bold text-orange-600">{{ $i + 1 }}</span>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->formatted_price }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-600">{{ $product->order_items_count }} pesanan</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
