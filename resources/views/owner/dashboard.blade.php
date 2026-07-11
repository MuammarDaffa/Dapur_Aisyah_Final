@extends('layouts.owner')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-xs font-medium uppercase tracking-wider">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-bold mt-1">Rp {{ number_format($stats['daily_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-xl">💰</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs font-medium uppercase tracking-wider">Pendapatan Mingguan</p>
                    <p class="text-2xl font-bold mt-1">Rp {{ number_format($stats['weekly_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-xl">📅</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-xs font-medium uppercase tracking-wider">Pendapatan Bulanan</p>
                    <p class="text-2xl font-bold mt-1">Rp {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-xl">📊</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-xs font-medium uppercase tracking-wider">Total Pesanan</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($stats['total_orders']) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-xl">📦</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-xs font-medium uppercase tracking-wider">Total Pelanggan</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($stats['total_customers']) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-xl">👥</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Best Sellers --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Order Status Pie Chart --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 text-lg mb-4 flex items-center gap-2">
                <span>🥧</span> Proporsi Status Pesanan
            </h3>
            <div class="relative h-72 flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>

        {{-- Best Sellers --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800 text-lg flex items-center gap-2">
                    <span>⭐</span> Produk Best Seller
                </h3>
                <a href="{{ route('owner.best-sellers') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                    Lihat Semua →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($bestSellers->take(5) as $index => $product)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-lg">🍽️</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-teal-600">{{ $product->order_items_count }}</p>
                            <p class="text-xs text-gray-400">terjual</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <span class="text-3xl block mb-2">📦</span>
                        Belum ada data penjualan
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Reviews --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 text-lg flex items-center gap-2">
                <span>💬</span> Ulasan Terbaru
            </h3>
            <a href="{{ route('owner.reviews') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                Lihat Semua →
            </a>
        </div>

        @if($recentReviews->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <span class="text-3xl block mb-2">📝</span>
                Belum ada ulasan
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($recentReviews as $review)
                    <div class="border border-gray-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800 text-sm">{{ $review->user->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-600 line-clamp-2 mt-1">{{ $review->comment }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">{{ $review->order->cateringService->name ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusData = @json($orderStatuses);
    const labels = {
        'processing': 'Diproses',
        'on_delivery': 'Sedang Dikirim',
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan'
    };
    const colors = {
        'processing': '#3B82F6',
        'on_delivery': '#8B5CF6',
        'completed': '#10B981',
        'cancelled': '#EF4444'
    };

    const chartLabels = Object.keys(statusData).map(k => labels[k] || k);
    const chartColors = Object.keys(statusData).map(k => colors[k] || '#9CA3AF');
    const chartValues = Object.values(statusData);

    if (chartValues.length > 0) {
        new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartValues,
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            pointStyleWidth: 10,
                            font: { size: 12, family: 'Poppins' }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
});
</script>
@endpush
@endsection
