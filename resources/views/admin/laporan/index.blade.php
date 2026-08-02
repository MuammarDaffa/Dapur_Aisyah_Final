@extends('layouts.admin')

@section('title', 'Rekapitulasi Penjualan')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Filters --}}
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title">Filter Laporan</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports') }}" method="GET" class="row gx-3 gy-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Periode</label>
                        <select name="period" class="form-select">
                            <option value="">Semua Periode</option>
                            <option value="harian" {{ request('period') == 'harian' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Status Pesanan</label>
                        <select name="status_pesanan" class="form-select">
                            <option value="">Semua Status Pesanan</option>
                            <option value="selesai" {{ request('status_pesanan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ request('status_pesanan') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="diproses" {{ request('status_pesanan') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Terapkan</button>
                        <a href="{{ route('admin.reports') }}" class="btn btn-default"><i class="fa-solid fa-rotate-left"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pesanan Table --}}
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Daftar Pesanan</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nomor Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th class="text-center">Status Pembayaran</th>
                                <th class="text-center">Status Pesanan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan as $pesanan)
                                <tr>
                                    <td class="align-middle fw-medium">{{ $pesanan->nomor_pesanan }}</td>
                                    <td class="align-middle">{{ $pesanan->user->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $pesanan->layanan->nama ?? '-' }}</td>
                                    <td class="align-middle fw-bold text-success">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                    <td class="align-middle text-center">
                                        <span class="badge text-bg-{{ $pesanan->status_pembayaran_color }} mb-1">
                                            {{ $pesanan->status_pembayaran_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge text-bg-{{ $pesanan->status_pesanan_color }}">
                                            {{ $pesanan->status_pesanan_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle">{{ $pesanan->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fa-solid fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted fw-medium mb-0">Tidak ada data pesanan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pesanan->hasPages())
                <div class="card-footer">
                    {{ $pesanan->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
