@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary mb-4">
            <div class="card-body p-3">
                <form action="{{ route('admin.pesanan') }}" method="GET" class="row gx-2 gy-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pesanan/nama..." class="form-control">
                    </div>
                    <div class="col-md-2">
                        <select name="status_pembayaran" class="form-select">
                            <option value="">Semua Status Pembayaran</option>
                            @foreach(['belum_dibayar'=>'Belum Dibayar','dp'=>'DP Dibayar','lunas'=>'Lunas'] as $k=>$v)
                                <option value="{{ $k }}" {{ request('status_pembayaran')==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status_pesanan" class="form-select">
                            <option value="">Semua Status Pesanan</option>
                            @foreach(['diproses'=>'Diproses','dibatalkan'=>'Dibatalkan','selesai'=>'Selesai'] as $k=>$v)
                                <option value="{{ $k }}" {{ request('status_pesanan')==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="service" class="form-select">
                            <option value="">Semua Layanan</option>
                                <option value="harian" {{ request('service') == 'harian' ? 'selected' : '' }}>Katering Harian</option>
                                <option value="acara" {{ request('service') == 'acara' ? 'selected' : '' }}>Katering Acara</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-search"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Daftar Pesanan</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-nowrap">
                        <thead>
                            <tr>
                                <th>Pesanan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>Status Pembayaran</th>
                                <th>Status Pesanan</th>
                                <th>Tgl Transaksi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan as $p)
                                <tr>
                                    <td class="align-middle fw-medium">{{ $p->nomor_pesanan }}</td>
                                    <td class="align-middle">
                                        <span class="d-block">Katering {{ ucfirst($p->tipe_layanan) }}</span>
                                    </td>
                                    <td class="align-middle fw-bold text-success">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                    <td class="align-middle ">
                                        <span class="badge text-bg-{{ $p->status_pembayaran_color }} mb-1">
                                            {{ $p->status_pembayaran_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge text-bg-{{ $p->status_pesanan_color }}">
                                            {{ $p->status_pesanan_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-muted">{{ $p->created_at->format('d/m/Y') }}</td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex gap-2 justify-content-center">
    <a href="{{ route('admin.pesanan.show', $p) }}"
       class="btn btn-sm btn-info text-white"
       title="Lihat Detail Pesanan">
        Lihat
    </a>

    <form action="{{ route('admin.pesanan.destroy', $p) }}"
          method="POST"
          onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Apakah Anda yakin ingin menghapus pesanan ini?');">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-sm btn-danger"
                title="Hapus Pesanan">
            Hapus
        </button>
    </form>
</div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada pesanan.</td></tr>
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
