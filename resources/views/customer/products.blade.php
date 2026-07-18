@extends('layouts.app')
@section('title', 'Menu Kami')
@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="d-flex d-flex-column md:d-flex-row md:align-items-center justify-content-between g-3 mb-8">
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Menu <span class="text-primary">Kami</span></h2>
            <!-- <p class="fs-6 text-secondary mt-1">Daftar menu katering harian yang tersedia sesuai jadwal saat ini.</p> -->
        </div>
        @if($services->count() > 1)
        <div class="d-flex align-items-center g-3 overflow-x-auto pb-2 md:pb-0">
            <a href="{{ route('customer.products') }}"
               class="px-4 py-2 rounded small fw-bold {{ !request('service') ? 'bg-primary text-white text-white shadow-sm' : 'bg-white text-secondary border hover:bg-light' }}">
                Semua Layanan
            </a>
            @foreach($services as $srv)
            <a href="{{ route('customer.products', ['service' => $srv->id]) }}"
               class="px-4 py-2 rounded small fw-bold {{ request('service') == $srv->id ? 'bg-primary text-white text-white shadow-sm' : 'bg-white text-secondary border hover:bg-light' }}">
                {{ $srv->name }}
            </a>
            @endforeach
        </div>
        @endif
    </div>

    @if($items->isNotEmpty())
    <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-3 xl:row-cols-4 g-3 mb-12">
        @foreach($items as $item)
            @php
                $product = $item->product;
                $canOrder = $item->canOrder() && $item->isAvailable();
            @endphp
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:-translate-y-1 border border border-secondary d-flex d-flex-column {{ !$canOrder ? 'opacity-75 grayscale-[0.3]' : '' }}">
                <div class="position-relative h-44 d-flex align-items-center justify-content-center overflow-hidden">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-100 h-100 object-cover">
                    @else
                        <div style="width: 64px; height: 64px;" class="rounded-2xl bg-white/60 backdrop-blur-sm d-flex align-items-center justify-content-center text-primary shadow-sm">
                            <svg style="width: 32px; height: 32px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    @endif

                    @if(!$item->isAvailable())
                        <span class="position-absolute bg-dark/50 backdrop-blur-[2px] d-flex align-items-center justify-content-center text-white fw-bold fs-5 tracking-wider">HABIS</span>
                    @endif
                </div>

                <div class="p-5 d-flex-1 d-flex d-flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between g-3 mb-2">
                            <span class="small text-primary fw-bold uppercase tracking-wider">{{ $product->cateringService->name ?? '' }}</span>
                            <span class="d-inline-d-flex align-items-center g-3 small bg-primary text-white text-primary px-2.5 py-1 rounded-pill fw-bold border border border-primary">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $item->day_name }}</span>
                            </span>
                        </div>
                        <h3 class="fw-bold text-secondary text-base mb-1 leading-snug">{{ $product->name }}</h3>
                        <p class="small text-secondary mb-4 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                    </div>

                    <div class="pt-3 border-t border border-secondary d-flex align-items-center justify-content-between g-3">
                        <div>
                            <span class="small text-secondary d-block">Harga</span>
                            <span class="text-base fw-bold text-primary">{{ $product->formatted_price }}</span>
                        </div>
                        @if($canOrder)
                        <button type="button" onclick="openOrderModal({{ $product->id }}, '{{ $item->menu_date->format('Y-m-d') }}')"
                            class="d-inline-d-flex align-items-center g-3.5 px-4 py-2 bg-primary text-white text-white small fw-bold rounded hover:bg-primary text-white shadow-sm hover:shadow">
                            <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            <span>Pesan</span>
                        </button>
                        @else
                        <button type="button" disabled
                            class="d-inline-d-flex align-items-center g-3.5 px-3 py-2 bg-light text-secondary small fw-bold rounded cursor-not-allowed">
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
    <div class="text-center py-16 px-4 bg-white rounded-2xl border border border-secondary shadow-sm max-w-2xl mx-auto my-8">
        <!-- <div style="width: 64px; height: 64px;" class="bg-primary text-white text-primary rounded-pill d-flex align-items-center justify-content-center mx-auto mb-4">
            <svg style="width: 32px; height: 32px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div> -->
        <h3 class="fs-5 fw-bold text-secondary mb-2">Menu Belum Tersedia</h3>
        <p class="fs-6 text-secondary max-w-md mx-auto leading-relaxed">Saat ini belum tersedia menu harian. Silakan cek kembali nanti.</p>
    </div>
    @endif
</div>

