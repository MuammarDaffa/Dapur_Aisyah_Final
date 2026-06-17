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
                        $basePrice = $cart->product ? (float) $cart->product->price : ($cart->customOption ? (float) $cart->customOption->price : 0);
                        $extrasList = collect();
                        $extrasPrice = 0;
                        if (!empty($cart->extras)) {
                            $extraIds = array_column($cart->extras, 'id');
                            $options = \App\Models\CustomOption::whereIn('id', $extraIds)->get()->keyBy('id');
                            
                            foreach ($cart->extras as $extraData) {
                                if ($opt = $options->get($extraData['id'])) {
                                    $exPrice = (float) $opt->price * $extraData['qty'];
                                    $extrasPrice += $exPrice;
                                    $extrasList->push((object)[
                                        'name' => $opt->name,
                                        'qty' => $extraData['qty'],
                                        'price' => $exPrice,
                                    ]);
                                }
                            }
                        }
                    @endphp
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 cart-item" data-base-price="{{ $basePrice }}" data-extras-price="{{ $extrasPrice }}">
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
                                    @if($extrasList->isNotEmpty())
                                        <div class="text-sm text-gray-600 mt-1 mb-1">
                                            <span class="font-medium">Extra:</span>
                                            <ul class="list-disc pl-4 mt-0.5 space-y-0.5 text-xs">
                                                @foreach($extrasList as $ex)
                                                    <li>{{ $ex->name }} {{ $ex->qty }}x (+Rp {{ number_format($ex->price, 0, ',', '.') }})</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <p class="text-sm text-orange-500">{{ $cart->product->cateringService->name ?? '' }}</p>
                                    <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($basePrice, 0, ',', '.') }} / porsi</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-3 ml-4">
                                <p class="text-lg font-bold text-gray-900 item-subtotal">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</p>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-500">{{ $cart->quantity }} porsi</span>
                                    <button type="button" onclick="openEditModal({{ $cart->id }})" class="px-4 py-2 bg-orange-100 text-orange-600 text-xs font-bold rounded-lg hover:bg-orange-200 transition-colors">Ubah Pesanan</button>
                                </div>
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

{{-- Data Keranjang JSON --}}
@php
    $allCartsJson = $carts->keyBy('id')->map(function($c) {
        $basePrice = $c->product ? (float) $c->product->price : ($c->customOption ? (float) $c->customOption->price : 0);
        $name = $c->product->name ?? ($c->customOption->name ?? 'Item');
        $serviceId = $c->product->catering_service_id ?? ($c->customOption->catering_service_id ?? null);
        return [
            'id' => $c->id,
            'name' => $name,
            'price' => $basePrice,
            'quantity' => $c->quantity,
            'service_id' => $serviceId,
            'extras' => $c->extras ?? [],
            'update_url' => route('customer.cart.update', $c->id),
        ];
    });
@endphp

