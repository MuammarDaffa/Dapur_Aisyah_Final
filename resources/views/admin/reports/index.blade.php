@extends('layouts.admin')

@section('title', 'Rekapitulasi Penjualan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Rekapitulasi Penjualan</h2>
            <p class="text-sm text-gray-500 mt-1">Ringkasan data penjualan dan pesanan</p>
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
                    <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                </select>
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

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Pesanan</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($summary['total_orders']) }}</p>
                </div>
                <!-- <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📦</span>
                </div> -->
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Pendapatan</p>
                    <p class="text-3xl font-bold mt-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <!-- <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">💰</span>
                </div> -->
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Rata-rata Nilai Pesanan</p>
                    <p class="text-3xl font-bold mt-1">Rp {{ number_format($summary['average_order'], 0, ',', '.') }}</p>
                </div>
                <!-- <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📊</span>
                </div> -->
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Detail Pesanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">No. Pesanan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Pelanggan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Layanan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Total</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-orange-600 hover:text-orange-700 font-medium">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $order->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $order->cateringService->name ?? '-' }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$order->status" />
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-4xl">📊</span>
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
