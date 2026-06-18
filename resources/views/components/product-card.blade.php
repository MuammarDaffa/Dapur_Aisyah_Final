@props(['product'])

<div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:-translate-y-1">
    {{-- Product Image --}}
    <div class="relative overflow-hidden h-48">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-50 flex items-center justify-center">
                <span class="text-5xl">🍽️</span>
            </div>
        @endif

        {{-- Best Seller Badge --}}
        @if($product->is_best_seller)
            <div class="absolute top-3 left-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center gap-1">
                <span>⭐</span> Best Seller
            </div>
        @endif

        {{-- Service Badge --}}
        @if($product->cateringService)
            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-medium px-2.5 py-1 rounded-full shadow">
                {{ $product->cateringService->name }}
            </div>
        @endif
    </div>

    {{-- Card Content --}}
    <div class="p-4 space-y-3">
        <h3 class="font-semibold text-gray-800 text-lg leading-tight line-clamp-1 group-hover:text-orange-600 transition-colors">
            {{ $product->name }}
        </h3>

        @if($product->description)
            <p class="text-sm text-gray-500 line-clamp-2">{{ $product->description }}</p>
        @endif

        {{-- Available Days --}}
        @if($product->available_days)
            <div class="flex flex-wrap gap-1">
                <span class="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded-full font-medium">
                    {{ ucfirst($product->available_days) }}
                </span>
            </div>
        @endif

        {{-- Price & Action --}}
        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <div>
                <span class="text-xs text-gray-400">Mulai dari</span>
                <p class="text-lg font-bold text-orange-600">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>
            </div>

            <form action="{{ route('customer.cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white p-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95"
                        title="Tambah ke Keranjang">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
