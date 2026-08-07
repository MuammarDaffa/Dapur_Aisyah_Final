@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Small Boxes (Stat box) -->
    <div class="row">

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
                <a href="{{ route('admin.pesanan') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
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
                <a href="{{ route('admin.pesanan') }}?status_pesanan=diproses" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                    Kelola pesanan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Pesanan Selesai -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h4 class="fw-bold">{{ $stats['completed_orders'] }}</h4>
                    <p>Pesanan Selesai</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <a href="{{ route('admin.pesanan') }}?status_pesanan=selesai" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat pesanan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Pesanan Batal -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h4 class="fw-bold">{{ $stats['canceled_orders'] }}</h4>
                    <p>Pesanan Batal</p>
                </div>
                <div class="small-box-icon">
                    <i class="fa-solid fa-times-circle"></i>
                </div>
                <a href="{{ route('admin.pesanan') }}?status_pesanan=dibatalkan" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Lihat pesanan <i class="fa-solid fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!-- Recent Pesanan Row -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Pesanan Terbaru</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.pesanan') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
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
                                @forelse($recentOrders->take(5) as $pesanan)
                                    <tr>
                                        <td class="align-middle fw-bold">{{ $pesanan->nomor_pesanan }}</td>
                                        <td class="align-middle">{{ $pesanan->user->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $pesanan->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="align-middle">
                                            <span class="badge text-bg-{{ $pesanan->status_pembayaran_color }} mb-1">
                                                {{ $pesanan->status_pembayaran_label }}
                                            </span>
                                            <span class="badge text-bg-{{ $pesanan->status_pesanan_color }}">
                                                {{ $pesanan->status_pesanan_label }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('admin.pesanan.show', $pesanan) }}" class="btn btn-sm btn-primary">Lihat</a>
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
