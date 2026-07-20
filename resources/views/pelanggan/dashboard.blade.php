@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome -->
    <div class="rounded-2xl p-8 mb-8 text-white">
        <h2 class="fs-3 fw-bold d-flex align-items-center">Selamat datang, {{ auth()->user()->name }}! <svg class="w-7 h-7 d-inline-block text-primary ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></h2>
        <p class="mt-2 text-primary">Siap pesan katering hari ini?</p>
        <a href="{{ route('pelanggan.produk') }}" class="d-inline-d-flex align-items-center mt-4 px-6 py-2.5 bg-white text-primary fw-bold rounded-pill hover:shadow">
            <span>Lihat Menu</span>
            <svg style="width: 16px; height: 16px;" class="ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="row row-cols-1 row-cols-md-3 g-3 mb-8">
        <div class="card shadow-sm mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs-6 text-secondary">Total Pesanan</p>
                    <p class="fs-3 fw-bold text-secondary">{{ auth()->user()->pesanan()->count() }}</p>
                </div>
                <div style="width: 48px; height: 48px;" class="rounded bg-primary text-white text-primary d-flex align-items-center justify-content-center flex-shrink-0">
                    <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs-6 text-secondary">Keranjang</p>
                    <p class="fs-3 fw-bold text-secondary">{{ $cartCount }}</p>
                </div>
                <div style="width: 48px; height: 48px;" class="rounded bg-primary text-white text-primary d-flex align-items-center justify-content-center flex-shrink-0">
                    <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs-6 text-secondary">Ulasan Diberikan</p>
                    <p class="fs-3 fw-bold text-secondary">{{ auth()->user()->ulasan()->count() }}</p>
                </div>
                <div style="width: 48px; height: 48px;" class="rounded bg-primary text-white text-primary d-flex align-items-center justify-content-center flex-shrink-0">
                    <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Pesanan -->
    <div class="bg-white rounded shadow-sm border border border-secondary">
        <div class="p-6 border-b border border-secondary d-flex justify-content-between align-items-center">
            <h3 class="fs-5 fw-bold text-secondary">Pesanan Terbaru</h3>
            <a href="{{ route('pelanggan.pesanan') }}" class="d-inline-d-flex align-items-center fs-6 text-primary hover:text-primary fw-medium">
                <span>Lihat Semua</span>
                <svg style="width: 16px; height: 16px;" class="ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentOrders as $pesanan)
                <a href="{{ route('pelanggan.pesanan.show', $pesanan) }}" class="d-flex align-items-center justify-content-between p-4 hover:bg-primary text-white/50">
                    <div class="d-flex align-items-center space-x-4">
                        <div style="height: 40px;" class="w-10 bg-primary text-white text-primary rounded d-flex align-items-center justify-content-center flex-shrink-0">
                            <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <div>
                            <p class="fw-medium text-secondary">{{ $pesanan->nomor_pesanan }}</p>
                            <p class="small text-secondary">{{ $pesanan->layananKatering->name ?? 'Katering' }} · {{ $pesanan->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="fw-bold text-secondary">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</p>
                        <span class="d-inline-block small px-2 py-0.5 rounded-pill fw-medium {{ match($pesanan->status) { 'diproses' => 'bg-info text-white text-info', 'dikirim' => 'bg-purple-100 text-purple-700', 'selesai' => 'bg-success text-white text-success', 'dibatalkan' => 'bg-danger text-white text-danger', default => 'bg-light text-secondary' } }}">
                            {{ $pesanan->status_label }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-secondary">
                    <div style="width: 64px; height: 64px;" class="mx-auto mb-3 text-secondary d-flex align-items-center justify-content-center">
                        <svg style="width: 48px; height: 48px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p>Belum ada pesanan. <a href="{{ route('pelanggan.produk') }}" class="text-primary fw-medium hover:underline">Pesan sekarang</a></p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
