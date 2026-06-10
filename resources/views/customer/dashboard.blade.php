@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl p-8 mb-8 text-white">
        <h2 class="text-2xl font-bold">Selamat datang, {{ auth()->user()->name }}! 👋</h2>
        <p class="mt-2 text-orange-100">Siap pesan katering hari ini?</p>
        <a href="{{ route('customer.products') }}" class="inline-block mt-4 px-6 py-2.5 bg-white text-orange-600 font-semibold rounded-full hover:shadow-lg transition-all">
            Lihat Menu →
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Pesanan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->orders()->count() }}</p>
                </div>
                <span class="text-3xl">📦</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Keranjang</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cartCount }}</p>
                </div>
                <span class="text-3xl">🛒</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ulasan Diberikan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->reviews()->count() }}</p>
                </div>
                <span class="text-3xl">⭐</span>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h3>
            <a href="{{ route('customer.orders') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">Lihat Semua →</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentOrders as $order)
                <a href="{{ route('customer.orders.show', $order) }}" class="flex items-center justify-between p-4 hover:bg-orange-50/50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-lg">🍱</div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->cateringService->name ?? 'Katering' }} · {{ $order->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        <span class="inline-block text-xs px-2 py-0.5 rounded-full font-medium
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
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <p class="text-4xl mb-3">📭</p>
                    <p>Belum ada pesanan. <a href="{{ route('customer.products') }}" class="text-orange-500 font-medium hover:underline">Pesan sekarang</a></p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
