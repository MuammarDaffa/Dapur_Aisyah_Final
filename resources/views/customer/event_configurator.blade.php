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

    {{-- Form Data Pesanan --}}
    <form id="eventForm" action="{{ route('customer.event.checkout') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="catering_service_id" value="{{ $service->id }}">
        
        {{-- Section: Paket --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4">1. Pilih Paket</h3>
            @error('package_id') <p class="text-red-500 text-sm mb-2">{{ $message }}</p> @enderror
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($packages as $pkg)
                <label class="relative cursor-pointer">
                    <input type="radio" name="package_id" value="{{ $pkg->id }}" class="peer sr-only" onchange="selectPackage({{ $pkg->id }})">
                    <div class="p-5 bg-white rounded-xl border-2 border-gray-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all shadow-sm">
                        <h4 class="font-bold text-gray-900">{{ $pkg->name }}</h4>
                        @if($pkg->description)<p class="text-sm text-gray-500 mt-1">{{ $pkg->description }}</p>@endif
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-lg font-bold text-orange-600">{{ $pkg->formatted_price }} / pax</span>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            
            {{-- Porsi Input --}}
            <div class="mt-4" id="portionContainer" style="display: none;">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Total Porsi *</label>
                <input type="number" name="total_portions" id="total_portions" value="{{ $service->min_portion }}" min="{{ $service->min_portion }}" max="{{ $service->max_portion ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-500" onchange="calculateTotal()">
                <p class="text-xs text-gray-500 mt-1">Minimal pemesanan: {{ $service->min_portion }} porsi.</p>
            </div>
        </div>

        {{-- Section: Menu & Penyajian --}}
        <div id="optionsSection" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100" style="display: none;">
            <h3 class="text-lg font-bold text-gray-900 mb-4">2. Pilih Menu & Opsi</h3>
            
            {{-- Menu (Checkboxes for selection within package logic, simplified here to just select menus) --}}
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><span>🍽️</span> Menu (Pilih Menu Sesuai Paket)</h4>
                @error('menus') <p class="text-red-500 text-sm mb-2">Harap pilih minimal 1 menu.</p> @enderror
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="menuContainer">
                    @foreach($customOptions->where('type', 'menu') as $menu)
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="menus[]" value="{{ $menu->id }}" class="w-5 h-5 rounded border-gray-300 text-orange-500">
                        <span class="font-medium text-gray-900">{{ $menu->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Penyajian (Radio) --}}
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><span>🍲</span> Cara Penyajian *</h4>
                @error('serving_type_id') <p class="text-red-500 text-sm mb-2">Harap pilih cara penyajian.</p> @enderror
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($customOptions->where('type', 'serving_type') as $serving)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="serving_type_id" value="{{ $serving->id }}" class="peer sr-only" required>
                        <div class="p-3 text-center border-2 rounded-lg peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-colors">
                            <span class="font-medium text-gray-900">{{ $serving->name }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Extra --}}
            <div>
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><span>✨</span> Tambahan (Extra)</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($customOptions->where('type', 'extra') as $i => $extra)
                    <div class="flex items-center justify-between p-3 border rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $extra->name }}</p>
                            <p class="text-sm text-orange-600">+Rp {{ number_format($extra->price, 0, ',', '.') }}</p>
                            <input type="hidden" name="extras[{{ $i }}][id]" value="{{ $extra->id }}">
                            <input type="hidden" id="extra_price_{{ $i }}" value="{{ $extra->price }}">
                        </div>
                        <input type="number" name="extras[{{ $i }}][qty]" id="extra_qty_{{ $i }}" value="0" min="0" class="w-16 text-center border py-1 rounded" onchange="calculateTotal()">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Section: Detail Pengiriman & Checkout --}}
        <div id="checkoutSection" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100" style="display: none;">
            <h3 class="text-lg font-bold text-gray-900 mb-4">3. Informasi Pengiriman & Acara</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Acara * (Minimal H-3)</label>
                    <input type="date" name="order_date" required min="{{ \Carbon\Carbon::now()->addDays(3)->format('Y-m-d') }}" class="w-full px-4 py-2 rounded-lg border focus:border-orange-500">
                    @error('order_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jam Mulai Acara *</label>
                    <input type="time" name="event_start_time" required class="w-full px-4 py-2 rounded-lg border focus:border-orange-500">
                    @error('event_start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pengiriman *</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="pickup_method" value="delivery" checked class="text-orange-500 focus:ring-orange-500" onchange="toggleAddress(true)">
                        <span>Diantar (Delivery)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="pickup_method" value="pickup" class="text-orange-500 focus:ring-orange-500" onchange="toggleAddress(false)">
                        <span>Ambil Sendiri (Pickup)</span>
                    </label>
                </div>
            </div>

            <div id="addressSection" class="space-y-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kecamatan *</label>
                        <select name="district_id" id="district" class="w-full px-4 py-2 rounded-lg border focus:border-orange-500" onchange="loadVillages(); loadShippingCost();">
                            <option value="">Pilih Kecamatan</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kelurahan/Desa *</label>
                        <select name="village_id" id="village" class="w-full px-4 py-2 rounded-lg border focus:border-orange-500">
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Detail Alamat Lengkap *</label>
                    <textarea name="address_detail" rows="3" class="w-full px-4 py-2 rounded-lg border focus:border-orange-500" placeholder="Nama jalan, RT/RW, patokan..."></textarea>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 rounded-lg border focus:border-orange-500"></textarea>
            </div>

            {{-- Payment Method (Hidden because event is always transfer) --}}
            <input type="hidden" name="payment_method" value="transfer">

            {{-- Total Review --}}
            <div class="mt-6 p-4 bg-orange-50 rounded-xl border border-orange-100 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal Paket + Extra:</span>
                    <span id="labelSubtotal" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm" id="rowShipping">
                    <span class="text-gray-600">Ongkos Kirim:</span>
                    <span id="labelShipping" class="font-medium">Rp 0</span>
                </div>
                <div class="pt-2 border-t border-orange-200 flex justify-between items-center">
                    <span class="font-bold text-gray-900">Total Pembayaran:</span>
                    <span id="labelTotal" class="text-2xl font-bold text-orange-600">Rp 0</span>
                </div>
            </div>

            <button type="submit" class="w-full mt-6 px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-lg rounded-xl hover:shadow-lg transition-all">
                Pesan Sekarang & Bayar via Midtrans
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const packagesData = @json($packages->keyBy('id'));
    let currentPackagePrice = 0;
    let shippingCost = 0;

    function selectPackage(id) {
        document.getElementById('portionContainer').style.display = 'block';
        document.getElementById('optionsSection').style.display = 'block';
        document.getElementById('checkoutSection').style.display = 'block';
        
        currentPackagePrice = parseFloat(packagesData[id].price);
        calculateTotal();
    }

    function toggleAddress(show) {
        document.getElementById('addressSection').style.display = show ? 'block' : 'none';
        document.getElementById('rowShipping').style.display = show ? 'flex' : 'none';
        if (!show) shippingCost = 0;
        else loadShippingCost(); // Reload if switched back
        calculateTotal();
    }

    function calculateTotal() {
        let portions = parseInt(document.getElementById('total_portions').value) || 0;
        let subtotal = currentPackagePrice * portions;

        // Add extras
        document.querySelectorAll('input[id^="extra_qty_"]').forEach((input, index) => {
            let qty = parseInt(input.value) || 0;
            let price = parseFloat(document.getElementById(`extra_price_${index}`).value) || 0;
            subtotal += (price * qty); // extra price * extra qty (not portions!)
        });

        let total = subtotal + shippingCost;

        document.getElementById('labelSubtotal').textContent = formatRupiah(subtotal);
        document.getElementById('labelTotal').textContent = formatRupiah(total);
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    function loadVillages() {
        const districtId = document.getElementById('district').value;
        const villageSelect = document.getElementById('village');
        villageSelect.innerHTML = '<option value="">Memuat...</option>';

        if (districtId) {
            fetch(`/api/villages/${districtId}`)
                .then(res => res.json())
                .then(data => {
                    villageSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
                    data.forEach(v => {
                        villageSelect.innerHTML += `<option value="${v.id}">${v.name}</option>`;
                    });
                });
        } else {
            villageSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
        }
    }

    function loadShippingCost() {
        const districtId = document.getElementById('district').value;
        if (districtId && document.querySelector('input[name="pickup_method"]:checked').value === 'delivery') {
            fetch(`/api/shipping-cost/${districtId}`)
                .then(res => res.json())
                .then(data => {
                    shippingCost = parseFloat(data.cost) || 0;
                    document.getElementById('labelShipping').textContent = formatRupiah(shippingCost);
                    calculateTotal();
                });
        } else {
            shippingCost = 0;
            document.getElementById('labelShipping').textContent = 'Rp 0';
            calculateTotal();
        }
    }
</script>
@endpush
@endsection
