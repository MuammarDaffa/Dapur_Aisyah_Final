@extends('layouts.app')
@section('title', 'Daftar Paket - ' . $service->name)
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('pelanggan.event.service', $service->id) }}" class="d-inline-d-flex align-items-center fs-6 fw-medium text-primary hover:text-primary">
            <svg style="width: 16px; height: 16px;" class="me-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Pilih Layanan</span>
        </a>
        <h2 class="fs-3 sm:fs-2 fw-bold text-secondary mt-2">Daftar Paket Katering - {{ $service->name }}</h2>
        <p class="text-secondary mt-1">Pilih paket katering tetap (fixed package) yang sesuai dengan kapasitas dan selera acara Anda.</p>
    </div>

    @if($pakets->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border border-secondary shadow-sm">
            <p class="text-secondary fw-medium mb-4">Belum ada paket yang tersedia untuk layanan ini.</p>
            <a href="{{ route('pelanggan.event.service', $service->id) }}" class="px-6 py-2.5 bg-light text-secondary fw-medium rounded hover:bg-light">Kembali</a>
        </div>
    @else
        <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-3 g-3">
            @foreach($pakets as $pkg)
                <div class="bg-white rounded-2xl border border border-secondary hover:border border-primary shadow-sm hover:shadow d-flex d-flex-column overflow-hidden group">
                    {{-- Cover Image --}}
                    <div style="height: 192px;" class="w-100 bg-light position-relative overflow-hidden">
                        @if($pkg->image)
                            <img src="{{ Storage::url($pkg->image) }}" alt="{{ $pkg->name }}" class="w-100 h-100 object-cover group- transition-">
                        @else
                            <div class="w-100 h-100 via-amber-50 d-flex align-items-center justify-content-center">
                                <svg style="width: 64px; height: 64px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif
                        <div class="position-absolute bg-white/90 backdrop-blur-sm px-3 py-1 rounded-pill shadow-sm">
                            <span class="small fw-bold text-secondary">{{ $pkg->total_portions }} Porsi</span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 d-flex-1 d-flex d-flex-column justify-content-between">
                        <div>
                            <h3 class="fs-4 fw-bold text-secondary mb-2 group-hover:text-primary">{{ $pkg->name }}</h3>
                            @if($pkg->deskripsi)
                                <p class="fs-6 text-secondary mb-4 line-clamp-3 leading-relaxed">{{ $pkg->deskripsi }}</p>
                            @endif
                        </div>

                        <div class="pt-4 border-t border border-secondary mt-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="small text-secondary">Harga Paket</p>
                                <p class="fs-5 fw-bold text-primary">{{ $pkg->formatted_price }}</p>
                            </div>
                            <a href="{{ route('pelanggan.event.package', [$service->id, $pkg->id]) }}" 
                               class="px-5 py-2.5 bg-primary text-white hover:bg-primary text-white text-white fw-bold fs-6 rounded shadow-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
