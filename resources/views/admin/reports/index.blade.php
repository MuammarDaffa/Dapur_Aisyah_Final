@extends('layouts.admin')

@section('title', 'Rekapitulasi Penjualan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Rekapitulasi Penjualan</h2>
            <!-- <p class="fs-6 text-secondary mt-1">Ringkasan data penjualan dan pesanan</p> -->
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded shadow-md border border border-secondary p-5">
        <form action="{{ route('admin.reports') }}" method="GET" class="d-flex d-flex-wrap items-end g-3">
            <div class="d-flex-1 min-w-[140px]">
                <label class="form-label fw-bold">Periode</label>
                <select name="period"
                        class="form-select w-100 border border-secondary rounded shadow-sm focus:border border-primary fs-6">
                    <option value="">Semua Periode</option>
                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <div class="d-flex-1 min-w-[140px]">
                <label class="form-label fw-bold">Status</label>
                <select name="status"
                        class="form-select w-100 border border-secondary rounded shadow-sm focus:border border-primary fs-6">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                </select>
            </div>
            <div class="d-flex-1 min-w-[140px]">
                <label class="form-label fw-bold">Tanggal</label>
                <input type="date" lang="id-ID" name="date" value="{{ request('date') }}"
                       class="w-100 border border-secondary rounded shadow-sm focus:border border-primary fs-6">
            </div>
            <button type="submit"
                    class="hover: hover: text-white px-5 py-2.5 rounded fw-bold shadow-md hover:shadow fs-6">
                Terapkan Filter
            </button>
            <a href="{{ route('admin.reports') }}"
               class="text-secondary hover:text-secondary px-4 py-2.5 rounded border border border-secondary hover:bg-light fs-6 fw-medium">
                Reset
            </a>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded shadow-md overflow-hidden border border border-secondary">
        <div class="px-6 py-4 border-b border border-secondary">
            <h3 class="fw-bold text-secondary">Daftar Pesanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-100 fs-6">
                <thead class="border-b border border-secondary">
                    <tr>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Nomor Order</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Pelanggan</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Layanan</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Total</th>
                        <th class="text-center px-6 py-4 fw-bold text-secondary">Status</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-primary text-white/30">
                            <td class="px-6 py-4 fw-medium text-secondary">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 fw-medium text-secondary">{{ $order->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-secondary">{{ $order->cateringService->name ?? '-' }}</td>
                            <td class="px-6 py-4 fw-bold text-success">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-pill small fw-medium {{ match($order->status) { 'processing'=>'bg-info text-white text-info','on_delivery'=>'bg-purple-100 text-purple-700','completed'=>'bg-success text-white text-success','cancelled'=>'bg-danger text-white text-danger', default=>'bg-light text-secondary' } }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-secondary">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="d-flex d-flex-column align-items-center g-3">
                                    <div style="width: 64px; height: 64px;" class="bg-light rounded-pill d-flex align-items-center justify-content-center mb-1">
                                        <svg style="width: 32px; height: 32px;" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    </div>
                                    <p class="text-secondary fw-medium">Tidak ada data pesanan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border border-secondary">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
