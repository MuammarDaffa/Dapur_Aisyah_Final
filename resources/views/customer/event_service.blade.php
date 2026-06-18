@extends('layouts.app')
@section('title', 'Layanan Event - ' . $service->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('landing') }}#services" class="text-sm text-orange-500 hover:text-orange-600">← Kembali ke Layanan</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Layanan {{ $service->name }}</h2>
        <p class="text-gray-500">{{ $service->description }}</p>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- Paket Cards --}}
        @if($service->hasFeature('packages') && $packages->isNotEmpty())
            @foreach($packages as $pkg)
                <a href="{{ route('customer.event.package', [$service->id, $pkg->id]) }}" class="group block relative overflow-hidden rounded-2xl bg-white border border-gray-100 hover:border-orange-400 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="p-6">
                        <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                            📦
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $pkg->name }}</h3>
                        @if($pkg->description)
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $pkg->description }}</p>
                        @endif
                        <div class="flex items-center justify-between border-t border-gray-50 pt-4 mt-auto">
                            <span class="text-lg font-bold text-orange-600">{{ $pkg->formatted_price }}</span>
                            <span class="px-3 py-1 bg-orange-50 text-orange-600 text-xs font-bold rounded-lg">{{ $pkg->total_portions }} Porsi</span>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif

        {{-- Custom Menu Card --}}
        @if($service->hasFeature('full_custom'))
            <a href="{{ route('customer.event.custom', $service->id) }}" class="group block relative overflow-hidden rounded-2xl bg-white border border-gray-100 hover:border-blue-400 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="p-6">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                        🍽️
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Custom Menu</h3>
                    <p class="text-sm text-gray-500 mb-4">Pilih menu secara bebas dengan minimal pemesanan 30 porsi.</p>
                    <div class="flex items-center justify-end border-t border-gray-50 pt-4 mt-auto">
                        <span class="text-sm font-bold text-blue-600 group-hover:translate-x-1 transition-transform flex items-center gap-1">Buat Sekarang <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
                    </div>
                </div>
            </a>
        @endif
    </div>

</div>
@endsection
