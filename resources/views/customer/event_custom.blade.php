@extends('layouts.app')
@section('title', 'Custom Menu - ' . $service->name)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('customer.event.service', $service->id) }}" class="text-sm text-orange-500 hover:text-orange-600">← Kembali ke Layanan</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Custom Menu</h2>
        <p class="text-gray-500">Pilih menu secara bebas dengan minimal pemesanan 30 porsi.</p>
    </div>

    <form id="customForm" action="{{ route('customer.event.cart.store') }}" method="POST">
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
                <div class="space-y-3">
                    @foreach($menus as $idx => $menu)
                        <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-orange-50 transition-colors">
                            <label class="flex items-center gap-3 cursor-pointer flex-1">
                                <input type="checkbox" id="custom_menu_{{ $idx }}" data-id="{{ $menu->id }}" data-price="{{ $menu->price }}"
                                    class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-400 custom-menu-cb" onchange="toggleCustomMenu({{ $idx }})">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $menu->name }}</span>
                                    <p class="text-sm text-orange-600 font-semibold">Rp {{ number_format($menu->price, 0, ',', '.') }} / porsi</p>
                                </div>
                            </label>
                            <div class="flex items-center gap-2 opacity-0 pointer-events-none transition-opacity" id="custom_menu_qty_container_{{ $idx }}">
                                <button type="button" onclick="changeCustomQty({{ $idx }}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                                <input type="number" id="custom_menu_input_{{ $idx }}" value="0" min="0"
                                    class="w-16 text-center border rounded-lg py-1 font-semibold" onchange="recalcCustom()">
                                <button type="button" onclick="changeCustomQty({{ $idx }}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Porsi Indicator --}}
                <div class="mt-4 p-4 rounded-xl border border-yellow-200 bg-yellow-50" id="custom-portion-indicator">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold text-gray-700">Minimal: <span class="text-orange-600">30 porsi</span></span>
                        <span class="text-sm font-semibold text-gray-700">Total Dipilih: <span id="custom-total-portions" class="text-blue-600">0</span></span>
                    </div>
                    <p id="custom-portion-msg" class="text-sm font-medium mt-1 text-yellow-700">⚠️ Kurang 30 porsi lagi. Minimal 30 porsi.</p>
                </div>
            </div>

            {{-- Extra --}}
            @if($extras->isNotEmpty())
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tambahan (Extra) — Opsional</h3>
                    <div class="space-y-3">
                        @foreach($extras as $idx => $extra)
                            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-orange-50 transition-colors">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input type="checkbox" id="custom_extra_{{ $idx }}" data-id="{{ $extra->id }}" data-price="{{ $extra->price }}"
                                        class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-400 custom-extra-cb" onchange="toggleCustomExtra({{ $idx }})">
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $extra->name }}</span>
                                        <p class="text-sm text-orange-600 font-semibold">+Rp {{ number_format($extra->price, 0, ',', '.') }}</p>
                                    </div>
                                </label>
                                <div class="flex items-center gap-2 opacity-0 pointer-events-none transition-opacity" id="custom_extra_qty_container_{{ $idx }}">
                                    <button type="button" onclick="changeCustomExtraQty({{ $idx }}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                                    <input type="number" id="custom_extra_input_{{ $idx }}" value="0" min="0"
                                        class="w-16 text-center border rounded-lg py-1 font-semibold" onchange="recalcCustom()">
                                    <button type="button" onclick="changeCustomExtraQty({{ $idx }}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Penyajian --}}
            @if($servings->isNotEmpty())
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Cara Penyajian *</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($servings as $serving)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="serving_type_id" value="{{ $serving->id }}" class="peer sr-only custom-serving-radio" onchange="recalcCustom()">
                                <div class="p-4 text-center border-2 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-colors">
                                    <span class="font-medium text-gray-900">{{ $serving->name }}</span>
                                </div>
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
                    Masukkan ke Keranjang 🛒
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const CUSTOM_MIN_PORTIONS = 30;

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
        val = Math.max(0, val + delta);
        if (val <= 0) {
            val = 0;
            cb.checked = false;
            document.getElementById('custom_menu_qty_container_' + idx).classList.add('opacity-0', 'pointer-events-none');
        }
        input.value = val;
        recalcCustom();
    }

    function toggleCustomExtra(idx) {
        const cb = document.getElementById('custom_extra_' + idx);
        const qtyContainer = document.getElementById('custom_extra_qty_container_' + idx);
        const input = document.getElementById('custom_extra_input_' + idx);
        if (cb.checked) {
            qtyContainer.classList.remove('opacity-0', 'pointer-events-none');
            input.value = 1;
        } else {
            qtyContainer.classList.add('opacity-0', 'pointer-events-none');
            input.value = 0;
        }
        recalcCustom();
    }

    function changeCustomExtraQty(idx, delta) {
        const input = document.getElementById('custom_extra_input_' + idx);
        const cb = document.getElementById('custom_extra_' + idx);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        if (val <= 0) {
            val = 0;
            cb.checked = false;
            document.getElementById('custom_extra_qty_container_' + idx).classList.add('opacity-0', 'pointer-events-none');
        }
        input.value = val;
        recalcCustom();
    }

    function recalcCustom() {
        let totalPortions = 0;
        let subtotalMenu = 0;
        let subtotalExtra = 0;

        document.querySelectorAll('.custom-menu-cb:checked').forEach(cb => {
            const price = parseFloat(cb.dataset.price);
            const idx = cb.id.split('_').pop();
            const qty = parseInt(document.getElementById('custom_menu_input_' + idx).value) || 0;
            totalPortions += qty;
            subtotalMenu += price * qty;
        });

        document.querySelectorAll('.custom-extra-cb:checked').forEach(cb => {
            const price = parseFloat(cb.dataset.price);
            const idx = cb.id.split('_').pop();
            const qty = parseInt(document.getElementById('custom_extra_input_' + idx).value) || 0;
            subtotalExtra += price * qty;
        });

        document.getElementById('custom-total-portions').textContent = totalPortions;
        document.getElementById('custom-subtotal-menu').textContent = formatRupiah(subtotalMenu);
        document.getElementById('custom-subtotal-extra').textContent = formatRupiah(subtotalExtra);
        document.getElementById('custom-total-price').textContent = formatRupiah(subtotalMenu + subtotalExtra);

        const msg = document.getElementById('custom-portion-msg');
        const indicator = document.getElementById('custom-portion-indicator');
        const btn = document.getElementById('custom-submit-btn');
        const hasServing = document.querySelector('.custom-serving-radio:checked');

        if (totalPortions < CUSTOM_MIN_PORTIONS) {
            indicator.className = 'mt-4 p-4 rounded-xl border border-yellow-200 bg-yellow-50';
            msg.textContent = `⚠️ Kurang ${CUSTOM_MIN_PORTIONS - totalPortions} porsi lagi. Minimal ${CUSTOM_MIN_PORTIONS} porsi.`;
            msg.className = 'text-sm font-medium mt-1 text-yellow-700';
            btn.disabled = true;
        } else if (!hasServing && document.querySelector('.custom-serving-radio')) {
            indicator.className = 'mt-4 p-4 rounded-xl border border-blue-200 bg-blue-50';
            msg.textContent = `ℹ️ Pilih cara penyajian terlebih dahulu.`;
            msg.className = 'text-sm font-medium mt-1 text-blue-700';
            btn.disabled = true;
        } else {
            indicator.className = 'mt-4 p-4 rounded-xl border border-green-200 bg-green-50';
            msg.textContent = `✅ Siap dimasukkan ke keranjang!`;
            msg.className = 'text-sm font-medium mt-1 text-green-700';
            btn.disabled = false;
        }

        buildCustomFormFields();
    }

    function buildCustomFormFields() {
        document.querySelectorAll('.custom-hidden-item').forEach(el => el.remove());

        const form = document.getElementById('customForm');
        let formIdx = 0;

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
            const idx = cb.id.split('_').pop();
            const qty = parseInt(document.getElementById('custom_extra_input_' + idx).value) || 0;
            if (qty > 0) {
                appendHidden(form, `items[${formIdx}][custom_option_id]`, id);
                appendHidden(form, `items[${formIdx}][quantity]`, qty);
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
</script>
@endpush
@endsection
