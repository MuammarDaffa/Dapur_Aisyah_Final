@extends('layouts.app')
@section('title', 'Menu Kami')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Menu <span class="text-orange-500">Kami</span></h2>
            <p class="text-sm text-gray-500 mt-1">Daftar menu katering harian yang tersedia sesuai jadwal saat ini.</p>
        </div>
        @if($services->count() > 1)
        <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0">
            <a href="{{ route('customer.products') }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ !request('service') ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                Semua Layanan
            </a>
            @foreach($services as $srv)
            <a href="{{ route('customer.products', ['service' => $srv->id]) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ request('service') == $srv->id ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                {{ $srv->name }}
            </a>
            @endforeach
        </div>
        @endif
    </div>

    @if($items->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
        @foreach($items as $item)
            @php
                $product = $item->product;
                $canOrder = $item->canOrder() && $product->isAvailable();
            @endphp
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 flex flex-col {{ !$canOrder ? 'opacity-75 grayscale-[0.3]' : '' }}">
                <div class="relative h-44 bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center overflow-hidden">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-white/60 backdrop-blur-sm flex items-center justify-center text-orange-500 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    @endif

                    @if($product->is_best_seller)
                        <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">Best Seller</span>
                    @endif

                    @if(!$product->isAvailable())
                        <span class="absolute inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center text-white font-bold text-lg tracking-wider">HABIS</span>
                    @elseif(!$canOrder)
                        <span class="absolute inset-0 bg-black/40 backdrop-blur-[1px] flex items-center justify-center p-4 text-center">
                            <span class="bg-gray-900/90 text-white text-xs font-bold px-3.5 py-2 rounded-full shadow-md">Batas Pesan Berakhir</span>
                        </span>
                    @endif
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs text-orange-600 font-semibold uppercase tracking-wider">{{ $product->cateringService->name ?? '' }}</span>
                            <span class="inline-flex items-center gap-1 text-xs bg-orange-50 text-orange-700 px-2.5 py-1 rounded-full font-semibold border border-orange-100">
                                <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $item->formatted_date }}</span>
                            </span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base mb-1 leading-snug">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-500 mb-4 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                    </div>

                    <div class="pt-3 border-t border-gray-50 flex items-center justify-between gap-3">
                        <div>
                            <span class="text-xs text-gray-400 block">Harga</span>
                            <span class="text-base font-bold text-orange-600">{{ $product->formatted_price }}</span>
                        </div>
                        @if($canOrder)
                        <button type="button" onclick="openOrderModal({{ $product->id }}, '{{ $item->menu_date->format('Y-m-d') }}')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 transition-all shadow-sm hover:shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            <span>Pesan</span>
                        </button>
                        @else
                        <button type="button" disabled
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-400 text-xs font-semibold rounded-xl cursor-not-allowed">
                            <span>Ditutup</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    {{-- Empty State (Exact Match according to guidelines) --}}
    <div class="text-center py-16 px-4 bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl mx-auto my-8">
        <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Menu Belum Tersedia</h3>
        <p class="text-sm text-gray-500 max-w-md mx-auto leading-relaxed">Saat ini belum tersedia menu katering harian. Silakan cek kembali nanti.</p>
    </div>
    @endif
</div>

