@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-4 g-3 mb-8">
        <div class="card shadow-sm mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs-6 text-secondary">Penjualan Hari Ini</p>
                    <p class="fs-3 fw-bold text-secondary">Rp {{ number_format($stats['daily_sales'], 0, ',', '.') }}</p>
                </div>
                <!-- <div style="width: 48px; height: 48px;" class="bg-success text-white rounded d-flex align-items-center justify-content-center text-success">
                    <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div> -->
            </div>
        </div>
        <div class="card shadow-sm mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs-6 text-secondary">Penjualan Bulanan</p>
                    <p class="fs-3 fw-bold text-secondary">Rp {{ number_format($stats['monthly_sales'], 0, ',', '.') }}</p>
                </div>
                <!-- <div style="width: 48px; height: 48px;" class="bg-info text-white rounded d-flex align-items-center justify-content-center text-info">
                    <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div> -->
            </div>
        </div>
        <div class="card shadow-sm mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs-6 text-secondary">Total Pesanan</p>
                    <p class="fs-3 fw-bold text-primary">{{ $stats['total_orders'] }}</p>
                </div>
                <!-- <div style="width: 48px; height: 48px;" class="bg-primary text-white rounded d-flex align-items-center justify-content-center text-primary">
                    <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div> -->
            </div>
        </div>
        <div class="card shadow-sm mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs-6 text-secondary">Pesanan Diproses</p>
                    <p class="fs-3 fw-bold text-info">{{ $stats['processing_orders'] }}</p>
                </div>
                <!-- <div style="width: 48px; height: 48px;" class="bg-purple-100 rounded d-flex align-items-center justify-content-center text-purple-600">
                    <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div> -->
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold text-dark mb-0">Pesanan Terbaru</h5>
                    <a href="{{ route('admin.orders') }}" class="text-primary text-decoration-none fw-medium">Lihat Semua &rarr;</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentOrders->take(5) as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="list-group-item list-group-item-action p-4 d-flex align-items-center justify-content-between border-bottom">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">{{ $order->order_number }}</h6>
                                <small class="text-secondary">{{ $order->user->name ?? '-' }} &bull; {{ $order->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <span class="badge rounded-pill fw-medium px-3 py-2 {{ match($order->status) { 'pending_payment' => 'bg-warning text-dark', 'processing' => 'bg-info text-white', 'on_delivery' => 'bg-primary text-white', 'completed' => 'bg-success text-white', 'cancelled' => 'bg-danger text-white', default => 'bg-secondary text-white' } }}">
                                {{ $order->status_label }}
                            </span>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">Belum ada pesanan terbaru.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
