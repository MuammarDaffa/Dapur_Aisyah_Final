@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary mb-4">
            <div class="card-body p-3">
                <form action="{{ route('admin.orders') }}" method="GET" class="row gx-2 gy-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari order/nama..." class="form-control">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach(['processing'=>'Diproses','on_delivery'=>'Dikirim','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $k=>$v)
                                <option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="date_type" class="form-select">
                            <option value="order_date" {{ request('date_type') == 'order_date' ? 'selected' : '' }}>Tgl Pengiriman</option>
                            <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Tgl Order</option>
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
                                <th>Order</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tgl Order</th>
                                <th>Tgl Pengiriman</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="align-middle fw-medium">{{ $order->order_number }}</td>
                                    <td class="align-middle">{{ $order->user->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $order->cateringService->name ?? '-' }}</td>
                                    <td class="align-middle fw-bold text-success">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td class="align-middle">
                                        <span class="badge {{ match($order->status) { 'processing'=>'text-bg-info','on_delivery'=>'text-bg-primary','completed'=>'text-bg-success','cancelled'=>'text-bg-danger', default=>'text-bg-secondary' } }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-muted">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="align-middle fw-medium text-primary">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail Pesanan">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Apakah Anda yakin ingin menghapus pesanan ini?');">
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
            @if($orders->hasPages())
            <div class="card-footer">
                {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
