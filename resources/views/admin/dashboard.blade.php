@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Small Boxes (Stat box) -->
    <div class="row">
        <!-- Penjualan Hari Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h4 class="fw-bold">Rp {{ number_format($stats['daily_sales'], 0, ',', '.') }}</h4>
                    <p>Penjualan Hari Ini</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <a href="{{ route('admin.reports') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat detail <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Penjualan Bulanan -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-info">
                <div class="inner">
                    <h4 class="fw-bold">Rp {{ number_format($stats['monthly_sales'], 0, ',', '.') }}</h4>
                    <p>Penjualan Bulanan</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-chart-bar"></i>
                </div>
                <a href="{{ route('admin.reports') }}" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat detail <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Pesanan -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h4 class="fw-bold">{{ $stats['total_orders'] }}</h4>
                    <p>Total Pesanan</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-shopping-bag"></i>
                </div>
                <a href="{{ route('admin.orders') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat pesanan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Pesanan Diproses -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h4 class="fw-bold">{{ $stats['processing_orders'] }}</h4>
                    <p>Pesanan Diproses</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-spinner"></i>
                </div>
                <a href="{{ route('admin.orders') }}?status=processing" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                    Kelola pesanan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!-- Recent Orders Row -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Pesanan Terbaru</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Pelanggan</th>
                                    <th>Waktu Pesan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders->take(5) as $order)
                                    <tr>
                                        <td class="align-middle fw-bold">{{ $order->order_number }}</td>
                                        <td class="align-middle">{{ $order->user->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="align-middle">
                                            <span class="badge {{ match($order->status) { 'pending_payment' => 'text-bg-warning', 'processing' => 'text-bg-info', 'on_delivery' => 'text-bg-primary', 'completed' => 'text-bg-success', 'cancelled' => 'text-bg-danger', default => 'text-bg-secondary' } }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-default"><i class="fa-solid fa-eye"></i> Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada pesanan terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
