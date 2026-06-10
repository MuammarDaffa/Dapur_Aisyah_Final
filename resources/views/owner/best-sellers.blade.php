@extends('layouts.owner')

@section('title', 'Produk Best Seller')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Produk Best Seller</h2>
            <p class="text-sm text-gray-500 mt-1">Top produk berdasarkan jumlah pembelian</p>
        </div>
        <div class="bg-gradient-to-r from-amber-400 to-orange-500 text-white px-4 py-2 rounded-xl font-semibold shadow">
            ⭐ {{ $bestSellers->total() }} Produk
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($bestSellers as $index => $product)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                {{-- Image --}}
                <div class="relative h-44 overflow-hidden">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-50 flex items-center justify-center">
                            <span class="text-5xl">🍽️</span>
                        </div>
                    @endif

                    {{-- Rank Badge --}}
                    <div class="absolute top-3 left-3 w-9 h-9 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">
                        #{{ $bestSellers->firstItem() + $index }}
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4 space-y-2">
                    <h3 class="font-semibold text-gray-800 truncate">{{ $product->name }}</h3>

                    @if($product->cateringService)
                        <p class="text-xs text-gray-400">{{ $product->cateringService->name }}</p>
                    @endif

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="font-bold text-green-600">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                        <div class="flex items-center gap-1 bg-teal-50 text-teal-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                            <span>📦</span>
                            <span>{{ $product->order_items_count }} terjual</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <span class="text-5xl block mb-3">📦</span>
                <p class="text-gray-400 font-medium text-lg">Belum ada data penjualan</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($bestSellers->hasPages())
        <div class="flex justify-center">
            {{ $bestSellers->links() }}
        </div>
    @endif
</div>
@endsection
