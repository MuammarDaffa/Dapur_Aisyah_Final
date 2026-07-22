@extends('layouts.app')
@section('title', 'Menu Kami')
@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="d-flex d-flex-column md:d-flex-row md:align-items-center justify-content-between g-3 mb-8">
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Menu <span class="text-primary">Kami</span></h2>
            <!-- <p class="fs-6 text-secondary mt-1">Daftar menu katering harian yang tersedia sesuai jadwal saat ini.</p> -->
        </div>
        @if($services->count() > 1)
        <div class="d-flex align-items-center g-3 overflow-x-auto pb-2 md:pb-0">
            <a href="{{ route('pelanggan.produk') }}"
               class="px-4 py-2 rounded small fw-bold {{ !request('service') ? 'bg-primary text-white text-white shadow-sm' : 'bg-white text-secondary border hover:bg-light' }}">
                Semua Layanan
            </a>
            @foreach($services as $srv)
            <a href="{{ route('pelanggan.produk', ['service' => $srv->id]) }}"
               class="px-4 py-2 rounded small fw-bold {{ request('service') == $srv->id ? 'bg-primary text-white text-white shadow-sm' : 'bg-white text-secondary border hover:bg-light' }}">
                {{ $srv->name }}
            </a>
            @endforeach
        </div>
        @endif
    </div>

    @if($menus->isNotEmpty())
    <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-3 xl:row-cols-4 g-3 mb-12">
        @foreach($menus as $menu)
            @php
                $canOrder = $menu->stok_tersisa > 0;
            @endphp
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:-translate-y-1 border border border-secondary d-flex d-flex-column {{ !$canOrder ? 'opacity-75 grayscale-[0.3]' : '' }}">
                <div class="position-relative h-44 d-flex align-items-center justify-content-center overflow-hidden">
                    @if(null)
                        <img src="{{ Storage::url(null) }}" alt="{{ $menu->nama_menu }}" class="w-100 h-100 object-cover">
                    @else
                        <div style="width: 64px; height: 64px;" class="rounded-2xl bg-white/60 backdrop-blur-sm d-flex align-items-center justify-content-center text-primary shadow-sm">
                            <svg style="width: 32px; height: 32px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    @endif

                    @if(!$canOrder)
                        <span class="position-absolute bg-dark/50 backdrop-blur-[2px] d-flex align-items-center justify-content-center text-white fw-bold fs-5 tracking-wider">HABIS</span>
                    @endif
                </div>

                <div class="p-5 d-flex-1 d-flex d-flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between g-3 mb-2">
                            <span class="small text-primary fw-bold uppercase tracking-wider">{{ $menu->layananKatering->name ?? '' }}</span>
                            <span class="d-inline-d-flex align-items-center g-3 small bg-primary text-white text-primary px-2.5 py-1 rounded-pill fw-bold border border border-primary">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $menu->hari }}</span>
                            </span>
                        </div>
                        <h3 class="fw-bold text-secondary text-base mb-1 leading-snug">{{ $menu->nama_menu }}</h3>
                        <p class="small text-secondary mb-4 line-clamp-2 leading-relaxed">{{ $menu->deskripsi }}</p>
                    </div>

                    <div class="pt-3 border-t border border-secondary d-flex align-items-center justify-content-between g-3">
                        <div>
                            <span class="small text-secondary d-block">Harga</span>
                            <span class="text-base fw-bold text-primary">{{ 'Rp ' . number_format($menu->harga, 0, ',', '.') }}</span>
                        </div>
                        <button type="button" disabled
                            class="d-inline-d-flex align-items-center g-3.5 px-4 py-2 bg-light text-secondary small fw-bold rounded cursor-not-allowed">
                            <span>Segera Hadir</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    {{-- Empty State --}}
    <div class="text-center py-16 px-4 bg-white rounded-2xl border border border-secondary shadow-sm max-w-2xl mx-auto my-8">
        <h3 class="fs-5 fw-bold text-secondary mb-2">Menu Belum Tersedia</h3>
        <p class="fs-6 text-secondary max-w-md mx-auto leading-relaxed">Saat ini belum tersedia menu harian. Silakan cek kembali nanti.</p>
    </div>
    @endif
</div>
@endsection
