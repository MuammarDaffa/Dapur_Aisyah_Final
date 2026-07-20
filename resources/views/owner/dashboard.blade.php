@extends('layouts.owner')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-5 g-3">
        <div class="rounded shadow p-5 text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-success small fw-medium uppercase tracking-wider">Pendapatan Hari Ini</p>
                    <p class="fs-3 fw-bold mt-1">Rp {{ number_format($stats['daily_revenue'], 0, ',', '.') }}</p>
                </div>
                <div style="width: 48px; height: 48px;" class="bg-white/20 rounded d-flex align-items-center justify-content-center">
                    <span class="fs-4">💰</span>
                </div>
            </div>
        </div>

        <div class="rounded shadow p-5 text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-info small fw-medium uppercase tracking-wider">Pendapatan Mingguan</p>
                    <p class="fs-3 fw-bold mt-1">Rp {{ number_format($stats['weekly_revenue'], 0, ',', '.') }}</p>
                </div>
                <div style="width: 48px; height: 48px;" class="bg-white/20 rounded d-flex align-items-center justify-content-center">
                    <span class="fs-4">📅</span>
                </div>
            </div>
        </div>

        <div class="rounded shadow p-5 text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-purple-100 small fw-medium uppercase tracking-wider">Pendapatan Bulanan</p>
                    <p class="fs-3 fw-bold mt-1">Rp {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</p>
                </div>
                <div style="width: 48px; height: 48px;" class="bg-white/20 rounded d-flex align-items-center justify-content-center">
                    <span class="fs-4">📊</span>
                </div>
            </div>
        </div>

        <div class="rounded shadow p-5 text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-primary small fw-medium uppercase tracking-wider">Total Pesanan</p>
                    <p class="fs-3 fw-bold mt-1">{{ number_format($stats['total_orders']) }}</p>
                </div>
                <div style="width: 48px; height: 48px;" class="bg-white/20 rounded d-flex align-items-center justify-content-center">
                    <span class="fs-4">📦</span>
                </div>
            </div>
        </div>

        <div class="rounded shadow p-5 text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-pink-100 small fw-medium uppercase tracking-wider">Total Pelanggan</p>
                    <p class="fs-3 fw-bold mt-1">{{ number_format($stats['total_customers']) }}</p>
                </div>
                <div style="width: 48px; height: 48px;" class="bg-white/20 rounded d-flex align-items-center justify-content-center">
                    <span class="fs-4">👥</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Best Sellers --}}
    <div class="row row-cols-1 lg:row-cols-2 g-3">
        {{-- Pesanan Status Pie Chart --}}
        <div class="bg-white rounded shadow-md border border border-secondary p-6">
            <h3 class="fw-bold text-secondary fs-5 mb-4 d-flex align-items-center g-3">
                <span>🥧</span> Proporsi Status Pesanan
            </h3>
            <div class="position-relative h-72 d-flex align-items-center justify-content-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>

        {{-- Best Sellers --}}
        <div class="bg-white rounded shadow-md border border border-secondary p-6">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h3 class="fw-bold text-secondary fs-5 d-flex align-items-center g-3">
                    <span>⭐</span> Produk Best Seller
                </h3>
                <a href="{{ route('owner.best-sellers') }}" class="fs-6 text-teal-600 fw-medium">
                    Lihat Semua →
                </a>
            </div>
            <div class="d-flex flex-column gap-2">
                @forelse($bestSellers->take(5) as $index => $produk)
                    <div class="d-flex align-items-center g-3 p-3 rounded hover:bg-light">
                        <div style="width: 32px; height: 32px;" class="rounded d-flex align-items-center justify-content-center text-white fs-6 fw-bold">
                            {{ $index + 1 }}
                        </div>
                        <div style="height: 40px;" class="w-10 rounded overflow-hidden bg-light d-flex-flex-shrink-0">
                            @if($produk->image)
                                <img src="{{ asset('storage/' . $produk->image) }}" alt="{{ $produk->name }}" class="w-100 h-100 object-cover">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary fs-5">🍽️</div>
                            @endif
                        </div>
                        <div class="d-flex-1 min-w-0">
                            <p class="fw-medium text-secondary truncate">{{ $produk->name }}</p>
                            <p class="small text-secondary">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-end">
                            <p class="fw-bold text-teal-600">{{ $produk->order_items_count }}</p>
                            <p class="small text-secondary">terjual</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-secondary">
                        <span class="fs-2 d-block mb-2">📦</span>
                        Belum ada data penjualan
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Ulasan --}}
    <div class="bg-white rounded shadow-md border border border-secondary p-6">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold text-secondary fs-5 d-flex align-items-center g-3">
                <span>💬</span> Ulasan Terbaru
            </h3>
            <a href="{{ route('owner.ulasan') }}" class="fs-6 text-teal-600 fw-medium">
                Lihat Semua →
            </a>
        </div>

        @if($recentReviews->isEmpty())
            <div class="text-center py-8 text-secondary">
                <span class="fs-2 d-block mb-2">📝</span>
                Belum ada ulasan
            </div>
        @else
            <div class="row row-cols-1 md:row-cols-2 g-3">
                @foreach($recentReviews as $ulasan)
                    <div class="border border border-secondary rounded p-4 hover:shadow-md transition-shadow">
                        <div class="d-flex align-items-center g-3 mb-2">
                            <div style="height: 36px;" class="w-9 rounded-pill d-flex align-items-center justify-content-center text-white fs-6 fw-bold">
                                {{ strtoupper(substr($ulasan->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="d-flex-1">
                                <p class="fw-medium text-secondary fs-6">{{ $ulasan->user->name ?? '-' }}</p>
                                <p class="small text-secondary">{{ $ulasan->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if($ulasan->comment)
                            <p class="fs-6 text-secondary line-clamp-2 mt-1">{{ $ulasan->comment }}</p>
                        @endif
                        <p class="small text-secondary mt-2">{{ $ulasan->pesanan->layananKatering->name ?? '' }}</p>
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
        'diproses': 'Diproses',
        'dikirim': 'Sedang Dikirim',
        'selesai': 'Selesai',
        'dibatalkan': 'Dibatalkan'
    };
    const colors = {
        'diproses': '#3B82F6',
        'dikirim': '#8B5CF6',
        'selesai': '#10B981',
        'dibatalkan': '#EF4444'
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
