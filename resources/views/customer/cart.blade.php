@extends('layouts.app')
@section('title', 'Keranjang Belanja')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">🛒 Keranjang <span class="text-orange-500">Belanja</span></h2>

    @if($carts->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
            <p class="text-5xl mb-4">🛒</p>
            <p class="text-gray-500 font-medium mb-4">Keranjang belanja Anda kosong</p>
            <a href="{{ route('customer.products') }}" class="px-6 py-3 bg-orange-500 text-white font-medium rounded-full hover:bg-orange-600 transition-colors">Lihat Menu →</a>
        </div>
    @else
        <div class="space-y-4 mb-6">
            @foreach($groupedCarts as $groupKey => $groupItems)
                @php
                    $isEventGroup = $groupItems->first()->cart_group_id !== null;
                    $packageItem = $isEventGroup ? $groupItems->firstWhere('item_type', 'package') : null;
                @endphp

                @if($isEventGroup)
                    {{-- Event Group Display --}}
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-orange-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🎉</span>
                                <div>
                                    <h4 class="font-bold text-gray-900">Pesanan Event</h4>
                                    @if($packageItem && $packageItem->cateringPackage)
                                        <p class="text-sm text-orange-600 font-medium">Paket: {{ $packageItem->cateringPackage->name }}</p>
                                    @else
                                        <p class="text-sm text-blue-600 font-medium">Full Custom</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-lg font-bold text-gray-900">
                                    Rp {{ number_format($groupItems->sum(fn($c) => $c->subtotal), 0, ',', '.') }}
                                </span>
                                <form action="{{ route('customer.cart.destroy', $groupItems->first()) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" onclick="return confirm('Hapus seluruh pesanan event ini?')">
                                        Hapus Event
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Isi Event Items --}}
                        <div class="divide-y divide-gray-100">
                            @foreach($groupItems->where('item_type', '!=', 'package') as $cart)
                            <div class="py-2 flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    @if($cart->item_type === 'package_item')
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Paket</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">Tambahan</span>
                                    @endif
                                    <span class="font-medium text-gray-900">{{ $cart->customOption->name ?? 'Item' }}</span>
                                    <span class="text-gray-500">× {{ $cart->quantity }} porsi</span>
                                </div>
                                <span class="font-medium {{ $cart->item_type === 'package_item' ? 'text-green-600' : 'text-orange-600' }}">
                                    @if($cart->item_type === 'package_item')
                                        Termasuk
                                    @else
                                        Rp {{ number_format($cart->subtotal, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Regular Product Item (Harian) --}}
                    @foreach($groupItems as $cart)
                    @php
                        $unitPrice = $cart->product ? (float) $cart->product->price : ($cart->customOption ? (float) $cart->customOption->price : 0);
                    @endphp
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 cart-item" data-unit-price="{{ $unitPrice }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center text-3xl flex-shrink-0">
                                    @if($cart->product && $cart->product->image)
                                        <img src="{{ Storage::url($cart->product->image) }}" alt="{{ $cart->product->name }}" class="w-full h-full object-cover rounded-xl">
                                    @else
                                        🍛
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900">{{ $cart->product->name ?? ($cart->customOption->name ?? 'Item') }}</h4>
                                    <p class="text-sm text-orange-500">{{ $cart->product->cateringService->name ?? '' }}</p>
                                    <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($unitPrice, 0, ',', '.') }} / porsi</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-3 ml-4">
                                <p class="text-lg font-bold text-gray-900 item-subtotal">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</p>
                                <form action="{{ route('customer.cart.update', $cart) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf @method('PUT')
                                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                        <button type="button" onclick="adjustQty(this, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 text-sm font-bold transition-colors">−</button>
                                        <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" class="qty-input w-12 h-8 text-center border-x border-gray-200 text-sm font-semibold focus:outline-none focus:ring-0 focus:border-gray-200" onchange="recalcItem(this)">
                                        <button type="button" onclick="adjustQty(this, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 text-sm font-bold transition-colors">+</button>
                                    </div>
                                    <button type="submit" class="px-3 py-1.5 bg-orange-100 text-orange-600 text-xs font-semibold rounded-lg hover:bg-orange-200 transition-colors">Update</button>
                                </form>
                                <form action="{{ route('customer.cart.destroy', $cart) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="flex items-center gap-1 px-3 py-1.5 text-xs text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            @endforeach
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600">Subtotal (<span id="item-count">{{ $carts->count() }}</span> item)</span>
                <span class="text-xl font-bold text-gray-900" id="cart-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('customer.checkout') }}" class="block w-full text-center px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                Lanjut ke Checkout →
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    function adjustQty(button, delta) {
        const form = button.closest('form');
        const input = form.querySelector('.qty-input');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        input.value = val;
        recalcItem(input);
    }

    function recalcItem(input) {
        const cartItem = input.closest('.cart-item');
        if (!cartItem) return;
        const unitPrice = parseFloat(cartItem.dataset.unitPrice);
        const qty = parseInt(input.value) || 1;
        const subtotal = unitPrice * qty;
        cartItem.querySelector('.item-subtotal').textContent = formatRupiah(subtotal);
        recalcTotal();
    }

    function recalcTotal() {
        let total = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            const unitPrice = parseFloat(item.dataset.unitPrice);
            const qty = parseInt(item.querySelector('.qty-input').value) || 1;
            total += unitPrice * qty;
        });
        document.getElementById('cart-total').textContent = formatRupiah(total);
    }
</script>
@endpush
@endsection
