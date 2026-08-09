@extends('layouts.owner')

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
                <form action="{{ route('owner.reports') }}" method="GET" class="row gx-3 gy-3 align-items-end">
                    <!-- <div class="col-md-3">
                        <label class="form-label fw-bold">Periode (Cepat)</label>
                        <select name="period" class="form-select">
                            <option value="">Semua Waktu</option>
                            <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                        </select>
                    </div> -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Layanan</label>
                        <select name="tipe_layanan" class="form-select">
                            <option value="">Semua Layanan</option>
                            <option value="harian" {{ request('tipe_layanan') == 'harian' ? 'selected' : '' }}>Katering Harian</option>
                            <option value="acara" {{ request('tipe_layanan') == 'acara' ? 'selected' : '' }}>Katering Acara Kantoran</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                    <div class="col-md-12 mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Terapkan</button>
                        <a href="{{ route('owner.reports') }}" class="btn btn-secondary text-white">Reset</a>
                        
                    </div>
                </form>
            </div>
        </div>

        {{-- Pesanan Table --}}
        <div class="card card-outline card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Daftar Pesanan</h3>
                <a href="{{ route('owner.reports.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf me-1"></i> Cetak PDF</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Tanggal Transaksi</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan as $index => $p)
                                <tr>
                                    <td class="align-middle">{{ $pesanan->firstItem() + $index }}</td>
                                    <td class="align-middle fw-medium">{{ $p->nomor_pesanan }}</td>
                                    <td class="align-middle">{{ $p->user->name ?? '-' }}</td>
                                    <td class="align-middle">Katering {{ ucfirst($p->tipe_layanan) }}</td>
                                    <td class="align-middle">{{ $p->created_at->format('d M Y') }}</td>
                                    <td class="align-middle fw-bold text-success text-end">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fa-solid fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted fw-medium mb-0">Tidak ada data laporan yang sesuai dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="5" class="text-end fw-bold">Total Pemasukan:</td>
                                <td class="fw-bold text-success text-end fs-5">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
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
