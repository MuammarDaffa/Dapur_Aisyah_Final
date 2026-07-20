@extends('layouts.app')
@section('title', 'Layanan Acara - ' . $service->name)
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('landing') }}#services" class="d-inline-d-flex align-items-center fs-6 fw-medium text-primary hover:text-primary">
            <svg style="width: 16px; height: 16px;" class="me-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Layanan</span>
        </a>
        <h2 class="fs-3 sm:fs-2 fw-bold text-secondary mt-2">Layanan {{ $service->name }}</h2>
        <!-- <p class="text-secondary mt-1">{{ $service->deskripsi }}</p> -->
    </div>

    <!-- {{-- Subtitle --}}
    <div class="mb-6 border-b border border-secondary pb-4">
        <h3 class="fs-5 fw-bold text-secondary">Pilih Paket atau Custom Menu</h3>
        <p class="fs-6 text-secondary mt-0.5">Setiap paket siap saji dan custom menu berdiri sendiri sebagai pilihan utama untuk acara Anda.</p>
    </div> -->

    @if((!$service->hasFeature('packages') || $pakets->isEmpty()) && !$service->hasFeature('full_custom'))
        <div class="bg-white rounded-2xl p-12 text-center border border border-secondary shadow-sm">
            <p class="text-secondary fw-medium mb-4">Belum ada pilihan paket atau custom menu untuk layanan ini.</p>
            <a href="{{ route('landing') }}#services" class="px-6 py-2.5 bg-light text-secondary fw-medium rounded hover:bg-light">Kembali</a>
        </div>
    @else
        <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-3 g-3">
            
            {{-- Daftar Paket (Setiap paket berdiri sendiri di level yang sama) --}}
            @if($service->hasFeature('packages') && $pakets->isNotEmpty())
                @foreach($pakets as $pkg)
                    <a href="{{ route('pelanggan.acara.package', [$service->id, $pkg->id]) }}" 
                       class="group bg-white rounded-2xl border border border-secondary hover:border border-primary shadow-sm hover:shadow d-flex d-flex-column overflow-d-none">
                        
                        {{-- Cover / Top Area --}}
                        <div style="height: 192px;" class="w-100 bg-light position-relative overflow-hidden">
                            @if($pkg->image)
                                <img src="{{ Storage::url($pkg->image) }}" alt="{{ $pkg->name }}" class="w-100 h-100 object-cover group- transition-">
                            @else
                                <div class="w-100 h-100 via-amber-50 d-flex align-items-center justify-content-center">
                                    <svg style="width: 64px; height: 64px;" class="text-primary group- transition-" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <span class="d-inline-block px-2.5 py-0.5 bg-primary text-white text-primary fw-bold text-[11px] rounded mb-2">Paket Katering</span>
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
                                <span style="width: 32px; height: 32px;" class="rounded-pill bg-primary text-white text-primary d-flex align-items-center justify-content-center group-hover:bg-primary text-white group-hover:text-white group-hover:translate-x-1 shadow-2xs">
                                    <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif

            {{-- Custom Menu Card (Berada pada posisi yang setara dengan kartu Paket) --}}
            @if($service->hasFeature('full_custom'))
                <a href="{{ route('pelanggan.event.custom', $service->id) }}" 
                   class="group bg-white rounded-2xl border border border-secondary hover:border-blue-400 shadow-sm hover:shadow d-flex d-flex-column overflow-d-none">
                    
                    {{-- Top Illustration Area --}}
                    <div style="height: 192px;" class="w-100 via-indigo-50 position-relative overflow-hidden d-flex align-items-center justify-content-center">
                        <svg style="width: 64px; height: 64px;" class="text-info group- transition-" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <path d="M12 11h4"/>
                            <path d="M12 16h4"/>
                            <path d="M8 11h.01"/>
                            <path d="M8 16h.01"/>
                        </svg>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 d-flex-1 d-flex d-flex-column justify-content-between">
                        <div>
                            <h3 class="fs-4 fw-bold text-secondary mb-2 group-hover:text-info">Custom Menu</h3>
                            <p class="fs-6 text-secondary mb-4 line-clamp-3 leading-relaxed">
                                Susun menu secara bebas dan fleksibel sesuai selera dan kebutuhan acara Anda dengan minimal pemesanan mulai dari {{ $service->min_portion }} porsi.
                            </p>
                        </div>

                        <div class="pt-4 border-t border border-secondary mt-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="small text-secondary">Harga</p>
                                <p class="fs-6 fw-bold text-info">Sesuai Pilihan Menu</p>
                            </div>
                            <span style="width: 32px; height: 32px;" class="rounded-pill bg-info text-white text-info d-flex align-items-center justify-content-center group-hover:bg-info text-white group-hover:text-white group-hover:translate-x-1 shadow-2xs">
                                <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endif

        </div>
    @endif

</div>
@endsection
