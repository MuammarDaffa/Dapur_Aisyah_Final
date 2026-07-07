@extends('layouts.app')
@section('title', 'Daftar Paket - ' . $service->name)
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('customer.event.service', $service->id) }}" class="text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors">← Kembali ke Pilih Layanan</a>
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">Daftar Paket Katering - {{ $service->name }}</h2>
        <p class="text-gray-500 mt-1">Pilih paket katering tetap (fixed package) yang sesuai dengan kapasitas dan selera acara Anda.</p>
    </div>

    @if($packages->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
            <p class="text-gray-500 font-medium mb-4">Belum ada paket yang tersedia untuk layanan ini.</p>
            <a href="{{ route('customer.event.service', $service->id) }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">Kembali</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($packages as $pkg)
                <div class="bg-white rounded-2xl border border-gray-100 hover:border-orange-400 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden group">
                    {{-- Cover Image --}}
                    <div class="h-48 w-full bg-gray-100 relative overflow-hidden">
                        @if($pkg->image)
                            <img src="{{ Storage::url($pkg->image) }}" alt="{{ $pkg->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-orange-100 via-amber-50 to-orange-50 flex items-center justify-center">
                                <svg class="w-16 h-16 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <a href="{{ route('customer.event.package', [$service->id, $pkg->id]) }}" 
                               class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
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