{{-- Order Modal --}}
<div id="orderModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-lg mx-4 max-h-[90vh] d-flex d-flex-column">
        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between p-6 border-b border border-secondary d-flex-flex-shrink-0">
            <div class="d-flex align-items-center g-3">
                <div class="p-2 bg-primary text-white text-primary rounded">
                    <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-base fw-bold text-secondary">Tambah ke Keranjang</h3>
            </div>
            <button onclick="closeOrderModal()" class="p-1 text-secondary hover:text-secondary rounded hover:bg-light">
                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto d-flex-1 p-6">
            <form id="orderForm" action="{{ route('customer.cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="modalProductId">
                <input type="hidden" name="menu_date" id="modalMenuDate">

                {{-- Product Info --}}
                <div class="d-flex align-items-center justify-content-between g-3 mb-6 bg-primary text-white/50 border border border-primary/60 rounded p-4">
                    <h4 id="modalProductName" class="fw-bold text-secondary text-base"></h4>
                    <span id="modalProductPrice" class="text-primary fw-bold text-base d-flex-flex-shrink-0"></span>
                </div>

                {{-- Quantity --}}
                <div class="mb-6">
                    <label class="form-label fw-bold">Jumlah Porsi *</label>
                    <div class="d-flex align-items-center g-3">
                        <button type="button" onclick="changeQty(-1)" style="height: 40px;" class="w-10 rounded bg-light hover:bg-light d-flex align-items-center justify-content-center text-secondary fw-bold fs-5">−</button>
                        <input type="text" inputmode="none" readonly tabindex="-1" name="quantity" id="modalQty" value="1" class="form-control w-20 text-center px-3 py-2 rounded border border border-secondary fw-bold text-secondary focus: cursor-default select-none">
                        <button type="button" onclick="changeQty(1)" style="height: 40px;" class="w-10 rounded bg-light hover:bg-light d-flex align-items-center justify-content-center text-secondary fw-bold fs-5">+</button>
                    </div>
                </div>

                {{-- Extras Loading State --}}
                <div id="modalExtrasLoading" class="fs-6 text-secondary py-2 d-none d-flex align-items-center g-3">
                    <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Memuat opsi tambahan...</span>
                </div>

                {{-- Extras Container --}}
                <div id="modalExtrasContainer" class="mb-5 d-none">
                    <label class="form-label fw-bold">Extra Tambahan (Opsional)</label>
                    <div id="modalExtrasList" class="divide-y divide-gray-100">
                        <!-- Checkboxes will be injected here -->
                    </div>
                </div>

                {{-- Total Preview --}}
                <div class="rounded p-4 border border border-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-6 text-secondary fw-medium">Estimasi Total</span>
                        <span id="modalTotal" class="fs-4 fw-bold text-primary">Rp 0</span>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="p-6 border-t border border-secondary d-flex-flex-shrink-0">
            <button type="button" onclick="submitOrderAjax(event)"
                class="btn btn-primary">
                <!-- <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> -->
                <span>Masukan Keranjang</span>
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
        document.getElementById('modalProductPrice').textContent = formatRupiah(currentProduct.price);
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
                            <div class="d-flex align-items-center justify-content-between py-2.5 px-2 border-b border border-secondary last:border-b-0 hover:bg-primary text-white/50 rounded g-3">
                                <label class="d-flex align-items-center g-3.5 cursor-pointer d-flex-1 min-w-0">
                                    <input type="checkbox" name="extras[${extra.id}][id]" value="${extra.id}" data-price="${extra.price}" id="extra_cb_${extra.id}" onchange="toggleExtra(${extra.id})" style="width: 16px; height: 16px;" class="rounded border border-secondary text-primary extra-checkbox flex-shrink-0">
                                    <span class="fs-6 fw-medium text-secondary truncate">${extra.name}</span>
                                </label>
                                <div class="d-flex align-items-center g-3.5 flex-shrink-0">
                                    <span class="fs-6 fw-bold text-primary flex-shrink-0">+${formatRupiah(extra.price)}</span>
                                    <div id="extra_qty_container_${extra.id}" class="d-none align-items-center g-3">
                                        <button type="button" onclick="changeExtraQty(${extra.id}, -1)" class="w-7 h-7 d-flex align-items-center justify-content-center bg-light hover:bg-light rounded fw-bold text-secondary fs-6 flex-shrink-0">−</button>
                                        <input type="text" inputmode="none" readonly tabindex="-1" name="extras[${extra.id}][qty]" id="extra_qty_${extra.id}" value="0" class="form-control w-12 text-center py-1 rounded border border border-secondary small fw-bold text-secondary focus: cursor-default select-none bg-light flex-shrink-0" disabled>
                                        <button type="button" onclick="changeExtraQty(${extra.id}, 1)" class="w-7 h-7 d-flex align-items-center justify-content-center bg-light hover:bg-light rounded fw-bold text-secondary fs-6 flex-shrink-0">+</button>
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
        
        if (cb && cb.checked) {
            if (qtyContainer) {
                qtyContainer.classList.remove('hidden');
                qtyContainer.classList.add('flex');
            }
            if (qtyInput) {
                qtyInput.value = 1;
                qtyInput.disabled = false;
            }
        } else {
            if (qtyContainer) {
                qtyContainer.classList.add('hidden');
                qtyContainer.classList.remove('flex');
            }
            if (qtyInput) {
                qtyInput.value = 0;
                qtyInput.disabled = true;
            }
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

    function submitOrderAjax(e) {
        if (e && e.preventDefault) e.preventDefault();
        const form = document.getElementById('orderForm');
        if (!form) return;

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const submitBtn = e.currentTarget || document.querySelector('button[onclick*="submitOrderAjax"]');
        if (submitBtn) submitBtn.disabled = true;

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (submitBtn) submitBtn.disabled = false;
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
                return;
            }
            if (data.success) {
                closeOrderModal();
                if (typeof window.updateCartBadges === 'function' && typeof data.cart_count !== 'undefined') {
                    window.updateCartBadges(data.cart_count);
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Produk dan opsi berhasil ditambahkan ke keranjang!',
                        showConfirmButton: true,
                        confirmButtonText: 'Oke',
                        confirmButtonColor: '#f97316'
                    });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat menambahkan ke keranjang.',
                        confirmButtonColor: '#f97316'
                    });
                } else {
                    alert(data.message || 'Terjadi kesalahan.');
                }
            }
        })
        .catch(err => {
            if (submitBtn) submitBtn.disabled = false;
            form.submit();
        });
    }
</script>
@endpush
@endsection
