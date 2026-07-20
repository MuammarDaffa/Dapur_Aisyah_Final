@extends('layouts.owner')

@section('title', 'Produk Best Seller')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Produk Best Seller</h2>
            <p class="fs-6 text-secondary mt-1">Top produk berdasarkan jumlah pembelian</p>
        </div>
        <div class="text-white px-4 py-2 rounded fw-bold shadow">
            ⭐ {{ $bestSellers->total() }} Produk
        </div>
    </div>

    {{-- Produk Grid --}}
    <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-4 g-3">
        @forelse($bestSellers as $index => $produk)
            <div class="bg-white rounded shadow-md overflow-hidden border border border-secondary hover:-translate-y-1">
                {{-- Image --}}
                <div class="position-relative h-44 overflow-hidden">
                    @if($produk->image)
                        <img src="{{ asset('storage/' . $produk->image) }}" alt="{{ $produk->name }}"
                             class="w-100 h-100 object-cover">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                            <span class="text-5xl">🍽️</span>
                        </div>
                    @endif

                    {{-- Rank Badge --}}
                    <div style="height: 36px;" class="position-absolute w-9 rounded-pill d-flex align-items-center justify-content-center text-white fw-bold fs-6 shadow">
                        #{{ $bestSellers->firstItem() + $index }}
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4 d-flex flex-column gap-2">
                    <h3 class="fw-bold text-secondary truncate">{{ $produk->name }}</h3>

                    @if($produk->layananKatering)
                        <p class="small text-secondary">{{ $produk->layananKatering->name }}</p>
                    @endif

                    <div class="d-flex align-items-center justify-content-between pt-2 border-t border border-secondary">
                        <span class="fw-bold text-success">
                            Rp {{ number_format($produk->harga, 0, ',', '.') }}
                        </span>
                        <div class="d-flex align-items-center g-3 bg-teal-50 text-teal-700 px-2.5 py-1 rounded-pill small fw-bold">
                            <span>📦</span>
                            <span>{{ $produk->order_items_count }} terjual</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <span class="text-5xl d-block mb-3">📦</span>
                <p class="text-secondary fw-medium fs-5">Belum ada data penjualan</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($bestSellers->hasPages())
        <div class="d-flex justify-content-center">
            {{ $bestSellers->links() }}
        </div>
    @endif
</div>
@endsection