{{-- Order Modal --}}
<div id="orderModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col transform transition-all">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Tambah ke Keranjang</h3>
            </div>
            <button onclick="closeOrderModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1 p-6">
            <form id="orderForm" action="{{ route('customer.cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="modalProductId">
                <input type="hidden" name="menu_date" id="modalMenuDate">

                {{-- Product Info --}}
                <div class="flex items-center space-x-4 mb-6 bg-orange-50/50 border border-orange-100/60 rounded-xl p-4">
                    <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center text-orange-500 shadow-sm border border-orange-100 flex-shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <h4 id="modalProductName" class="font-bold text-gray-900 text-base"></h4>
                        <p id="modalProductPrice" class="text-orange-600 font-bold text-sm"></p>
                        <p id="modalMenuDateLabel" class="text-xs text-gray-500 mt-1 font-medium"></p>
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
                <div id="modalExtrasLoading" class="text-sm text-gray-500 py-2 hidden flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Memuat opsi tambahan...</span>
                </div>

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
                        <span class="text-sm text-gray-600 font-medium">Estimasi Total</span>
                        <span id="modalTotal" class="text-xl font-bold text-orange-600">Rp 0</span>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="p-6 border-t border-gray-100 flex-shrink-0">
            <button type="button" onclick="document.getElementById('orderForm').submit()"
                class="w-full px-6 py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Konfirmasi Pesanan</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Build products data from all menu items
    const productsData = {
        @foreach($items as $item)
        {{ $item->product->id }}: {
            id: {{ $item->product->id }},
            name: @json($item->product->name),
            price: {{ (float) $item->product->price }},
            service_id: {{ $item->product->catering_service_id }},
        },
        @endforeach
    };
    let currentProduct = null;
    let availableExtras = [];

    function openOrderModal(productId, menuDate) {
        currentProduct = productsData[productId];
        if (!currentProduct) return;

        document.getElementById('modalProductId').value = currentProduct.id;
        document.getElementById('modalMenuDate').value = menuDate;
        document.getElementById('modalProductName').textContent = currentProduct.name;
        document.getElementById('modalProductPrice').textContent = formatRupiah(currentProduct.price) + ' / porsi';
        document.getElementById('modalMenuDateLabel').textContent = 'Tanggal Kirim: ' + menuDate;
        document.getElementById('modalQty').value = 1;
        
        document.getElementById('modalExtrasList').innerHTML = '';
        document.getElementById('modalExtrasContainer').classList.add('hidden');
        document.getElementById('modalExtrasLoading').classList.remove('hidden');

        // Fetch extras for this product
        fetch(`/api/product/${currentProduct.id}/extras`)
            .then(res => res.json())
            .then(data => {
                availableExtras = data.filter(opt => opt.type === 'extra');
                document.getElementById('modalExtrasLoading').classList.add('hidden');
                
                if (availableExtras.length > 0) {
                    let html = '';
                    availableExtras.forEach(extra => {
                        html += `
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-xl hover:bg-orange-50 transition-colors">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input type="checkbox" name="extras[${extra.id}][id]" value="${extra.id}" data-price="${extra.price}" id="extra_cb_${extra.id}" onchange="toggleExtra(${extra.id})" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-400 extra-checkbox">
                                    <span class="text-sm font-medium text-gray-700">${extra.name}</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-orange-600">+${formatRupiah(extra.price)}</span>
                                    <div class="flex items-center gap-1 transition-opacity duration-200 opacity-0 pointer-events-none" id="extra_qty_container_${extra.id}">
                                        <button type="button" onclick="changeExtraQty(${extra.id}, -1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 transition-colors">−</button>
                                        <input type="text" inputmode="numeric" name="extras[${extra.id}][qty]" id="extra_qty_${extra.id}" value="0" oninput="manualExtraQty(${extra.id})" onchange="manualExtraQty(${extra.id})" class="w-10 text-center bg-transparent text-sm font-bold text-gray-900 focus:outline-none" disabled>
                                        <button type="button" onclick="changeExtraQty(${extra.id}, 1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 transition-colors">+</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    document.getElementById('modalExtrasList').innerHTML = html;
                    document.getElementById('modalExtrasContainer').classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error("Gagal memuat extras", err);
                document.getElementById('modalExtrasLoading').classList.add('hidden');
            });

        updateModalTotal();
        document.getElementById('orderModal').style.display = 'flex';
    }

    function closeOrderModal() {
        document.getElementById('orderModal').style.display = 'none';
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
        if (!cb) return;
        if (!cb.checked) return;

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
        if (!currentProduct) return;
        const qty = parseInt(document.getElementById('modalQty').value) || 1;
        let total = currentProduct.price * qty;

        // Add extras prices (Tidak dikali porsi menu)
        const extraCheckboxes = document.querySelectorAll('.extra-checkbox:checked');
        extraCheckboxes.forEach(cb => {
            const extraId = cb.value;
            const extraQty = parseInt(document.getElementById('extra_qty_' + extraId).value) || 0;
            total += parseFloat(cb.getAttribute('data-price')) * extraQty;
        });

        document.getElementById('modalTotal').textContent = formatRupiah(total);
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    // Close modal when clicking outside
    document.getElementById('orderModal').addEventListener('click', function(e) {
        if (e.target === this) closeOrderModal();
    });
</script>
@endpush
@endsection
