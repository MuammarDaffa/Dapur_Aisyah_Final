@extends('layouts.owner')

@section('title', 'Dashboard')

@section('content')
    <!-- Small Boxes (Stat box) -->
    <div class="row">

        <!-- Penjualan Minggu Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-info">
                <div class="inner">
                    <h4 class="fw-bold">Rp {{ number_format($stats['weekly_revenue'], 0, ',', '.') }}</h4>
                    <p>Penjualan Minggu Ini</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-calendar-week"></i>
                </div>
                <a href="{{ route('owner.reports') }}?period=weekly" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat Laporan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Penjualan Bulan Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h4 class="fw-bold">Rp {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</h4>
                    <p>Penjualan Bulan Ini</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <a href="{{ route('owner.reports') }}?period=monthly" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat Laporan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Pesanan -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h4 class="fw-bold">{{ number_format($stats['total_orders'], 0, ',', '.') }}</h4>
                    <p>Total Pesanan</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-shopping-cart"></i>
                </div>
                <a href="{{ route('owner.pesanan') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat pesanan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Ulasan -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h4 class="fw-bold">{{ number_format($stats['total_reviews'], 0, ',', '.') }}</h4>
                    <p>Total Ulasan</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-star"></i>
                </div>
                <a href="{{ route('owner.ulasan') }}" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat Ulasan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mt-4">
       

        <div class="col-lg-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Ulasan Terbaru</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Pelanggan</th>
                                    <th>Komentar</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReviews as $review)
                                    <tr>
                                        <td>{{ $review->user->name }}</td>
                                        <td class="text-truncate" style="max-width: 200px;" title="{{ $review->komentar }}">
                                            {{ $review->komentar }}
                                        </td>
                                        <td>{{ $review->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Belum ada ulasan terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('owner.ulasan') }}" class="btn btn-sm btn-info text-white">Lihat Semua Ulasan</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