{{-- Edit Order Modal --}}
<div id="editOrderModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col transform transition-all">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">📝 Ubah Pesanan</h3>
            <button type="button" onclick="closeEditModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1 p-6">
            <form id="editOrderForm" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="cart_id" id="modalCartId">

                {{-- Product Info --}}
                <div class="flex items-center space-x-4 mb-6 bg-orange-50/50 rounded-xl p-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center text-3xl flex-shrink-0">🍛</div>
                    <div>
                        <h4 id="modalProductName" class="font-bold text-gray-900"></h4>
                        <p id="modalProductPrice" class="text-orange-600 font-semibold text-sm"></p>
                    </div>
                </div>

                {{-- Quantity --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Porsi *</label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changeQty(-1)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg transition-colors">−</button>
                        <input type="number" name="quantity" id="modalQty" value="1" min="1" class="w-20 text-center px-3 py-2 rounded-xl border border-gray-200 font-semibold text-gray-900 focus:border-orange-400 focus:ring-2 focus:ring-orange-100" onchange="updateModalTotal()">
                        <button type="button" onclick="changeQty(1)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg transition-colors">+</button>
                    </div>
                </div>

                {{-- Extras Loading State --}}
                <div id="modalExtrasLoading" class="text-sm text-gray-500 py-2 hidden">Memuat opsi tambahan...</div>

                {{-- Extras Container --}}
                <div id="modalExtrasContainer" class="mb-6 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Extra Tambahan (Opsional)</label>
                    <div id="modalExtrasList" class="space-y-2">
                        <!-- Checkboxes will be injected here -->
                    </div>
                </div>

                {{-- Total Preview --}}
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-4 border border-orange-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Estimasi Total</span>
                        <span id="modalTotal" class="text-xl font-bold text-orange-600">Rp 0</span>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="p-6 border-t border-gray-100 flex-shrink-0 flex gap-3">
            <button type="button" onclick="closeEditModal()" class="flex-1 px-6 py-3.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors text-sm">
                Batal
            </button>
            <button type="button" onclick="document.getElementById('editOrderForm').submit()"
                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all text-sm">
                Ubah
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const cartsData = @json($allCartsJson);
    let currentCart = null;
    let availableExtras = [];

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    function openEditModal(cartId) {
        currentCart = cartsData[cartId];
        if (!currentCart) return;

        document.getElementById('editOrderForm').action = currentCart.update_url;
        document.getElementById('modalCartId').value = currentCart.id;
        document.getElementById('modalProductName').textContent = currentCart.name;
        document.getElementById('modalProductPrice').textContent = formatRupiah(currentCart.price) + ' / porsi';
        document.getElementById('modalQty').value = currentCart.quantity;
        
        document.getElementById('modalExtrasList').innerHTML = '';
        document.getElementById('modalExtrasContainer').classList.add('hidden');
        document.getElementById('modalExtrasLoading').classList.remove('hidden');

        fetch(`/api/service/${currentCart.service_id}/custom-options`)
            .then(res => res.json())
            .then(data => {
                availableExtras = data.filter(opt => opt.type === 'extra');
                document.getElementById('modalExtrasLoading').classList.add('hidden');
                
                if (availableExtras.length > 0) {
                    let html = '';
                    availableExtras.forEach(extra => {
                        // Cek apakah extra ini sudah ada di keranjang currentCart.extras
                        let extraInCart = currentCart.extras.find(e => parseInt(e.id) === parseInt(extra.id));
                        let isChecked = extraInCart ? 'checked' : '';
                        let extraQty = extraInCart ? extraInCart.qty : 0;
                        let disabledState = extraInCart ? '' : 'disabled';
                        let containerClasses = extraInCart ? '' : 'opacity-0 pointer-events-none';

                        html += `
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-xl hover:bg-orange-50 transition-colors">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input type="checkbox" name="extras[${extra.id}][id]" value="${extra.id}" data-price="${extra.price}" id="extra_cb_${extra.id}" onchange="toggleExtra(${extra.id})" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-400 extra-checkbox" ${isChecked}>
                                    <span class="text-sm font-medium text-gray-700">${extra.name}</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-orange-600">+${formatRupiah(extra.price)}</span>
                                    <div class="flex items-center gap-1 transition-opacity duration-200 ${containerClasses}" id="extra_qty_container_${extra.id}">
                                        <button type="button" onclick="changeExtraQty(${extra.id}, -1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 transition-colors">−</button>
                                        <input type="text" inputmode="numeric" name="extras[${extra.id}][qty]" id="extra_qty_${extra.id}" value="${extraQty}" oninput="manualExtraQty(${extra.id})" onchange="manualExtraQty(${extra.id})" class="w-10 text-center bg-transparent text-sm font-bold text-gray-900 focus:outline-none" ${disabledState}>
                                        <button type="button" onclick="changeExtraQty(${extra.id}, 1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 transition-colors">+</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    document.getElementById('modalExtrasList').innerHTML = html;
                    document.getElementById('modalExtrasContainer').classList.remove('hidden');
                }
                updateModalTotal();
            })
            .catch(err => {
                console.error("Gagal memuat extras", err);
                document.getElementById('modalExtrasLoading').classList.add('hidden');
            });

        updateModalTotal();
        document.getElementById('editOrderModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editOrderModal').style.display = 'none';
    }

    function changeQty(delta) {
        const input = document.getElementById('modalQty');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        input.value = val;
        updateModalTotal();
    }

    function toggleExtra(id) {
        const cb = document.getElementById('extra_cb_' + id);
        const qtyContainer = document.getElementById('extra_qty_container_' + id);
        const qtyInput = document.getElementById('extra_qty_' + id);
        
        if (cb.checked) {
            qtyContainer.classList.remove('opacity-0', 'pointer-events-none');
            qtyInput.value = 1;
            qtyInput.disabled = false;
        } else {
            qtyContainer.classList.add('opacity-0', 'pointer-events-none');
            qtyInput.value = 0;
            qtyInput.disabled = true;
        }
        updateModalTotal();
    }

    function changeExtraQty(id, delta) {
        const cb = document.getElementById('extra_cb_' + id);
        if (!cb || !cb.checked) return;

        const input = document.getElementById('extra_qty_' + id);
        if (!input) return;

        let val = parseInt(input.value) || 0;
        val += delta;
        
        if (val < 1) {
            cb.checked = false;
            toggleExtra(id);
        } else {
            input.value = val;
            updateModalTotal();
        }
    }

    function manualExtraQty(id) {
        const cb = document.getElementById('extra_cb_' + id);
        if (!cb || !cb.checked) return;
        
        const input = document.getElementById('extra_qty_' + id);
        if (!input) return;

        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) {
            if (input.value === "") return;
            val = 1;
            input.value = 1;
        }
        updateModalTotal();
    }

    function updateModalTotal() {
        if (!currentCart) return;
        const qty = parseInt(document.getElementById('modalQty').value) || 1;
        let total = currentCart.price * qty;

        const extraCheckboxes = document.querySelectorAll('.extra-checkbox:checked');
        extraCheckboxes.forEach(cb => {
            const extraId = cb.value;
            const extraQty = parseInt(document.getElementById('extra_qty_' + extraId).value) || 0;
            total += parseFloat(cb.getAttribute('data-price')) * extraQty;
        });

        document.getElementById('modalTotal').textContent = formatRupiah(total);
    }

    // Close modal when clicking outside
    document.getElementById('editOrderModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endpush
@endsection
