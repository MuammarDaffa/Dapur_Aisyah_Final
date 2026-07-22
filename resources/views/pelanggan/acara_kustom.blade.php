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
        <a href="{{ route('pelanggan.acara.service', $service->id) }}" class="d-inline-d-flex align-items-center fs-6 text-primary hover:text-primary">
            <svg style="width: 16px; height: 16px;" class="me-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Layanan</span>
        </a>
        <h2 class="fs-3 fw-bold text-secondary mt-2">Custom Menu</h2>
        <p class="text-secondary">Pilih menu sesuka Anda sesuai kebutuhan acara.</p>
    </div>

    <div id="customForm" onkeydown="return event.key != 'Enter';">
        <input type="hidden" name="layanan_katering_id" value="{{ $service->id }}">

        @php
            $menus = $opsiKustom->where('type', 'menu')->values();
            $extras = $opsiKustom->where('type', 'extra')->values();
            $servings = $opsiKustom->where('type', 'tipe_penyajian')->values();
        @endphp

        <div class="space-y-6">
            {{-- Pilih Menu --}}
            <div class="card shadow-sm mb-4 p-4">
                <h3 class="fs-5 fw-bold text-secondary mb-4">Pilih Menu</h3>
                <div class="divide-y divide-gray-100 border-t border-b border border-secondary">
                    @foreach($menus as $idx => $menu)
                        <div class="py-3.5 px-2 hover:bg-primary text-white/50 rounded">
                            {{-- Baris 1: Checkbox + Nama Menu (kiri) & Kontrol Jumlah (kanan) --}}
                            <div class="d-flex align-items-center justify-content-between g-3">
                                <div class="d-flex align-items-center g-3 d-flex-1 min-w-0">
                                    <input type="checkbox" id="custom_menu_{{ $idx }}" data-id="{{ $menu->id }}" data-harga="{{ $menu->harga }}"
                                        class="w-4 h-4 rounded border border-secondary text-primary custom-menu-cb shrink-0 cursor-pointer" onchange="toggleCustomMenu({{ $idx }})">
                                    <label for="custom_menu_{{ $idx }}" class="fs-6 sm:text-base fw-bold text-secondary cursor-pointer truncate">{{ $menu->name }}</label>
                                </div>
                                <div class="d-flex align-items-center g-3.5 opacity-0 pointer-events-none transition-opacity flex-shrink-0" id="custom_menu_qty_container_{{ $idx }}">
                                    <button type="button" onclick="changeCustomQty({{ $idx }}, -1)" class="w-7 h-7 d-flex align-items-center justify-content-center bg-light hover:bg-light rounded fw-bold text-secondary fs-6 flex-shrink-0">−</button>
                                    <input type="number" id="custom_menu_input_{{ $idx }}" value="0" min="1"
                                        class="form-control w-14 text-center py-1 border border border-secondary rounded small sm:fs-6 fw-bold text-secondary bg-white flex-shrink-0 focus: focus:border border-primary -1 desktop-no-spinner custom-menu-qty-input" oninput="validateCustomInput({{ $idx }})" onchange="validateCustomInputBlur({{ $idx }})">
                                    <button type="button" onclick="changeCustomQty({{ $idx }}, 1)" class="w-7 h-7 d-flex align-items-center justify-content-center bg-light hover:bg-light rounded fw-bold text-secondary fs-6 flex-shrink-0 custom-menu-plus-btn">+</button>
                                </div>
                            </div>

                            {{-- Baris 2: Harga Menu --}}
                            <div class="ps-7 mt-1">
                                <span class="small sm:fs-6 fw-bold text-primary">Rp {{ number_format($menu->harga, 0, ',', '.') }} / porsi</span>
                            </div>

                            {{-- Baris 3 & Selanjutnya: Link Lihat Detail & Daftar Isi/Menu --}}
                            @if($menu->items && count($menu->items) > 0)
                            <div class="ps-7 mt-1.5">
                                <button type="button" onclick="toggleMenuDetail({{ $idx }}, event)" class="small fw-bold text-info hover:text-info focus: underline">Lihat Detail</button>
                            </div>
                            <div id="menu_detail_{{ $idx }}" class="d-none ps-7 mt-2">
                                <div class="py-2.5 px-3.5 bg-light/80 small text-secondary border-l-2 border border-primary rounded-r-lg">
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
                <div class="mt-4 p-4 rounded border border border-secondary bg-light d-flex align-items-center justify-content-between" id="custom-porsi-indicator">
                    <span class="text-base fw-bold text-secondary">Total Porsi: <span id="custom-total-portions">0</span> / {{ $service->maksimal_porsi }}</span>
                </div>
            </div>

            {{-- Extra --}}
            @if($extras->isNotEmpty())
                <div class="card shadow-sm mb-4 p-4">
                    <h3 class="fs-5 fw-bold text-secondary mb-4">Tambahan (Extra) — Opsional</h3>
                    <div class="divide-y divide-gray-100 border-t border-b border border-secondary">
                        @foreach($extras as $idx => $extra)
                            <div class="py-3 px-2 d-flex align-items-center justify-content-between g-3 hover:bg-primary text-white/50 rounded">
                                <label for="custom_extra_{{ $idx }}" class="d-flex align-items-center g-3 cursor-pointer d-flex-1 min-w-0">
                                    <input type="checkbox" id="custom_extra_{{ $idx }}" data-id="{{ $extra->id }}" data-harga="{{ $extra->harga }}"
                                        class="w-4 h-4 rounded border border-secondary text-primary custom-extra-cb shrink-0 cursor-pointer" onchange="recalcCustom()">
                                    <span class="fs-6 fw-medium text-secondary truncate">{{ $extra->name }}</span>
                                </label>
                                <span class="fs-6 fw-bold text-primary flex-shrink-0">+Rp {{ number_format($extra->harga, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Penyajian --}}
            @if($servings->isNotEmpty())
                <div class="card shadow-sm mb-4 p-4">
                    <h3 class="fs-5 fw-bold text-secondary mb-4">Cara Penyajian *</h3>
                    <div class="d-flex d-flex-column sm:d-flex-row sm:d-flex-wrap g-3 sm:g-3 pt-1">
                        @foreach($servings as $serving)
                            <label class="d-flex align-items-center g-3 py-2 px-3 rounded hover:bg-primary text-white/50 cursor-pointer border border border-secondary sm:border-transparent sm:hover:border border-secondary">
                                <input type="radio" name="serving_type_id" value="{{ $serving->id }}" class="w-4 h-4 text-primary border border-secondary custom-serving-radio cursor-pointer shrink-0" onchange="recalcCustom()">
                                <span class="fs-6 fw-medium text-secondary">{{ $serving->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Ringkasan & Tombol --}}
            <div class="card shadow-sm mb-4 p-4">
                <div class="p-4 bg-primary text-white rounded border border border-primary d-flex flex-column gap-2 mb-4">
                    <div class="d-flex justify-content-between fs-6">
                        <span class="text-secondary">Subtotal Menu:</span>
                        <span id="custom-subtotal-menu" class="fw-medium">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between fs-6">
                        <span class="text-secondary">Subtotal Extra:</span>
                        <span id="custom-subtotal-extra" class="fw-medium">Rp 0</span>
                    </div>
                    <div class="pt-2 border-t border border-primary d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-secondary">Total Harga</span>
                        <span id="custom-total-harga" class="fs-3 fw-bold text-primary">Rp 0</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const CUSTOM_MIN_PORTIONS = {{ $service->min_portion }};
    const CUSTOM_MAX_PORTIONS = {{ $service->maksimal_porsi }};

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
            const harga = parseFloat(cb.dataset.harga);
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
            subtotalMenu += harga * qty;
        });

        document.querySelectorAll('.custom-extra-cb:checked').forEach(cb => {
            const harga = parseFloat(cb.dataset.harga) || 0;
            subtotalExtra += harga * totalPortions;
        });

        document.getElementById('custom-total-portions').textContent = totalPortions;
        document.getElementById('custom-subtotal-menu').textContent = formatRupiah(subtotalMenu);
        document.getElementById('custom-subtotal-extra').textContent = formatRupiah(subtotalExtra);
        document.getElementById('custom-total-harga').textContent = formatRupiah(subtotalMenu + subtotalExtra);

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
                appendHidden(form, `items[${formIdx}][opsi_kustom_id]`, id);
                appendHidden(form, `items[${formIdx}][jumlah]`, qty);
                appendHidden(form, `items[${formIdx}][item_type]`, 'custom_menu');
                formIdx++;
            }
        });

        document.querySelectorAll('.custom-extra-cb:checked').forEach(cb => {
            const id = cb.dataset.id;
            if (menuPortions > 0) {
                appendHidden(form, `items[${formIdx}][opsi_kustom_id]`, id);
                appendHidden(form, `items[${formIdx}][jumlah]`, menuPortions);
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
