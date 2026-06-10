@extends('layouts.app')
@section('title', 'Pesanan Saya')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">📦 Pesanan <span class="text-orange-500">Saya</span></h2>

    <!-- Status Filter -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('customer.orders') }}" class="px-4 py-2 rounded-full text-sm font-medium {{ !request('status') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">Semua</a>
        @foreach(['pending_payment' => 'Menunggu Bayar', 'processing' => 'Diproses', 'on_delivery' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $key => $label)
            <a href="{{ route('customer.orders', ['status' => $key]) }}" class="px-4 py-2 rounded-full text-sm font-medium {{ request('status') == $key ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ $label }}</a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($orders as $order)
            <a href="{{ route('customer.orders.show', $order) }}" class="block bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">🍱</span>
                        <div>
                            <p class="font-bold text-gray-900">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->cateringService->name ?? 'Katering' }} · {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full font-medium
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
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Tanggal: {{ $order->order_date->format('d M Y') }} · {{ ucfirst($order->pickup_method) }}</span>
                    <span class="font-bold text-orange-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl p-12 text-center shadow-sm">
                <p class="text-5xl mb-3">📭</p>
                <p class="text-gray-500">Belum ada pesanan.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
