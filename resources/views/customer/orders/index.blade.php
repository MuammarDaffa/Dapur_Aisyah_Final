@extends('layouts.app')
@section('title', 'Pesanan Saya')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="fs-3 fw-bold text-secondary mb-6 d-flex align-items-center">
        <svg class="w-7 h-7 text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        <span>Pesanan <span class="text-primary">Saya</span></span>
    </h2>

    <!-- Status Filter -->
    <div class="d-flex d-flex-wrap g-3 mb-6">
        <a href="{{ route('customer.pesanan') }}" class="px-4 py-2 rounded-pill fs-6 fw-medium {{ !request('status') ? 'bg-primary text-white text-white' : 'bg-light text-secondary hover:bg-light' }}">Semua</a>
        @foreach(['diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $key => $label)
            <a href="{{ route('customer.pesanan', ['status' => $key]) }}" class="px-4 py-2 rounded-pill fs-6 fw-medium {{ request('status') == $key ? 'bg-primary text-white text-white' : 'bg-light text-secondary hover:bg-light' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="d-flex flex-column gap-3">
        @forelse($pesanan as $pesanan)
            <a href="{{ route('customer.pesanan.show', $pesanan) }}" class="d-block bg-white rounded p-5 shadow-sm border border border-secondary hover:shadow-md transition-shadow">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center space-x-3">
                        <div style="height: 40px;" class="w-10 bg-primary text-white text-primary rounded d-flex align-items-center justify-content-center flex-shrink-0">
                            <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <div>
                            <p class="fw-bold text-secondary">{{ $pesanan->nomor_pesanan }}</p>
                            <p class="small text-secondary">{{ $pesanan->layananKatering->name ?? 'Katering' }} · {{ $pesanan->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <span class="small px-3 py-1 rounded-pill fw-medium {{ match($pesanan->status) { 'diproses' => 'bg-info text-white text-info', 'dikirim' => 'bg-purple-100 text-purple-700', 'selesai' => 'bg-success text-white text-success', 'dibatalkan' => 'bg-danger text-white text-danger', default => 'bg-light text-secondary' } }}">
                        {{ $pesanan->status_label }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center fs-6">
                    <span class="text-secondary">Tanggal: {{ $pesanan->tanggal_pesanan->format('d M Y') }} · {{ ucfirst($pesanan->metode_pengambilan) }}</span>
                    <span class="fw-bold text-primary">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                </div>
            </a>
        @empty
            <div class="bg-white rounded p-12 text-center shadow-sm">
                <div style="width: 64px; height: 64px;" class="mx-auto mb-3 text-secondary d-flex align-items-center justify-content-center">
                    <svg style="width: 48px; height: 48px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <p class="text-secondary">Belum ada pesanan.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $pesanan->withQueryString()->links() }}</div>
</div>
@endsection
