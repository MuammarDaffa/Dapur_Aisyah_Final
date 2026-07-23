@extends('layouts.app')
@section('title', 'Fitur Sedang Dikembangkan')
@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto text-center bg-white rounded-2xl shadow-sm border border-secondary p-12">
        <div style="width: 80px; height: 80px;" class="bg-primary/10 text-primary rounded-full d-flex align-items-center justify-content-center mx-auto mb-6">
            <svg style="width: 40px; height: 40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
        <h2 class="fs-3 fw-bold text-secondary mb-4">Fitur Sedang Dikembangkan</h2>
        <p class="fs-5 text-secondary mb-8">Mohon maaf, fitur pemesanan saat ini sedang dalam tahap pengembangan dan belum dapat digunakan. Silakan kembali lagi nanti.</p>
        <a href="{{ route('pelanggan.dashboard') }}" class="btn btn-primary px-6 py-3 rounded fw-bold text-white shadow-sm hover:shadow">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
