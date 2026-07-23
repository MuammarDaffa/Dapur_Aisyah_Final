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
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach(['diproses'=>'Diproses','dikirim'=>'Dikirim','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'] as $k=>$v)
                                <option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="date_type" class="form-select">
                            <option value="tanggal_pesanan" {{ request('date_type') == 'tanggal_pesanan' ? 'selected' : '' }}>Tgl Pengiriman</option>
                            <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Tgl Pesanan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="form-control">
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
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tgl Pesanan</th>
                                <th>Tgl Pengiriman</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan as $p)
                                <tr>
                                    <td class="align-middle fw-medium">{{ $p->nomor_pesanan }}</td>
                                    <td class="align-middle">{{ $p->user->name ?? '-' }}</td>
                                    <td class="align-middle">
                                        <span class="d-block">{{ $p->layanan->nama ?? '-' }}</span>
                                        <small class="text-muted">{{ ucfirst($p->layanan->tipe ?? '') }}</small>
                                    </td>
                                    <td class="align-middle fw-bold text-success">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                    <td class="align-middle">
                                        <span class="badge {{ match($p->status) { 'diproses'=>'text-bg-info','dikirim'=>'text-bg-primary','selesai'=>'text-bg-success','dibatalkan'=>'text-bg-danger', default=>'text-bg-secondary' } }}">
                                            {{ $p->status_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-muted">{{ $p->created_at->format('d/m/Y') }}</td>
                                    <td class="align-middle fw-medium text-primary">{{ \Carbon\Carbon::parse($p->tanggal_pesanan)->format('d/m/Y') }}</td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.pesanan.show', $p) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail Pesanan">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.pesanan.destroy', $p) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Apakah Anda yakin ingin menghapus pesanan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Pesanan">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada pesanan.</td></tr>
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
