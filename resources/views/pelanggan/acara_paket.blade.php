@extends('layouts.app')
@section('title', 'Detail Paket - ' . $paket->name)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('pelanggan.acara.service', $service->id) }}" class="d-inline-d-flex align-items-center fs-6 fw-medium text-primary hover:text-primary">
            <svg style="width: 16px; height: 16px;" class="me-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Pilih Layanan</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border border-secondary overflow-hidden mb-8">
        {{-- Cover Image --}}
        @if($paket->image)
            <div style="height: 256px;" class="w-100 sm:h-80 bg-light position-relative overflow-hidden">
                <img src="{{ Storage::url($paket->image) }}" alt="{{ $paket->name }}" class="w-100 h-100 object-cover">
            </div>
        @endif

        <div class="p-6 sm:p-10">
            {{-- Section: Informasi Paket --}}
            <div>
                <div class="d-flex d-flex-column sm:d-flex-row sm:align-items-center justify-content-between g-3">
                    <div>
                        <span class="d-inline-block px-3 py-1 bg-primary text-white text-primary fw-bold small rounded-pill mb-2">Paket Katering Tetap</span>
                        <h1 class="fs-3 sm:fs-2 fw-bold text-secondary">{{ $paket->name }}</h1>
                        <p class="fs-6 fw-medium text-secondary mt-1">Layanan: {{ $service->name }}</p>
                    </div>
                    <div class="text-start sm:text-end">
                        <p class="small text-secondary">Harga Paket</p>
                        <p class="fs-3 sm:fs-2 fw-bold text-primary">{{ $paket->formatted_price }}</p>
                        <p class="small fw-bold text-secondary mt-1">{{ $paket->total_portions }} Porsi</p>
                    </div>
                </div>

                @if($paket->deskripsi)
                    <p class="text-secondary leading-relaxed fs-6 sm:text-base mt-4">{{ $paket->deskripsi }}</p>
                @endif
            </div>

            @php
                $menus = $paket->getIncludedMenus();
                $serving = $paket->getIncludedServingTypes()->first();
                $benefits = $paket->benefits ?? [];
            @endphp

            <hr class="border border-secondary my-8">

            {{-- Section: Daftar Menu Dalam Paket --}}
            <div>
                <h3 class="text-base fw-bold text-secondary uppercase tracking-wider mb-6">Daftar Menu Dalam Paket</h3>
                <div class="space-y-6">
                    @forelse($menus as $menu)
                        <div>
                            <h4 class="fw-bold text-secondary text-base sm:fs-5">{{ $menu->name }}</h4>
                            @if(!empty($menu->items))
                                @if(is_array($menu->items) || $menu->items instanceof \Traversable)
                                    <ul class="mt-2 space-y-1.5 text-secondary fs-6 sm:text-base">
                                        @foreach($menu->items as $item)
                                            @if(!empty($item))
                                                <li class="d-flex items-start g-3">
                                                    <span class="text-primary fw-bold mt-0.5">•</span>
                                                    <span>{{ is_string($item) ? $item : json_encode($item) }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="fs-6 sm:text-base text-secondary mt-1">{{ $menu->items }}</p>
                                @endif
                            @endif
                        </div>
                        @if(!$loop->last)
                            <hr class="border border-secondary my-6">
                        @endif
                    @empty
                        <p class="fs-6 text-secondary">Belum ada menu yang dikonfigurasi untuk paket ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Section: Penyajian --}}
            @if($serving)
                <hr class="border border-secondary my-8">
                <div>
                    <h3 class="text-base fw-bold text-secondary uppercase tracking-wider mb-3">Penyajian</h3>
                    <p class="fw-bold text-secondary text-base sm:fs-5">{{ $serving->name }}</p>
                    <p class="fs-6 text-secondary mt-1">Selesai disajikan lengkap sesuai standar hidangan katering kami.</p>
                </div>
            @endif

            {{-- Section: Benefit --}}
            @if(!empty($benefits))
                <hr class="border border-secondary my-8">
                <div>
                    <h3 class="text-base fw-bold text-secondary uppercase tracking-wider mb-4">Benefit Tambahan</h3>
                    <ul class="d-flex flex-column gap-2 text-secondary fs-6 sm:text-base">
                        @foreach($benefits as $b)
                            <li class="d-flex items-start g-3.5">
                                <svg style="width: 20px; height: 20px;" class="text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>{{ is_string($b) ? $b : json_encode($b) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <hr class="border border-secondary my-8">

                <div class="d-flex d-flex-column sm:d-flex-row align-items-center justify-content-between g-3 pt-6 border-t border border-secondary">
                    <div>
                        <p class="fs-6 text-secondary fw-medium">Total Harga Paket ({{ $paket->total_portions }} Porsi)</p>
                        <p class="fs-3 sm:fs-2 fw-bold text-primary mt-0.5">{{ $paket->formatted_price }}</p>
                    </div>
                </div>
        </div>
    </div>

</div>
@endsection
