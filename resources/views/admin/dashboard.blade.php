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
                <!-- <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div> -->
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penjualan Bulanan</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['monthly_sales'], 0, ',', '.') }}</p>
                </div>
                <!-- <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div> -->
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Pesanan</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['total_orders'] }}</p>
                </div>
                <!-- <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div> -->
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pesanan Diproses</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['processing_orders'] }}</p>
                </div>
                <!-- <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div> -->
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
