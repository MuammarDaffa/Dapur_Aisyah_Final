@extends('layouts.app')
@section('title', 'Layanan Event - ' . $service->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('landing') }}#services" class="text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors">← Kembali ke Layanan</a>
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">Layanan {{ $service->name }}</h2>
        <p class="text-gray-500 mt-1">{{ $service->description }}</p>
    </div>

    {{-- Pilihan Mode Pemesanan --}}
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Pilih Metode Pemesanan</h3>
        <p class="text-sm text-gray-500">Silakan pilih antara paket tetap yang praktis atau susun menu khusus sesuai selera Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Paket Card --}}
        @if($service->hasFeature('packages'))
            <a href="{{ route('customer.event.packages', $service->id) }}" class="group block relative overflow-hidden rounded-2xl bg-white border-2 border-gray-100 hover:border-orange-500 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                <div class="p-6 sm:p-8">
                    <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="inline-block px-3 py-1 bg-orange-100 text-orange-700 font-semibold text-xs rounded-full mb-3">Praktis & Siap Saji</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">Paket Katering</h3>
                    <p class="text-sm text-gray-500 leading-relaxed mb-6">
                        Pilih dari daftar paket siap saji dengan komposisi menu dan harga tetap yang telah dirancang khusus oleh chef profesional kami.
                    </p>
                </div>
                <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between group-hover:bg-orange-50/50 transition-colors">
                    <span class="text-sm font-bold text-gray-700 group-hover:text-orange-600 transition-colors">Lihat Daftar Paket</span>
                    <span class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 group-hover:text-orange-600 group-hover:translate-x-1 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </a>
        @endif

        {{-- Custom Menu Card --}}
        @if($service->hasFeature('full_custom'))
            <a href="{{ route('customer.event.custom', $service->id) }}" class="group block relative overflow-hidden rounded-2xl bg-white border-2 border-gray-100 hover:border-blue-500 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                <div class="p-6 sm:p-8">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 font-semibold text-xs rounded-full mb-3">Fleksibel & Bebas</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Custom Menu</h3>
                    <p class="text-sm text-gray-500 leading-relaxed mb-6">
                        Susun menu secara bebas sesuai kebutuhan acara Anda dengan minimal pemesanan mulai dari {{ $service->min_portion }} porsi.
                    </p>
                </div>
                <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between group-hover:bg-blue-50/50 transition-colors">
                    <span class="text-sm font-bold text-gray-700 group-hover:text-blue-600 transition-colors">Susun Menu Sekarang</span>
                    <span class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </a>
        @endif
    </div>

</div>
@endsection
