@extends('layouts.app')
@section('title', 'Konfigurasi Event - ' . $service->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('landing') }}" class="text-sm text-orange-500 hover:text-orange-600">← Kembali</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">🎉 {{ $service->name }}</h2>
        <p class="text-gray-500">{{ $service->description }}</p>
        @if($service->min_portion || $service->max_portion)
        <p class="text-sm text-gray-600 mt-1">
            Porsi: {{ $service->min_portion ?? 1 }}
            @if($service->max_portion) - {{ $service->max_portion }} @else + @endif porsi
        </p>
        @endif
    </div>

    {{-- Step Indicator --}}
    <div class="flex items-center gap-2 mb-8">
        <div id="step1-indicator" class="flex items-center gap-2 px-4 py-2 rounded-full bg-orange-500 text-white text-sm font-medium transition-all">
            <span>1</span> Pilih Metode
        </div>
        <div class="w-8 h-px bg-gray-300"></div>
        <div id="step2-indicator" class="flex items-center gap-2 px-4 py-2 rounded-full bg-gray-200 text-gray-500 text-sm font-medium transition-all">
            <span>2</span> Konfigurasi
        </div>
        <div class="w-8 h-px bg-gray-300"></div>
        <div id="step3-indicator" class="flex items-center gap-2 px-4 py-2 rounded-full bg-gray-200 text-gray-500 text-sm font-medium transition-all">
            <span>3</span> Review
        </div>
    </div>

    {{-- Step 1: Pilih Metode --}}
    <div id="step1" class="space-y-4">
        <h3 class="text-lg font-bold text-gray-900">Pilih Metode Pemesanan</h3>

        @if($service->hasFeature('packages') && $packages->isNotEmpty())
        <button type="button" onclick="selectMethod('package')" class="method-btn w-full text-left p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-orange-400 transition-all shadow-sm">
            <div class="flex items-start gap-4">
                <span class="text-3xl">📋</span>
                <div>
                    <h4 class="font-bold text-gray-900 text-lg">Paket + Tambahan</h4>
                    <p class="text-sm text-gray-500 mt-1">Pilih paket yang sudah disiapkan, lalu tambahkan menu/item tambahan sesuai kebutuhan.</p>
                    <p class="text-xs text-orange-600 mt-2 font-medium">{{ $packages->count() }} paket tersedia</p>
                </div>
            </div>
        </button>
        @endif

        @if($service->hasFeature('full_custom'))
        <button type="button" onclick="selectMethod('custom')" class="method-btn w-full text-left p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-blue-400 transition-all shadow-sm">
            <div class="flex items-start gap-4">
                <span class="text-3xl">🎨</span>
                <div>
                    <h4 class="font-bold text-gray-900 text-lg">Full Custom</h4>
                    <p class="text-sm text-gray-500 mt-1">Pilih semua menu, dekorasi, penyajian, dan extra secara manual sesuai keinginan Anda.</p>
                </div>
            </div>
        </button>
        @endif
    </div>

    {{-- Step 2: Konfigurasi --}}
    <div id="step2" class="hidden space-y-6">

        {{-- Package Selection (only for package method) --}}
        <div id="packageSection" class="hidden">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pilih Paket</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="packageCards">
                @foreach($packages as $pkg)
                <button type="button" onclick="selectPackage({{ $pkg->id }})" class="pkg-card text-left p-5 bg-white rounded-xl border-2 border-gray-200 hover:border-orange-400 transition-all shadow-sm" data-pkg-id="{{ $pkg->id }}">
                    <h4 class="font-bold text-gray-900">{{ $pkg->name }}</h4>
                    @if($pkg->description)<p class="text-sm text-gray-500 mt-1">{{ $pkg->description }}</p>@endif
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-lg font-bold text-orange-600">{{ $pkg->formatted_price }}</span>
                        <span class="text-sm text-gray-500">{{ $pkg->total_portions }} porsi</span>
                    </div>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Package Content (menu portion splitting) --}}
        <div id="packageContentSection" class="hidden">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Isi Paket</h3>
            <p class="text-sm text-gray-500 mb-4">Bagi total porsi paket ke menu yang tersedia. Total harus tepat <strong id="totalPortionLabel">0</strong> porsi.</p>
            <div id="packageMenuItems" class="space-y-3"></div>
            <div class="mt-3 p-3 rounded-lg bg-orange-50 border border-orange-200 flex justify-between items-center">
                <span class="text-sm font-medium text-orange-700">Sisa porsi:</span>
                <span id="remainingPortion" class="text-lg font-bold text-orange-600">0</span>
            </div>
        </div>

        {{-- Custom Options Selection --}}
        <div id="optionsSection">
            <h3 class="text-lg font-bold text-gray-900 mb-4" id="optionsSectionTitle">Pilih Opsi</h3>

            @php
                $optionsByType = $customOptions->groupBy('type');
            @endphp

            @foreach($optionsByType as $type => $opts)
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    @switch($type)
                        @case('menu') <span>🍽️</span> Menu @break
                        @case('decoration') <span>🎨</span> Dekorasi @break
                        @case('serving_type') <span>🍲</span> Penyajian @break
                        @case('extra') <span>➕</span> Extra @break
                        @default <span>📦</span> {{ ucfirst(str_replace('_',' ',$type)) }}
                    @endswitch
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($opts as $opt)
                    <div class="option-card flex items-center justify-between p-4 bg-white rounded-xl border border-gray-200 hover:border-orange-300 transition-all" data-option-id="{{ $opt->id }}" data-type="{{ $opt->type }}" data-price="{{ $opt->price }}" data-name="{{ $opt->name }}">
                        <div>
                            <p class="font-medium text-gray-900">{{ $opt->name }}</p>
                            <p class="text-sm text-orange-600 font-semibold">Rp {{ number_format($opt->price, 0, ',', '.') }}/porsi</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="changeOptionQty({{ $opt->id }}, -1)" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold flex items-center justify-center">−</button>
                            <input type="number" id="opt-qty-{{ $opt->id }}" value="0" min="0" class="w-14 text-center border rounded-lg py-1 text-sm font-semibold" onchange="updateOptionQty({{ $opt->id }})">
                            <button type="button" onclick="changeOptionQty({{ $opt->id }}, 1)" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold flex items-center justify-center">+</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="goToStep(1)" class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-colors">← Kembali</button>
            <button type="button" onclick="goToStep(3)" class="flex-1 px-6 py-3 bg-orange-500 text-white font-semibold rounded-xl hover:bg-orange-600 transition-colors">Review Pesanan →</button>
        </div>
    </div>

    {{-- Step 3: Review --}}
    <div id="step3" class="hidden space-y-6">
        <h3 class="text-lg font-bold text-gray-900">Review Pesanan Event</h3>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div id="reviewContent"></div>

            {{-- Total --}}
            <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between items-center">
                <span class="text-lg font-bold text-gray-900">Total Estimasi</span>
                <span id="reviewTotal" class="text-2xl font-bold text-orange-600">Rp 0</span>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="goToStep(2)" class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-colors">← Kembali</button>
            <form id="eventForm" action="{{ route('customer.cart.event-group') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="catering_service_id" value="{{ $service->id }}">
                <input type="hidden" name="catering_package_id" id="formPackageId" value="">
                <div id="formItemsContainer"></div>
                <button type="submit" class="w-full px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                    Tambahkan ke Keranjang 🛒
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Data from server
    const serviceId = {{ $service->id }};
    const packagesData = @json($packages->keyBy('id'));
    const optionsData = @json($customOptions->keyBy('id'));

    let currentMethod = null; // 'package' or 'custom'
    let selectedPackageId = null;
    let selectedPackageData = null;

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    // Step Navigation
    function goToStep(step) {
        // Validation before proceeding
        if (step === 3) {
            if (!validateConfiguration()) return;
        }

        document.getElementById('step1').classList.toggle('hidden', step !== 1);
        document.getElementById('step2').classList.toggle('hidden', step !== 2);
        document.getElementById('step3').classList.toggle('hidden', step !== 3);

        ['step1-indicator', 'step2-indicator', 'step3-indicator'].forEach((id, i) => {
            const el = document.getElementById(id);
            if (i + 1 <= step) {
                el.classList.remove('bg-gray-200', 'text-gray-500');
                el.classList.add('bg-orange-500', 'text-white');
            } else {
                el.classList.remove('bg-orange-500', 'text-white');
                el.classList.add('bg-gray-200', 'text-gray-500');
            }
        });

        if (step === 3) buildReview();
    }

    // Method Selection
    function selectMethod(method) {
        currentMethod = method;

        if (method === 'package') {
            document.getElementById('packageSection').classList.remove('hidden');
            document.getElementById('optionsSectionTitle').textContent = 'Tambahan (Opsional)';
        } else {
            document.getElementById('packageSection').classList.add('hidden');
            document.getElementById('packageContentSection').classList.add('hidden');
            document.getElementById('optionsSectionTitle').textContent = 'Pilih Menu & Opsi';
            selectedPackageId = null;
            selectedPackageData = null;
        }

        goToStep(2);
    }

    // Package Selection
    function selectPackage(pkgId) {
        selectedPackageId = pkgId;
        selectedPackageData = packagesData[pkgId];

        // Highlight selected
        document.querySelectorAll('.pkg-card').forEach(c => {
            c.classList.remove('border-orange-500', 'bg-orange-50');
            c.classList.add('border-gray-200');
        });
        const selected = document.querySelector(`.pkg-card[data-pkg-id="${pkgId}"]`);
        selected.classList.remove('border-gray-200');
        selected.classList.add('border-orange-500', 'bg-orange-50');

        // Show package content for portion splitting
        if (selectedPackageData.custom_options && selectedPackageData.custom_options.length > 0) {
            buildPackageContent();
            document.getElementById('packageContentSection').classList.remove('hidden');
        }
    }

    function buildPackageContent() {
        const container = document.getElementById('packageMenuItems');
        container.innerHTML = '';
        const totalPortions = selectedPackageData.total_portions;
        document.getElementById('totalPortionLabel').textContent = totalPortions;

        const menuOptions = selectedPackageData.custom_options.filter(o => o.type === 'menu');

        menuOptions.forEach(opt => {
            const defaultQty = opt.pivot ? opt.pivot.quantity : 0;
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
            div.innerHTML = `
                <div>
                    <span class="font-medium text-gray-900">${opt.name}</span>
                    <span class="text-sm text-gray-500 ml-2">Rp ${Number(opt.price).toLocaleString('id-ID')}/porsi</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="changePkgMenuQty(${opt.id}, -1)" class="w-8 h-8 rounded-lg bg-white border text-gray-600 font-bold flex items-center justify-center">−</button>
                    <input type="number" id="pkg-menu-${opt.id}" value="${defaultQty}" min="0" max="${totalPortions}" class="w-16 text-center border rounded-lg py-1 text-sm font-semibold" onchange="updateRemainingPortion()">
                    <button type="button" onclick="changePkgMenuQty(${opt.id}, 1)" class="w-8 h-8 rounded-lg bg-white border text-gray-600 font-bold flex items-center justify-center">+</button>
                </div>
            `;
            container.appendChild(div);

            // Set qty di option card juga
            const optInput = document.getElementById(`opt-qty-${opt.id}`);
            if (optInput) optInput.value = defaultQty;
        });

        updateRemainingPortion();
    }

    function changePkgMenuQty(optId, delta) {
        const input = document.getElementById(`pkg-menu-${optId}`);
        let val = parseInt(input.value) + delta;
        if (val < 0) val = 0;
        input.value = val;

        // Sync to option qty
        const optInput = document.getElementById(`opt-qty-${optId}`);
        if (optInput) optInput.value = val;

        updateRemainingPortion();
    }

    function updateRemainingPortion() {
        if (!selectedPackageData) return;
        const totalPortions = selectedPackageData.total_portions;
        let used = 0;

        const menuOptions = selectedPackageData.custom_options.filter(o => o.type === 'menu');
        menuOptions.forEach(opt => {
            const input = document.getElementById(`pkg-menu-${opt.id}`);
            if (input) used += parseInt(input.value) || 0;
        });

        const remaining = totalPortions - used;
        const el = document.getElementById('remainingPortion');
        el.textContent = remaining;
        el.classList.toggle('text-red-600', remaining < 0);
        el.classList.toggle('text-green-600', remaining === 0);
        el.classList.toggle('text-orange-600', remaining > 0);
    }

    // Option Qty Management
    function changeOptionQty(optId, delta) {
        const input = document.getElementById(`opt-qty-${optId}`);
        let val = parseInt(input.value) + delta;
        if (val < 0) val = 0;
        input.value = val;
    }

    function updateOptionQty(optId) {
        // If this is a package menu item, sync back
        const pkgInput = document.getElementById(`pkg-menu-${optId}`);
        if (pkgInput) {
            const optInput = document.getElementById(`opt-qty-${optId}`);
            pkgInput.value = optInput.value;
            updateRemainingPortion();
        }
    }

    // Validation
    function validateConfiguration() {
        // Check if any items selected
        let hasItems = false;
        document.querySelectorAll('[id^="opt-qty-"]').forEach(input => {
            if (parseInt(input.value) > 0) hasItems = true;
        });

        if (!hasItems && !selectedPackageId) {
            alert('Pilih minimal satu item.');
            return false;
        }

        // For package method, validate portion splitting
        if (currentMethod === 'package' && selectedPackageData) {
            const remaining = parseInt(document.getElementById('remainingPortion').textContent);
            if (remaining !== 0) {
                alert(`Total porsi harus tepat ${selectedPackageData.total_portions}. Sisa: ${remaining}`);
                return false;
            }
        }

        return true;
    }

    // Build Review
    function buildReview() {
        const reviewContent = document.getElementById('reviewContent');
        const formItems = document.getElementById('formItemsContainer');
        reviewContent.innerHTML = '';
        formItems.innerHTML = '';

        let total = 0;
        let itemIndex = 0;

        // Package price
        if (currentMethod === 'package' && selectedPackageData) {
            document.getElementById('formPackageId').value = selectedPackageId;
            total += parseFloat(selectedPackageData.price);

            reviewContent.innerHTML += `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <div>
                        <span class="font-bold text-gray-900">📋 ${selectedPackageData.name}</span>
                        <span class="text-xs text-gray-500 ml-2">(${selectedPackageData.total_portions} porsi)</span>
                    </div>
                    <span class="font-bold text-orange-600">${formatRupiah(selectedPackageData.price)}</span>
                </div>
            `;
        } else {
            document.getElementById('formPackageId').value = '';
        }

        // Collect selected options
        document.querySelectorAll('[id^="opt-qty-"]').forEach(input => {
            const qty = parseInt(input.value);
            if (qty <= 0) return;

            const optId = input.id.replace('opt-qty-', '');
            const opt = optionsData[optId];
            if (!opt) return;

            // Determine if this is a package item or addition
            let isPackageItem = false;
            if (currentMethod === 'package' && selectedPackageData) {
                const pkgOpts = selectedPackageData.custom_options.map(o => o.id);
                if (pkgOpts.includes(parseInt(optId)) && opt.type === 'menu') {
                    isPackageItem = true;
                }
            }

            const itemType = isPackageItem ? 'package_item' : 'addition';
            const itemPrice = isPackageItem ? 0 : parseFloat(opt.price) * qty;
            total += itemPrice;

            reviewContent.innerHTML += `
                <div class="flex justify-between items-center py-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${isPackageItem ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'}">${isPackageItem ? 'Paket' : 'Tambahan'}</span>
                        <span class="text-gray-900">${opt.name}</span>
                        <span class="text-gray-500">× ${qty}</span>
                    </div>
                    <span class="font-medium ${isPackageItem ? 'text-green-600' : 'text-orange-600'}">${isPackageItem ? 'Termasuk' : formatRupiah(itemPrice)}</span>
                </div>
            `;

            // Add hidden form fields
            formItems.innerHTML += `
                <input type="hidden" name="items[${itemIndex}][custom_option_id]" value="${optId}">
                <input type="hidden" name="items[${itemIndex}][quantity]" value="${qty}">
                <input type="hidden" name="items[${itemIndex}][item_type]" value="${itemType}">
            `;
            itemIndex++;
        });

        document.getElementById('reviewTotal').textContent = formatRupiah(total);
    }
</script>
@endpush
@endsection
