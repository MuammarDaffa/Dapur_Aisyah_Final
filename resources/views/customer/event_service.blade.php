@extends('layouts.app')
@section('title', 'Layanan Event - ' . $service->name)
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('landing') }}#services" class="text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors">← Kembali ke Layanan</a>
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">Layanan {{ $service->name }}</h2>
        <!-- <p class="text-gray-500 mt-1">{{ $service->description }}</p> -->
    </div>

    <!-- {{-- Subtitle --}}
    <div class="mb-6 border-b border-gray-100 pb-4">
        <h3 class="text-lg font-bold text-gray-800">Pilih Paket atau Custom Menu</h3>
        <p class="text-sm text-gray-500 mt-0.5">Setiap paket siap saji dan custom menu berdiri sendiri sebagai pilihan utama untuk acara Anda.</p>
    </div> -->

    @if((!$service->hasFeature('packages') || $packages->isEmpty()) && !$service->hasFeature('full_custom'))
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
            <p class="text-gray-500 font-medium mb-4">Belum ada pilihan paket atau custom menu untuk layanan ini.</p>
            <a href="{{ route('landing') }}#services" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">Kembali</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- Daftar Paket (Setiap paket berdiri sendiri di level yang sama) --}}
            @if($service->hasFeature('packages') && $packages->isNotEmpty())
                @foreach($packages as $pkg)
                    <a href="{{ route('customer.event.package', [$service->id, $pkg->id]) }}" 
                       class="group bg-white rounded-2xl border border-gray-100 hover:border-orange-400 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden">
                        
                        {{-- Cover / Top Area --}}
                        <div class="h-48 w-full bg-gray-100 relative overflow-hidden">
                            @if($pkg->image)
                                <img src="{{ Storage::url($pkg->image) }}" alt="{{ $pkg->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-orange-100 via-amber-50 to-orange-50 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-orange-300 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full shadow-sm">
                                <span class="text-xs font-bold text-gray-800">{{ $pkg->total_portions }} Porsi</span>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="inline-block px-2.5 py-0.5 bg-orange-50 text-orange-700 font-semibold text-[11px] rounded-md mb-2">Paket Katering</span>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">{{ $pkg->name }}</h3>
                                @if($pkg->description)
                                    <p class="text-sm text-gray-500 mb-4 line-clamp-3 leading-relaxed">{{ $pkg->description }}</p>
                                @endif
                            </div>

                            <div class="pt-4 border-t border-gray-100 mt-4 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-400">Harga Paket</p>
                                    <p class="text-lg font-bold text-orange-600">{{ $pkg->formatted_price }}</p>
                                </div>
                                <span class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white group-hover:translate-x-1 transition-all shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <a href="{{ route('customer.event.custom', $service->id) }}" 
                   class="group bg-white rounded-2xl border border-gray-100 hover:border-blue-400 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden">
                    
                    {{-- Top Illustration Area --}}
                    <div class="h-48 w-full bg-gradient-to-br from-blue-100 via-indigo-50 to-blue-50 relative overflow-hidden flex items-center justify-center">
                        <svg class="w-16 h-16 text-blue-400 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Custom Menu</h3>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-3 leading-relaxed">
                                Susun menu secara bebas dan fleksibel sesuai selera dan kebutuhan acara Anda dengan minimal pemesanan mulai dari {{ $service->min_portion }} porsi.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-100 mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-400">Harga</p>
                                <p class="text-sm font-bold text-blue-600">Sesuai Pilihan Menu</p>
                            </div>
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white group-hover:translate-x-1 transition-all shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
