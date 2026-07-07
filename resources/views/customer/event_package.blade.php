@extends('layouts.app')
@section('title', 'Detail Paket - ' . $package->name)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('customer.event.service', $service->id) }}" class="text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors">← Kembali ke Pilih Layanan</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        {{-- Cover Image --}}
        @if($package->image)
            <div class="w-full h-64 sm:h-80 bg-gray-100 relative overflow-hidden">
                <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-6 mb-6">
                <div>
                    <span class="inline-block px-3 py-1 bg-orange-100 text-orange-700 font-bold text-xs rounded-full mb-2">Fixed Package (Paket Tetap)</span>
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
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-2">Deskripsi Paket</h3>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">{{ $package->description }}</p>
                </div>
            @endif

            @php
                $menus = $package->getIncludedMenus();
                $serving = $package->getIncludedServingTypes()->first();
                $benefits = $package->benefits ?? [];
            @endphp

            {{-- Daftar Menu --}}
            <div class="mb-8">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3">Daftar Menu Termasuk dalam Paket</h3>
                <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-100 space-y-4">
                    @forelse($menus as $menu)
                        <div class="flex items-start justify-between gap-4 p-3 bg-white rounded-xl border border-gray-100 shadow-2xs">
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-sm sm:text-base">{{ $menu->name }}</p>
                                @if($menu->items)
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $menu->items }}</p>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="inline-block px-2.5 py-1 bg-green-50 text-green-700 font-semibold text-xs rounded-lg border border-green-200/50">
                                    Termasuk ({{ $package->total_portions }} Porsi)
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada menu yang dikonfigurasi untuk paket ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Penyajian & Benefit --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                {{-- Penyajian --}}
                @if($serving)
                    <div class="bg-blue-50/50 rounded-xl p-5 border border-blue-100">
                        <h4 class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-2">Penyajian</h4>
                        <p class="font-bold text-gray-900 text-base">{{ $serving->name }}</p>
                        <p class="text-xs text-blue-600 mt-1">Selesai disajikan sesuai standar hidangan katering kami.</p>
                    </div>
                @endif

                {{-- Benefit --}}
                @if(!empty($benefits))
                    <div class="bg-amber-50/50 rounded-xl p-5 border border-amber-100">
                        <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-2">Benefit Tambahan</h4>
                        <ul class="space-y-1.5 text-sm text-gray-700">
                            @foreach($benefits as $b)
                                <li class="flex items-center gap-2">
                                    <span class="text-green-600 font-bold">✓</span>
                                    <span class="font-medium">{{ $b }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Form Tambah ke Keranjang --}}
            <form action="{{ route('customer.event.cart.store') }}" method="POST" class="border-t border-gray-100 pt-6">
                @csrf
                <input type="hidden" name="catering_service_id" value="{{ $service->id }}">
                <input type="hidden" name="catering_package_id" value="{{ $package->id }}">
                @if($serving)
                    <input type="hidden" name="serving_type_id" value="{{ $serving->id }}">
                @endif

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-800 mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-orange-500 focus:border-orange-500 text-sm" placeholder="Contoh: Tolong jangan terlalu pedas, pengiriman tepat waktu...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-orange-50/50 p-6 rounded-2xl border border-orange-100">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Total Harga Paket ({{ $package->total_portions }} Porsi)</p>
                        <p class="text-2xl font-bold text-orange-600 mt-0.5">{{ $package->formatted_price }}</p>
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-sm hover:shadow-md transition-all">
                        Masukkan ke Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
