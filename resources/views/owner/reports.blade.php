@extends('layouts.owner')

@section('title', 'Rekapitulasi Penjualan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Rekapitulasi Penjualan</h2>
        <p class="text-sm text-gray-500 mt-1">Ringkasan data penjualan bisnis Anda</p>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
        <form action="{{ route('owner.reports') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Periode</label>
                <select name="period"
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-teal-500 focus:border-teal-500 text-sm">
                    <option value="">Semua Periode</option>
                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <button type="submit"
                    class="bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all text-sm">
                Terapkan
            </button>
            <a href="{{ route('owner.reports') }}"
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
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📦</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-white/20">
                <p class="text-blue-200 text-xs">Pesanan dengan status selesai</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Pendapatan</p>
                    <p class="text-3xl font-bold mt-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">💰</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-white/20">
                <p class="text-green-200 text-xs">Total pendapatan dari pesanan selesai</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Rata-rata Nilai Pesanan</p>
                    <p class="text-3xl font-bold mt-1">Rp {{ number_format($summary['average_order'], 0, ',', '.') }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📊</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-white/20">
                <p class="text-purple-200 text-xs">Rata-rata total per pesanan</p>
            </div>
        </div>
    </div>

    {{-- Chart Placeholder --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 text-lg mb-4 flex items-center gap-2">
            <span>📈</span> Tren Pendapatan
        </h3>
        <div class="h-64 flex items-center justify-center">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple bar chart for revenue
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Pesanan', 'Total Pendapatan (x1000)', 'Rata-rata (x1000)'],
                datasets: [{
                    label: 'Ringkasan',
                    data: [
                        {{ $summary['total_orders'] }},
                        {{ round($summary['total_revenue'] / 1000) }},
                        {{ round($summary['average_order'] / 1000) }}
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(139, 92, 246, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { family: 'Poppins' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', size: 11 } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
