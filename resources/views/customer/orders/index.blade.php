@extends('layouts.app')
@section('title', 'Pesanan Saya')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <svg class="w-7 h-7 text-orange-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        <span>Pesanan <span class="text-orange-500">Saya</span></span>
    </h2>

    <!-- Status Filter -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('customer.orders') }}" class="px-4 py-2 rounded-full text-sm font-medium {{ !request('status') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">Semua</a>
        @foreach(['processing' => 'Diproses', 'on_delivery' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $key => $label)
            <a href="{{ route('customer.orders', ['status' => $key]) }}" class="px-4 py-2 rounded-full text-sm font-medium {{ request('status') == $key ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ $label }}</a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($orders as $order)
            <a href="{{ route('customer.orders.show', $order) }}" class="block bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->cateringService->name ?? 'Katering' }} · {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full font-medium
                        {{ match($order->status) {
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
                <div class="w-16 h-16 mx-auto mb-3 text-gray-300 flex items-center justify-center">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <p class="text-gray-500">Belum ada pesanan.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
