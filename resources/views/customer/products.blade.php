@extends('layouts.app')
@section('title', 'Menu Kami')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Menu <span class="text-orange-500">Kami</span></h2>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('customer.products') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Cari Menu</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama menu..." class="w-full px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Layanan</label>
                <select name="service" class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-orange-400">
                    <option value="">Semua Layanan</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Hari</label>
                <select name="day" class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-orange-400">
                    <option value="">Semua Hari</option>
                    @foreach(['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $day)
                        <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>{{ ucfirst($day) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">Filter</button>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            @php
                $isAvailable = $product->isAvailable();
                $isTodayOnly = is_array($product->available_days) && count($product->available_days) === 1 && in_array($currentDay, $product->available_days);
                $isPastCutoff = $isTodayOnly && $currentHour >= 10;
                $canOrder = $isAvailable && !$isPastCutoff;
            @endphp
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 {{ !$canOrder ? 'opacity-75 grayscale-[0.3]' : '' }}">
                <div class="relative h-44 bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-5xl">🍛</span>
                    @endif
                    @if($product->is_best_seller)
                        <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">🔥 Best Seller</span>
                    @endif
                    @if(!$isAvailable)
                        <span class="absolute inset-0 bg-black/40 flex items-center justify-center text-white font-bold text-lg">HABIS</span>
                    @endif
                </div>
                <div class="p-5">
                    <p class="text-xs text-orange-500 font-medium mb-1">{{ $product->cateringService->name ?? '' }}</p>
                    <h3 class="font-bold text-gray-900 mb-1">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $product->description }}</p>
                    @if($product->available_days)
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach($product->available_days as $day)
                                <span class="text-xs {{ $day === $currentDay ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-600' }} px-2 py-0.5 rounded-full">{{ ucfirst($day) }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    @if($isPastCutoff)
                        <p class="text-xs text-red-500 font-medium mb-3">Pemesanan ditutup (lewat jam 10:00)</p>
                    @endif

                    <div class="flex items-center justify-between mt-auto">
                        <span class="text-lg font-bold text-orange-600">{{ $product->formatted_price }}</span>
                        @if($canOrder)
                        <button type="button" onclick="openOrderModal({{ $product->id }})"
                            class="flex items-center space-x-1 px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-xl hover:bg-orange-600 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            <span>Keranjang</span>
                        </button>
                        @else
                        <button type="button" disabled
                            class="flex items-center space-x-1 px-4 py-2 bg-gray-300 text-gray-500 text-sm font-medium rounded-xl cursor-not-allowed">
                            <span>Tidak Tersedia</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <p class="text-5xl mb-3">🍽️</p>
                <p class="font-medium">Belum ada menu tersedia.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $products->withQueryString()->links() }}</div>
</div>

{{-- Order Modal (Task 3: Tambah Extra) --}}
<div id="orderModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col transform transition-all">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">🛒 Tambah ke Keranjang</h3>
            <button onclick="closeOrderModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1 p-6">
            <form id="orderForm" action="{{ route('customer.cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="modalProductId">

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
        <div class="p-6 border-t border-gray-100 flex-shrink-0">
            <button type="button" onclick="document.getElementById('orderForm').submit()"
                class="w-full px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all text-sm">
                Tambah ke Keranjang →
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    @php
        $productsJson = $products->getCollection()->keyBy('id')->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->price,
                'service_id' => $p->catering_service_id,
            ];
        });
    @endphp
    const productsData = @json($productsJson);
    let currentProduct = null;
    let availableExtras = [];

    function openOrderModal(productId) {
        currentProduct = productsData[productId];
        if (!currentProduct) return;

        document.getElementById('modalProductId').value = currentProduct.id;
        document.getElementById('modalProductName').textContent = currentProduct.name;
        document.getElementById('modalProductPrice').textContent = formatRupiah(currentProduct.price) + ' / porsi';
        document.getElementById('modalQty').value = 1;
        
        document.getElementById('modalExtrasList').innerHTML = '';
        document.getElementById('modalExtrasContainer').classList.add('hidden');
        document.getElementById('modalExtrasLoading').classList.remove('hidden');

        // Fetch extras for this service
        fetch(`/api/service/${currentProduct.service_id}/custom-options`)
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
            // Jika kosong/tidak valid saat mengetik, biarkan sementara tapi jangan update total ke NaN
            // Akan otomatis jadi 1 saat kehilangan fokus atau tambah/kurang
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
