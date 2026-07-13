@extends('layouts.app')
@section('title', 'Detail Paket - ' . $package->name)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('customer.event.service', $service->id) }}" class="inline-flex items-center text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors">
            <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Pilih Layanan</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        {{-- Cover Image --}}
        @if($package->image)
            <div class="w-full h-64 sm:h-80 bg-gray-100 relative overflow-hidden">
                <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-6 sm:p-10">
            {{-- Section: Informasi Paket --}}
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="inline-block px-3 py-1 bg-orange-100 text-orange-700 font-bold text-xs rounded-full mb-2">Paket Katering Tetap</span>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $package->name }}</h1>
                        <p class="text-sm font-medium text-gray-500 mt-1">Layanan: {{ $service->name }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs text-gray-400">Harga Paket</p>
                        <p class="text-2xl sm:text-3xl font-bold text-orange-600">{{ $package->formatted_price }}</p>
                        <p class="text-xs font-semibold text-gray-600 mt-1">{{ $package->total_portions }} Porsi</p>
                    </div>
                </div>

                @if($package->description)
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base mt-4">{{ $package->description }}</p>
                @endif
            </div>

            @php
                $menus = $package->getIncludedMenus();
                $serving = $package->getIncludedServingTypes()->first();
                $benefits = $package->benefits ?? [];
            @endphp

            <hr class="border-gray-100 my-8">

            {{-- Section: Daftar Menu Dalam Paket --}}
            <div>
                <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-6">Daftar Menu Dalam Paket</h3>
                <div class="space-y-6">
                    @forelse($menus as $menu)
                        <div>
                            <h4 class="font-bold text-gray-900 text-base sm:text-lg">{{ $menu->name }}</h4>
                            @if(!empty($menu->items))
                                @if(is_array($menu->items) || $menu->items instanceof \Traversable)
                                    <ul class="mt-2 space-y-1.5 text-gray-600 text-sm sm:text-base">
                                        @foreach($menu->items as $item)
                                            @if(!empty($item))
                                                <li class="flex items-start gap-2">
                                                    <span class="text-orange-500 font-bold mt-0.5">•</span>
                                                    <span>{{ is_string($item) ? $item : json_encode($item) }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm sm:text-base text-gray-600 mt-1">{{ $menu->items }}</p>
                                @endif
                            @endif
                        </div>
                        @if(!$loop->last)
                            <hr class="border-gray-100 my-6">
                        @endif
                    @empty
                        <p class="text-sm text-gray-400">Belum ada menu yang dikonfigurasi untuk paket ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Section: Penyajian --}}
            @if($serving)
                <hr class="border-gray-100 my-8">
                <div>
                    <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-3">Penyajian</h3>
                    <p class="font-semibold text-gray-800 text-base sm:text-lg">{{ $serving->name }}</p>
                    <p class="text-sm text-gray-500 mt-1">Selesai disajikan lengkap sesuai standar hidangan katering kami.</p>
                </div>
            @endif

            {{-- Section: Benefit --}}
            @if(!empty($benefits))
                <hr class="border-gray-100 my-8">
                <div>
                    <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-4">Benefit Tambahan</h3>
                    <ul class="space-y-2 text-gray-700 text-sm sm:text-base">
                        @foreach($benefits as $b)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>{{ is_string($b) ? $b : json_encode($b) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <hr class="border-gray-100 my-8">

            {{-- Section: Catatan & Tombol Masukkan Keranjang --}}
            <form action="{{ route('customer.event.cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="catering_service_id" value="{{ $service->id }}">
                <input type="hidden" name="catering_package_id" value="{{ $package->id }}">
                @if($serving)
                    <input type="hidden" name="serving_type_id" value="{{ $serving->id }}">
                @endif

                <div class="mb-8">
                    <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-3">Catatan Pesanan</h3>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-orange-500 focus:border-orange-500 text-sm sm:text-base" placeholder="Contoh: Tolong jangan terlalu pedas, pengiriman tepat waktu...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-6 border-t border-gray-100">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Harga Paket ({{ $package->total_portions }} Porsi)</p>
                        <p class="text-2xl sm:text-3xl font-bold text-orange-600 mt-0.5">{{ $package->formatted_price }}</p>
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-sm hover:shadow-md transition-all text-base">
                        Masukkan ke Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
