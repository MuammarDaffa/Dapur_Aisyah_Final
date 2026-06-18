@extends('layouts.app')
@section('title', 'Konfigurasi Event - ' . $service->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('landing') }}" class="text-sm text-orange-500 hover:text-orange-600">← Kembali</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">🎉 {{ $service->name }}</h2>
        <p class="text-gray-500">{{ $service->description }}</p>
    </div>

    {{-- Pilih Mode: Paket atau Custom --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Pilih Jenis Pemesanan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($service->hasFeature('packages') && $packages->isNotEmpty())
            <button type="button" onclick="setMode('package')" id="btn-mode-package"
                class="p-5 rounded-xl border-2 border-gray-200 text-left hover:border-orange-400 transition-all focus:outline-none">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-3xl">📦</span>
                    <h4 class="font-bold text-gray-900 text-lg">Paket</h4>
                </div>
                <p class="text-sm text-gray-500">Pilih paket yang tersedia dan bagi porsi ke menu.</p>
            </button>
            @endif
            @if($service->hasFeature('full_custom'))
            <button type="button" onclick="setMode('custom')" id="btn-mode-custom"
                class="p-5 rounded-xl border-2 border-gray-200 text-left hover:border-orange-400 transition-all focus:outline-none">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-3xl">🍽️</span>
                    <h4 class="font-bold text-gray-900 text-lg">Custom Menu</h4>
                </div>
                <p class="text-sm text-gray-500">Pilih menu secara bebas dengan jumlah porsi masing-masing.</p>
            </button>
            @endif
        </div>
    </div>

    {{-- =============================== --}}
    {{-- MODE PAKET --}}
    {{-- =============================== --}}
    <div id="section-package" style="display: none;">
        <form id="packageForm" action="{{ route('customer.event.cart.store') }}" method="POST">
            @csrf
            <input type="hidden" name="catering_service_id" value="{{ $service->id }}">

            {{-- Pilih Paket --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">1. Pilih Paket</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="packageList">
                    @foreach($packages as $pkg)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="catering_package_id" value="{{ $pkg->id }}" class="peer sr-only" onchange="selectPackage({{ $pkg->id }})">
                        <div class="p-5 bg-white rounded-xl border-2 border-gray-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all shadow-sm">
                            <h4 class="font-bold text-gray-900">{{ $pkg->name }}</h4>
                            @if($pkg->description)<p class="text-sm text-gray-500 mt-1">{{ $pkg->description }}</p>@endif
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-lg font-bold text-orange-600">{{ $pkg->formatted_price }}</span>
                                <span class="text-sm text-gray-500">{{ $pkg->total_portions }} Porsi</span>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Konfigurasi Porsi Paket --}}
            <div id="pkg-config" style="display:none;" class="space-y-6">
                {{-- Menu Paket: bagi porsi --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">2. Bagi Porsi ke Menu</h3>

                    {{-- Porsi Indicator --}}
                    <div class="mb-4 p-4 rounded-xl border" id="pkg-portion-indicator">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Target Porsi: <span id="pkg-target" class="text-orange-600">0</span></span>
                            <span class="text-sm font-semibold text-gray-700">Total Dipilih: <span id="pkg-selected" class="text-blue-600">0</span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div id="pkg-progress-bar" class="h-3 rounded-full transition-all duration-300 bg-orange-500" style="width:0%"></div>
                        </div>
                        <p id="pkg-portion-msg" class="text-sm mt-2 font-medium"></p>
                    </div>

                    <div id="pkg-menu-list" class="space-y-3">
                        {{-- Diisi oleh JS saat paket dipilih --}}
                    </div>
                </div>

                {{-- Free Items --}}
                <div id="pkg-free-section" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100" style="display:none;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">🎁 Free (Termasuk Paket)</h3>
                    <div id="pkg-free-list" class="space-y-2"></div>
                </div>

                {{-- Penyajian Paket --}}
                <div id="pkg-serving-section" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100" style="display:none;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">🍲 Penyajian</h3>
                    <div id="pkg-serving-list" class="grid grid-cols-1 sm:grid-cols-3 gap-3"></div>
                </div>

                {{-- Ringkasan & Tombol --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="p-4 bg-orange-50 rounded-xl border border-orange-100 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-900">Total Harga Paket</span>
                            <span id="pkg-total-price" class="text-2xl font-bold text-orange-600">Rp 0</span>
                        </div>
                    </div>
                    <button type="submit" id="pkg-submit-btn" disabled
                        class="w-full px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-lg rounded-xl hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        Masukkan ke Keranjang 🛒
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- =============================== --}}
    {{-- MODE CUSTOM --}}
    {{-- =============================== --}}
    <div id="section-custom" style="display: none;">
        <form id="customForm" action="{{ route('customer.event.cart.store') }}" method="POST">
            @csrf
            <input type="hidden" name="catering_service_id" value="{{ $service->id }}">

            {{-- Pilih Menu --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">1. Pilih Menu</h3>
                <p class="text-sm text-gray-500 mb-4">Minimal total 30 porsi. Pilih menu dan atur jumlah porsi.</p>

                <div class="space-y-3" id="custom-menu-list">
                    @foreach($customOptions->where('type', 'menu') as $menu)
                    <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-orange-50 transition-colors">
                        <div class="flex items-center gap-3 flex-1">
                            <input type="checkbox" id="custom_menu_{{ $menu->id }}" data-id="{{ $menu->id }}" data-price="{{ $menu->price }}" data-name="{{ $menu->name }}"
                                class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-400 custom-menu-cb" onchange="toggleCustomMenu({{ $menu->id }})">
                            <div>
                                <label for="custom_menu_{{ $menu->id }}" class="font-medium text-gray-900 cursor-pointer">{{ $menu->name }}</label>
                                <p class="text-sm text-orange-600 font-semibold">Rp {{ number_format($menu->price, 0, ',', '.') }} / porsi</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 pointer-events-none transition-opacity" id="custom_menu_qty_{{ $menu->id }}">
                            <button type="button" onclick="changeCustomQty({{ $menu->id }}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                            <input type="number" id="custom_menu_input_{{ $menu->id }}" value="0" min="0"
                                class="w-16 text-center border rounded-lg py-1 font-semibold" onchange="recalcCustom()">
                            <button type="button" onclick="changeCustomQty({{ $menu->id }}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Porsi Indicator --}}
                <div class="mt-4 p-4 rounded-xl border" id="custom-portion-indicator">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold text-gray-700">Minimal: <span class="text-orange-600">30 porsi</span></span>
                        <span class="text-sm font-semibold text-gray-700">Total Dipilih: <span id="custom-total-portions" class="text-blue-600">0</span></span>
                    </div>
                    <p id="custom-portion-msg" class="text-sm font-medium mt-1"></p>
                </div>
            </div>

            {{-- Extra --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">2. Tambahan (Extra) — Opsional</h3>
                <div class="space-y-3" id="custom-extras-list">
                    @foreach($customOptions->where('type', 'extra') as $extra)
                    <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-orange-50 transition-colors">
                        <div class="flex items-center gap-3 flex-1">
                            <input type="checkbox" id="custom_extra_{{ $extra->id }}" data-id="{{ $extra->id }}" data-price="{{ $extra->price }}" data-name="{{ $extra->name }}"
                                class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-400 custom-extra-cb" onchange="toggleCustomExtra({{ $extra->id }})">
                            <div>
                                <label for="custom_extra_{{ $extra->id }}" class="font-medium text-gray-900 cursor-pointer">{{ $extra->name }}</label>
                                <p class="text-sm text-orange-600 font-semibold">+Rp {{ number_format($extra->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 pointer-events-none transition-opacity" id="custom_extra_qty_{{ $extra->id }}">
                            <button type="button" onclick="changeCustomExtraQty({{ $extra->id }}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                            <input type="number" id="custom_extra_input_{{ $extra->id }}" value="0" min="0"
                                class="w-16 text-center border rounded-lg py-1 font-semibold" onchange="recalcCustom()">
                            <button type="button" onclick="changeCustomExtraQty({{ $extra->id }}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Penyajian --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">3. Cara Penyajian *</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($customOptions->where('type', 'serving_type') as $serving)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="serving_type_id" value="{{ $serving->id }}" class="peer sr-only custom-serving-radio" onchange="recalcCustom()">
                        <div class="p-4 text-center border-2 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-colors">
                            <span class="font-medium text-gray-900">{{ $serving->name }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

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
        </form>
    </div>

</div>

@push('scripts')
<script>
    // =======================
    // DATA
    // =======================
    const packagesData = @json($packages->keyBy('id'));
    let currentMode = null;
    let selectedPackageId = null;

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    // =======================
    // MODE SWITCHING
    // =======================
    function setMode(mode) {
        currentMode = mode;
        document.getElementById('section-package').style.display = mode === 'package' ? 'block' : 'none';
        document.getElementById('section-custom').style.display = mode === 'custom' ? 'block' : 'none';

        const btnPkg = document.getElementById('btn-mode-package');
        const btnCustom = document.getElementById('btn-mode-custom');
        if (btnPkg) {
            btnPkg.classList.toggle('border-orange-500', mode === 'package');
            btnPkg.classList.toggle('bg-orange-50', mode === 'package');
            btnPkg.classList.toggle('border-gray-200', mode !== 'package');
        }
        if (btnCustom) {
            btnCustom.classList.toggle('border-orange-500', mode === 'custom');
            btnCustom.classList.toggle('bg-orange-50', mode === 'custom');
            btnCustom.classList.toggle('border-gray-200', mode !== 'custom');
        }
    }

    // =======================
    // PAKET MODE
    // =======================
    function selectPackage(pkgId) {
        selectedPackageId = pkgId;
        const pkg = packagesData[pkgId];
        if (!pkg) return;

        document.getElementById('pkg-config').style.display = 'block';
        document.getElementById('pkg-target').textContent = pkg.total_portions;
        document.getElementById('pkg-total-price').textContent = formatRupiah(pkg.price);

        // Render menu items from package's custom_options
        const menus = (pkg.custom_options || []).filter(opt => opt.type === 'menu');
        const extras = (pkg.custom_options || []).filter(opt => opt.type === 'extra');
        const servings = (pkg.custom_options || []).filter(opt => opt.type === 'serving_type');

        // Menu porsi
        let menuHtml = '';
        menus.forEach((menu, idx) => {
            menuHtml += `
            <div class="flex items-center justify-between p-4 border rounded-xl bg-white">
                <div>
                    <p class="font-medium text-gray-900">${menu.name}</p>
                    <p class="text-xs text-green-600 font-medium">Termasuk dalam paket</p>
                    <input type="hidden" name="items[${idx}][custom_option_id]" value="${menu.id}">
                    <input type="hidden" name="items[${idx}][item_type]" value="package_item">
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="changePkgQty(${idx}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                    <input type="number" name="items[${idx}][quantity]" id="pkg_qty_${idx}" value="0" min="0" max="${pkg.total_portions}"
                        class="w-16 text-center border rounded-lg py-1 font-semibold pkg-qty-input" onchange="recalcPkgPortions()" data-idx="${idx}">
                    <button type="button" onclick="changePkgQty(${idx}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                </div>
            </div>`;
        });
        document.getElementById('pkg-menu-list').innerHTML = menuHtml;

        // Free items (extras termasuk paket)
        if (extras.length > 0) {
            document.getElementById('pkg-free-section').style.display = 'block';
            let freeHtml = '';
            extras.forEach(ex => {
                freeHtml += `
                <div class="flex items-center gap-2 p-3 bg-green-50 rounded-lg border border-green-100">
                    <span class="text-green-600 font-bold">✓</span>
                    <span class="text-gray-900 font-medium">${ex.name}</span>
                    <span class="text-green-600 text-sm font-medium ml-auto">FREE</span>
                </div>`;
            });
            document.getElementById('pkg-free-list').innerHTML = freeHtml;
        } else {
            document.getElementById('pkg-free-section').style.display = 'none';
        }

        // Penyajian paket
        if (servings.length > 0) {
            document.getElementById('pkg-serving-section').style.display = 'block';
            let servingHtml = '';
            servings.forEach(s => {
                servingHtml += `
                <label class="relative cursor-pointer">
                    <input type="radio" name="serving_type_id" value="${s.id}" class="peer sr-only" checked>
                    <div class="p-4 text-center border-2 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-colors">
                        <span class="font-medium text-gray-900">${s.name}</span>
                    </div>
                </label>`;
            });
            document.getElementById('pkg-serving-list').innerHTML = servingHtml;
        } else {
            document.getElementById('pkg-serving-section').style.display = 'none';
        }

        recalcPkgPortions();
    }

    function changePkgQty(idx, delta) {
        const input = document.getElementById('pkg_qty_' + idx);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        input.value = val;
        recalcPkgPortions();
    }

    function recalcPkgPortions() {
        if (!selectedPackageId) return;
        const pkg = packagesData[selectedPackageId];
        const target = pkg.total_portions;

        let total = 0;
        document.querySelectorAll('.pkg-qty-input').forEach(input => {
            total += parseInt(input.value) || 0;
        });

        document.getElementById('pkg-selected').textContent = total;

        const pct = Math.min(100, (total / target) * 100);
        const bar = document.getElementById('pkg-progress-bar');
        bar.style.width = pct + '%';

        const indicator = document.getElementById('pkg-portion-indicator');
        const msg = document.getElementById('pkg-portion-msg');
        const btn = document.getElementById('pkg-submit-btn');

        if (total < target) {
            bar.className = 'h-3 rounded-full transition-all duration-300 bg-yellow-500';
            indicator.className = 'mb-4 p-4 rounded-xl border border-yellow-200 bg-yellow-50';
            msg.textContent = `⚠️ Kurang ${target - total} porsi lagi.`;
            msg.className = 'text-sm mt-2 font-medium text-yellow-700';
            btn.disabled = true;
        } else if (total > target) {
            bar.className = 'h-3 rounded-full transition-all duration-300 bg-red-500';
            indicator.className = 'mb-4 p-4 rounded-xl border border-red-200 bg-red-50';
            msg.textContent = `❌ Kelebihan ${total - target} porsi. Kurangi porsi.`;
            msg.className = 'text-sm mt-2 font-medium text-red-700';
            btn.disabled = true;
        } else {
            bar.className = 'h-3 rounded-full transition-all duration-300 bg-green-500';
            indicator.className = 'mb-4 p-4 rounded-xl border border-green-200 bg-green-50';
            msg.textContent = `✅ Porsi sudah tepat! Siap dimasukkan ke keranjang.`;
            msg.className = 'text-sm mt-2 font-medium text-green-700';
            btn.disabled = false;
        }
    }

    // =======================
    // CUSTOM MODE
    // =======================
    const CUSTOM_MIN_PORTIONS = 30;

    function toggleCustomMenu(id) {
        const cb = document.getElementById('custom_menu_' + id);
        const qtyContainer = document.getElementById('custom_menu_qty_' + id);
        const input = document.getElementById('custom_menu_input_' + id);
        if (cb.checked) {
            qtyContainer.classList.remove('opacity-0', 'pointer-events-none');
            input.value = 1;
        } else {
            qtyContainer.classList.add('opacity-0', 'pointer-events-none');
            input.value = 0;
        }
        recalcCustom();
    }

    function changeCustomQty(id, delta) {
        const input = document.getElementById('custom_menu_input_' + id);
        const cb = document.getElementById('custom_menu_' + id);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        if (val <= 0) {
            val = 0;
            cb.checked = false;
            document.getElementById('custom_menu_qty_' + id).classList.add('opacity-0', 'pointer-events-none');
        }
        input.value = val;
        recalcCustom();
    }

    function toggleCustomExtra(id) {
        const cb = document.getElementById('custom_extra_' + id);
        const qtyContainer = document.getElementById('custom_extra_qty_' + id);
        const input = document.getElementById('custom_extra_input_' + id);
        if (cb.checked) {
            qtyContainer.classList.remove('opacity-0', 'pointer-events-none');
            input.value = 1;
        } else {
            qtyContainer.classList.add('opacity-0', 'pointer-events-none');
            input.value = 0;
        }
        recalcCustom();
    }

    function changeCustomExtraQty(id, delta) {
        const input = document.getElementById('custom_extra_input_' + id);
        const cb = document.getElementById('custom_extra_' + id);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        if (val <= 0) {
            val = 0;
            cb.checked = false;
            document.getElementById('custom_extra_qty_' + id).classList.add('opacity-0', 'pointer-events-none');
        }
        input.value = val;
        recalcCustom();
    }

    function recalcCustom() {
        let totalPortions = 0;
        let subtotalMenu = 0;
        let subtotalExtra = 0;

        // Hitung menu
        document.querySelectorAll('.custom-menu-cb:checked').forEach(cb => {
            const id = cb.dataset.id;
            const price = parseFloat(cb.dataset.price);
            const qty = parseInt(document.getElementById('custom_menu_input_' + id).value) || 0;
            totalPortions += qty;
            subtotalMenu += price * qty;
        });

        // Hitung extras
        document.querySelectorAll('.custom-extra-cb:checked').forEach(cb => {
            const id = cb.dataset.id;
            const price = parseFloat(cb.dataset.price);
            const qty = parseInt(document.getElementById('custom_extra_input_' + id).value) || 0;
            subtotalExtra += price * qty;
        });

        document.getElementById('custom-total-portions').textContent = totalPortions;
        document.getElementById('custom-subtotal-menu').textContent = formatRupiah(subtotalMenu);
        document.getElementById('custom-subtotal-extra').textContent = formatRupiah(subtotalExtra);
        document.getElementById('custom-total-price').textContent = formatRupiah(subtotalMenu + subtotalExtra);

        // Porsi validation
        const msg = document.getElementById('custom-portion-msg');
        const indicator = document.getElementById('custom-portion-indicator');
        const btn = document.getElementById('custom-submit-btn');
        const hasServing = document.querySelector('.custom-serving-radio:checked');

        if (totalPortions < CUSTOM_MIN_PORTIONS) {
            indicator.className = 'mt-4 p-4 rounded-xl border border-yellow-200 bg-yellow-50';
            msg.textContent = `⚠️ Kurang ${CUSTOM_MIN_PORTIONS - totalPortions} porsi lagi. Minimal ${CUSTOM_MIN_PORTIONS} porsi.`;
            msg.className = 'text-sm font-medium mt-1 text-yellow-700';
            btn.disabled = true;
        } else if (!hasServing) {
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

        // Build hidden fields for custom form submission
        buildCustomFormFields();
    }

    function buildCustomFormFields() {
        // Remove existing hidden items
        document.querySelectorAll('.custom-hidden-item').forEach(el => el.remove());

        const form = document.getElementById('customForm');
        let idx = 0;

        // Menu items
        document.querySelectorAll('.custom-menu-cb:checked').forEach(cb => {
            const id = cb.dataset.id;
            const qty = parseInt(document.getElementById('custom_menu_input_' + id).value) || 0;
            if (qty > 0) {
                appendHidden(form, `items[${idx}][custom_option_id]`, id);
                appendHidden(form, `items[${idx}][quantity]`, qty);
                appendHidden(form, `items[${idx}][item_type]`, 'custom_menu');
                idx++;
            }
        });

        // Extra items
        document.querySelectorAll('.custom-extra-cb:checked').forEach(cb => {
            const id = cb.dataset.id;
            const qty = parseInt(document.getElementById('custom_extra_input_' + id).value) || 0;
            if (qty > 0) {
                appendHidden(form, `items[${idx}][custom_option_id]`, id);
                appendHidden(form, `items[${idx}][quantity]`, qty);
                appendHidden(form, `items[${idx}][item_type]`, 'addition');
                idx++;
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

    // Auto-select if only one mode
    @if($service->hasFeature('packages') && $packages->isNotEmpty() && !$service->hasFeature('full_custom'))
        setMode('package');
    @elseif($service->hasFeature('full_custom') && (!$service->hasFeature('packages') || $packages->isEmpty()))
        setMode('custom');
    @endif
</script>
@endpush
@endsection
