@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('content')
<div class="bg-white rounded shadow-sm border border border-secondary mb-6 p-4">
    <form action="{{ route('admin.orders') }}" method="GET" class="d-flex d-flex-wrap g-3 items-end">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari order/nama..." class="form-control px-4 py-2 rounded border border border-secondary fs-6 d-flex-1 min-w-[180px]">
        <select name="status" class="form-select px-4 py-2 rounded border border border-secondary fs-6">
            <option value="">Semua Status</option>
            @foreach(['processing'=>'Diproses','on_delivery'=>'Dikirim','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $k=>$v)
                <option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
        <select name="date_type" class="form-select px-4 py-2 rounded border border border-secondary fs-6 focus:border border-primary -0">
            <option value="order_date" {{ request('date_type') == 'order_date' ? 'selected' : '' }}>Tanggal Pengiriman</option>
            <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Tanggal Order</option>
        </select>
        <input type="date" lang="id-ID" name="filter_date" value="{{ request('filter_date') }}" class="px-4 py-2 rounded border border border-secondary fs-6 focus:border border-primary -0">
        <button class="btn btn-primary">Filter</button>
    </form>
</div>
<div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-100 fs-6 min-w-[800px]">
        <thead class="bg-light text-secondary small uppercase tracking-wider">
            <tr>
                <th class="px-6 py-3 text-start">Order</th>
                <th class="px-6 py-3 text-start">Pelanggan</th>
                <th class="px-6 py-3 text-start">Layanan</th>
                <th class="px-6 py-3 text-start">Total</th>
                <th class="px-6 py-3 text-start">Status</th>
                <th class="px-6 py-3 text-start">Tanggal Order</th>
                <th class="px-6 py-3 text-start">Tanggal Pengiriman</th>
                <th class="px-6 py-3 text-start">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($orders as $order)
                <tr class="hover:bg-light">
                    <td class="px-6 py-4 fw-medium">{{ $order->order_number }}</td>
                    <td class="px-6 py-4">{{ $order->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $order->cateringService->name ?? '-' }}</td>
                    <td class="px-6 py-4 fw-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-pill small fw-medium {{ match($order->status) { 'processing'=>'bg-info text-white text-info','on_delivery'=>'bg-purple-100 text-purple-700','completed'=>'bg-success text-white text-success','cancelled'=>'bg-danger text-white text-danger', default=>'bg-light text-secondary' } }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-secondary">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 fw-medium text-primary">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="d-flex align-items-center g-3">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               title="Lihat Detail Pesanan"
                               class="text-secondary hover:text-primary">
                                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline-d-flex align-items-center"
                                  onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Apakah Anda yakin ingin menghapus pesanan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Hapus Pesanan"
                                        class="btn btn-outline-danger text-secondary hover:text-danger">
                                    <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-6 py-8 text-center text-secondary">Tidak ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $orders->withQueryString()->links() }}</div>
@endsection
