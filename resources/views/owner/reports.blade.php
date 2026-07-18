@extends('layouts.owner')

@section('title', 'Rekapitulasi Penjualan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h2 class="fs-3 fw-bold text-secondary">Rekapitulasi Penjualan</h2>
        <p class="fs-6 text-secondary mt-1">Ringkasan data penjualan bisnis Anda</p>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded shadow-md border border border-secondary p-5">
        <form action="{{ route('owner.reports') }}" method="GET" class="d-flex d-flex-wrap items-end g-3">
            <div class="d-flex-1 min-w-[160px]">
                <label class="form-label fw-bold">Periode</label>
                <select name="period"
                        class="form-select w-100 border border-secondary rounded shadow-sm fs-6">
                    <option value="">Semua Periode</option>
                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <button type="submit"
                    class="hover: hover: text-white px-5 py-2.5 rounded fw-bold shadow-md hover:shadow fs-6">
                Terapkan
            </button>
            <a href="{{ route('owner.reports') }}"
               class="text-secondary hover:text-secondary px-4 py-2.5 rounded border border border-secondary hover:bg-light fs-6 fw-medium">
                Reset
            </a>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="row row-cols-1 md:row-cols-3 g-3">
        <div class="rounded shadow p-6 text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-info fs-6 fw-medium">Total Pesanan</p>
                    <p class="fs-2 fw-bold mt-1">{{ number_format($summary['total_orders']) }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded d-flex align-items-center justify-content-center">
                    <span class="fs-3">📦</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-white/20">
                <p class="text-info small">Pesanan dengan status selesai</p>
            </div>
        </div>

        <div class="rounded shadow p-6 text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-success fs-6 fw-medium">Total Pendapatan</p>
                    <p class="fs-2 fw-bold mt-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded d-flex align-items-center justify-content-center">
                    <span class="fs-3">💰</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-white/20">
                <p class="text-success small">Total pendapatan dari pesanan selesai</p>
            </div>
        </div>

        <div class="rounded shadow p-6 text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-purple-100 fs-6 fw-medium">Rata-rata Nilai Pesanan</p>
                    <p class="fs-2 fw-bold mt-1">Rp {{ number_format($summary['average_order'], 0, ',', '.') }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded d-flex align-items-center justify-content-center">
                    <span class="fs-3">📊</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-white/20">
                <p class="text-purple-200 small">Rata-rata total per pesanan</p>
            </div>
        </div>
    </div>

    {{-- Chart Placeholder --}}
    <div class="bg-white rounded shadow-md border border border-secondary p-6">
        <h3 class="fw-bold text-secondary fs-5 mb-4 d-flex align-items-center g-3">
            <span>📈</span> Tren Pendapatan
        </h3>
        <div style="height: 256px;" class="d-flex align-items-center justify-content-center">
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
