@extends('layouts.app')
@section('title', 'Custom Menu - ' . $service->name)
@section('content')
<style>
@media (min-width: 1024px) {
    /* Chrome, Safari, Edge, Opera */
    input[type=number].desktop-no-spinner::-webkit-outer-spin-button,
    input[type=number].desktop-no-spinner::-webkit-inner-spin-button,
    input[type=number].custom-menu-qty-input::-webkit-outer-spin-button,
    input[type=number].custom-menu-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    /* Firefox */
    input[type=number].desktop-no-spinner,
    input[type=number].custom-menu-qty-input {
        -moz-appearance: textfield;
    }
}
</style>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('customer.event.service', $service->id) }}" class="inline-flex items-center text-sm text-orange-500 hover:text-orange-600">
            <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Layanan</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Custom Menu</h2>
        <p class="text-gray-500">Pilih menu sesuka Anda sesuai kebutuhan acara.</p>
    </div>

    <form id="customForm" action="{{ route('customer.event.cart.store') }}" method="POST" onkeydown="return event.key != 'Enter';">
        @csrf
        <input type="hidden" name="catering_service_id" value="{{ $service->id }}">

        @php
            $menus = $customOptions->where('type', 'menu')->values();
            $extras = $customOptions->where('type', 'extra')->values();
            $servings = $customOptions->where('type', 'serving_type')->values();
        @endphp

        <div class="space-y-6">
            {{-- Pilih Menu --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Pilih Menu</h3>
                <div class="divide-y divide-gray-100 border-t border-b border-gray-100">
                    @foreach($menus as $idx => $menu)
                        <div class="py-3.5 px-2 hover:bg-orange-50/50 rounded-lg transition-colors">
                            {{-- Baris 1: Checkbox + Nama Menu (kiri) & Kontrol Jumlah (kanan) --}}
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <input type="checkbox" id="custom_menu_{{ $idx }}" data-id="{{ $menu->id }}" data-price="{{ $menu->price }}"
                                        class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 custom-menu-cb shrink-0 cursor-pointer" onchange="toggleCustomMenu({{ $idx }})">
                                    <label for="custom_menu_{{ $idx }}" class="text-sm sm:text-base font-semibold text-gray-900 cursor-pointer truncate">{{ $menu->name }}</label>
                                </div>
                                <div class="flex items-center gap-1.5 opacity-0 pointer-events-none transition-opacity shrink-0" id="custom_menu_qty_container_{{ $idx }}">
                                    <button type="button" onclick="changeCustomQty({{ $idx }}, -1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 text-sm shrink-0 transition-colors">−</button>
                                    <input type="number" id="custom_menu_input_{{ $idx }}" value="0" min="1"
                                        class="w-14 text-center py-1 border border-gray-200 rounded-lg text-xs sm:text-sm font-bold text-gray-900 bg-white shrink-0 focus:outline-none focus:border-orange-400 focus:ring-1 focus:ring-orange-400 desktop-no-spinner custom-menu-qty-input" oninput="validateCustomInput({{ $idx }})" onchange="validateCustomInputBlur({{ $idx }})">
                                    <button type="button" onclick="changeCustomQty({{ $idx }}, 1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 text-sm shrink-0 custom-menu-plus-btn transition-colors">+</button>
                                </div>
                            </div>

                            {{-- Baris 2: Harga Menu --}}
                            <div class="pl-7 mt-1">
                                <span class="text-xs sm:text-sm font-semibold text-orange-600">Rp {{ number_format($menu->price, 0, ',', '.') }} / porsi</span>
                            </div>

                            {{-- Baris 3 & Selanjutnya: Link Lihat Detail & Daftar Isi/Menu --}}
                            @if($menu->items && count($menu->items) > 0)
                            <div class="pl-7 mt-1.5">
                                <button type="button" onclick="toggleMenuDetail({{ $idx }}, event)" class="text-xs font-semibold text-blue-600 hover:text-blue-800 focus:outline-none underline">Lihat Detail</button>
                            </div>
                            <div id="menu_detail_{{ $idx }}" class="hidden pl-7 mt-2">
                                <div class="py-2.5 px-3.5 bg-gray-50/80 text-xs text-gray-700 border-l-2 border-orange-300 rounded-r-lg">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($menu->items as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Porsi Indicator --}}
                <div class="mt-4 p-4 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-between" id="custom-portion-indicator">
                    <span class="text-base font-bold text-gray-900">Total Porsi: <span id="custom-total-portions">0</span> / {{ $service->max_portion }}</span>
                </div>
            </div>

            {{-- Extra --}}
            @if($extras->isNotEmpty())
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tambahan (Extra) — Opsional</h3>
                    <div class="divide-y divide-gray-100 border-t border-b border-gray-100">
                        @foreach($extras as $idx => $extra)
                            <div class="py-3 px-2 flex items-center justify-between gap-3 hover:bg-orange-50/50 rounded-lg transition-colors">
                                <label for="custom_extra_{{ $idx }}" class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                                    <input type="checkbox" id="custom_extra_{{ $idx }}" data-id="{{ $extra->id }}" data-price="{{ $extra->price }}"
                                        class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 custom-extra-cb shrink-0 cursor-pointer" onchange="recalcCustom()">
                                    <span class="text-sm font-medium text-gray-800 truncate">{{ $extra->name }}</span>
                                </label>
                                <span class="text-sm font-semibold text-orange-600 shrink-0">+Rp {{ number_format($extra->price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Penyajian --}}
            @if($servings->isNotEmpty())
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Cara Penyajian *</h3>
                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:gap-6 pt-1">
                        @foreach($servings as $serving)
                            <label class="flex items-center gap-3 py-2 px-3 rounded-lg hover:bg-orange-50/50 cursor-pointer transition-colors border border-gray-100 sm:border-transparent sm:hover:border-gray-100">
                                <input type="radio" name="serving_type_id" value="{{ $serving->id }}" class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-400 custom-serving-radio cursor-pointer shrink-0" onchange="recalcCustom()">
                                <span class="text-sm font-medium text-gray-800">{{ $serving->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Ringkasan & Tombol --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="p-4 bg-orange-50 rounded-xl border border-orange-100 space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal Menu:</span>
                        <span id="custom-subtotal-menu" class="font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal Extra:</span>
                        <span id="custom-subtotal-extra" class="font-medium">Rp 0</span>
                    </div>
                    <div class="pt-2 border-t border-orange-200 flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total Harga</span>
                        <span id="custom-total-price" class="text-2xl font-bold text-orange-600">Rp 0</span>
                    </div>
                </div>
                <button type="submit" id="custom-submit-btn" disabled
                    class="w-full px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-lg rounded-xl hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="inline-flex items-center justify-center gap-2"><span>Masukkan ke Keranjang</span><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg></span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const CUSTOM_MIN_PORTIONS = {{ $service->min_portion }};
    const CUSTOM_MAX_PORTIONS = {{ $service->max_portion }};

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    function toggleCustomMenu(idx) {
        const cb = document.getElementById('custom_menu_' + idx);
        const qtyContainer = document.getElementById('custom_menu_qty_container_' + idx);
        const input = document.getElementById('custom_menu_input_' + idx);
        if (cb.checked) {
            qtyContainer.classList.remove('opacity-0', 'pointer-events-none');
            input.value = 1;
        } else {
            qtyContainer.classList.add('opacity-0', 'pointer-events-none');
            input.value = 0;
        }
        recalcCustom();
    }

    function changeCustomQty(idx, delta) {
        const input = document.getElementById('custom_menu_input_' + idx);
        const cb = document.getElementById('custom_menu_' + idx);
        let val = parseInt(input.value) || 0;
        
        if (delta > 0) {
            let currentTotal = 0;
            document.querySelectorAll('.custom-menu-cb:checked').forEach(itemCb => {
                const itemIdx = itemCb.id.split('_').pop();
                currentTotal += parseInt(document.getElementById('custom_menu_input_' + itemIdx).value) || 0;
            });
            if (currentTotal + delta > CUSTOM_MAX_PORTIONS) {
                return;
            }
        }

        val = Math.max(0, val + delta);
        if (val <= 0) {
            val = 0;
            cb.checked = false;
            document.getElementById('custom_menu_qty_container_' + idx).classList.add('opacity-0', 'pointer-events-none');
        }
        input.value = val;
        recalcCustom();
    }

    function toggleMenuDetail(idx, event) {
        event.preventDefault();
        event.stopPropagation();
        const detailPanel = document.getElementById('menu_detail_' + idx);
        if (detailPanel) {
            detailPanel.classList.toggle('hidden');
        }
    }

    function validateCustomInput(idx) {
        const inputEl = document.getElementById('custom_menu_input_' + idx);
        if (!inputEl) return;
        inputEl.value = inputEl.value.replace(/[^0-9]/g, '');
        if (inputEl.value !== '') {
            let val = parseInt(inputEl.value, 10);
            if (isNaN(val) || val < 1) {
                val = 1;
                inputEl.value = '1';
            }
        }
        recalcCustom();
    }

    function validateCustomInputBlur(idx) {
        const inputEl = document.getElementById('custom_menu_input_' + idx);
        if (!inputEl) return;
        inputEl.value = inputEl.value.replace(/[^0-9]/g, '');
        let val = parseInt(inputEl.value, 10);
        if (isNaN(val) || val < 1) {
            inputEl.value = '1';
        }
        recalcCustom();
    }

    function recalcCustom() {
        let totalPortions = 0;
        let subtotalMenu = 0;
        let subtotalExtra = 0;

        document.querySelectorAll('.custom-menu-cb:checked').forEach(cb => {
            const price = parseFloat(cb.dataset.price);
            const idx = cb.id.split('_').pop();
            const inputEl = document.getElementById('custom_menu_input_' + idx);
            let qty = parseInt(inputEl.value) || 0;
            if (qty < 1 && inputEl.value !== '') {
                qty = 1;
                inputEl.value = '1';
            }
            
            if (totalPortions + qty > CUSTOM_MAX_PORTIONS) {
                qty = Math.max(1, CUSTOM_MAX_PORTIONS - totalPortions);
                inputEl.value = qty;
            }
            
            totalPortions += qty;
            subtotalMenu += price * qty;
        });

        document.querySelectorAll('.custom-extra-cb:checked').forEach(cb => {
            const price = parseFloat(cb.dataset.price) || 0;
            subtotalExtra += price * totalPortions;
        });

        document.getElementById('custom-total-portions').textContent = totalPortions;
        document.getElementById('custom-subtotal-menu').textContent = formatRupiah(subtotalMenu);
        document.getElementById('custom-subtotal-extra').textContent = formatRupiah(subtotalExtra);
        document.getElementById('custom-total-price').textContent = formatRupiah(subtotalMenu + subtotalExtra);

        const isMaxReached = totalPortions >= CUSTOM_MAX_PORTIONS;
        document.querySelectorAll('.custom-menu-plus-btn').forEach(btn => {
            btn.disabled = isMaxReached;
            btn.classList.toggle('opacity-50', isMaxReached);
            btn.classList.toggle('cursor-not-allowed', isMaxReached);
        });

        const btn = document.getElementById('custom-submit-btn');
        const hasServing = document.querySelector('.custom-serving-radio:checked');

        btn.disabled = (totalPortions === 0 || totalPortions > CUSTOM_MAX_PORTIONS || (!hasServing && document.querySelector('.custom-serving-radio')));

        buildCustomFormFields(totalPortions);
    }

    function buildCustomFormFields(totalPortions = 0) {
        document.querySelectorAll('.custom-hidden-item').forEach(el => el.remove());

        const form = document.getElementById('customForm');
        let formIdx = 0;
        let menuPortions = totalPortions;
        if (!menuPortions) {
            document.querySelectorAll('.custom-menu-cb:checked').forEach(cb => {
                const idx = cb.id.split('_').pop();
                menuPortions += parseInt(document.getElementById('custom_menu_input_' + idx).value) || 0;
            });
        }

        document.querySelectorAll('.custom-menu-cb:checked').forEach(cb => {
            const id = cb.dataset.id;
            const idx = cb.id.split('_').pop();
            const qty = parseInt(document.getElementById('custom_menu_input_' + idx).value) || 0;
            if (qty > 0) {
                appendHidden(form, `items[${formIdx}][custom_option_id]`, id);
                appendHidden(form, `items[${formIdx}][quantity]`, qty);
                appendHidden(form, `items[${formIdx}][item_type]`, 'custom_menu');
                formIdx++;
            }
        });

        document.querySelectorAll('.custom-extra-cb:checked').forEach(cb => {
            const id = cb.dataset.id;
            if (menuPortions > 0) {
                appendHidden(form, `items[${formIdx}][custom_option_id]`, id);
                appendHidden(form, `items[${formIdx}][quantity]`, menuPortions);
                appendHidden(form, `items[${formIdx}][item_type]`, 'addition');
                formIdx++;
            }
        });
    }

    function appendHidden(form, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        input.className = 'custom-hidden-item';
        form.appendChild(input);
    }

    document.addEventListener('DOMContentLoaded', () => {
        recalcCustom();
    });
</script>
@endpush
@push('styles')
<style>
@media (min-width: 1024px) {
    /* Chrome, Safari, Edge, Opera */
    input[type=number].desktop-no-spinner::-webkit-outer-spin-button,
    input[type=number].desktop-no-spinner::-webkit-inner-spin-button,
    input[type=number].custom-menu-qty-input::-webkit-outer-spin-button,
    input[type=number].custom-menu-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    /* Firefox */
    input[type=number].desktop-no-spinner,
    input[type=number].custom-menu-qty-input {
        -moz-appearance: textfield;
    }
}
</style>
@endpush
@endsection
