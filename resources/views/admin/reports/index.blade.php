@extends('layouts.admin')

@section('title', 'Rekapitulasi Penjualan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Rekapitulasi Penjualan</h2>
            <!-- <p class="text-sm text-gray-500 mt-1">Ringkasan data penjualan dan pesanan</p> -->
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
        <form action="{{ route('admin.reports') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Periode</label>
                <select name="period"
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                    <option value="">Semua Periode</option>
                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Status</label>
                <select name="status"
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Tanggal</label>
                <input type="date" lang="id-ID" name="date" value="{{ request('date') }}"
                       class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
            </div>
            <button type="submit"
                    class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all text-sm">
                Terapkan Filter
            </button>
            <a href="{{ route('admin.reports') }}"
               class="text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 transition-all text-sm font-medium">
                Reset
            </a>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Daftar Pesanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Nomor Order</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Pelanggan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Layanan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Total</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $order->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $order->cateringService->name ?? '-' }}</td>
                            <td class="px-6 py-4 font-bold text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ match($order->status) { 'processing'=>'bg-blue-100 text-blue-700','on_delivery'=>'bg-purple-100 text-purple-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700', default=>'bg-gray-100 text-gray-700' } }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-1">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    </div>
                                    <p class="text-gray-400 font-medium">Tidak ada data pesanan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
