@extends('layouts.app')
@section('title', 'Checkout Event')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>#eventMap{height:250px;border-radius:12px;z-index:0;}</style>
@endpush
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('customer.cart', ['tab' => 'event']) }}" class="text-sm text-orange-500 hover:text-orange-600">← Kembali ke Keranjang</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">📋 Checkout <span class="text-orange-500">Event</span></h2>
    </div>

    <form action="{{ route('customer.event.checkout.store', $groupId) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Section --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Acara --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">📅 Informasi Acara</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $minDays = $service->minimal_order_days ?? 3;
                                $minDate = \Carbon\Carbon::now()->addDays($minDays)->format('Y-m-d');
                            @endphp
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Acara * (Minimal H-{{ $minDays }})</label>
                            <input type="date" name="order_date" required min="{{ $minDate }}" value="{{ old('order_date') }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 @error('order_date') border-red-400 @enderror">
                            @error('order_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jam Mulai Acara *</label>
                            <input type="time" name="event_start_time" required value="{{ old('event_start_time') }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 @error('event_start_time') border-red-400 @enderror">
                            @error('event_start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Pengiriman --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">🚚 Pengiriman</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pengiriman *</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="pickup_method" value="delivery" checked class="text-orange-500 focus:ring-orange-500" onchange="toggleEventAddress(true)">
                                    <span>Diantar (Delivery)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="pickup_method" value="pickup" class="text-orange-500 focus:ring-orange-500" onchange="toggleEventAddress(false)">
                                    <span>Ambil Sendiri (Pickup)</span>
                                </label>
                            </div>
                        </div>

                        <div id="eventAddressSection" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan (Otomatis dari Peta)</label>
                                    <select name="district_id" id="district_id" class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 focus:border-orange-400 pointer-events-none" readonly>
                                        <option value="">-- Pilih dari peta di bawah --</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}" data-lat="{{ $district->latitude ?? '' }}" data-lng="{{ $district->longitude ?? '' }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-orange-500 mt-1">Kecamatan akan terisi otomatis setelah Anda menggeser pin di peta.</p>
                                    @error('district_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan/Desa *</label>
                                    <select name="village_id" id="eventVillage" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400">
                                        <option value="">Pilih Kelurahan</option>
                                    </select>
                                    @error('village_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Detail Alamat Lengkap *</label>
                                <textarea name="address_detail" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" placeholder="Nama jalan, RT/RW, patokan...">{{ old('address_detail') }}</textarea>
                                @error('address_detail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <!-- Peta Lokasi (Leaflet.js) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">📍 Tandai Lokasi Acara di Peta</label>
                                <p class="text-xs text-gray-500 mb-2">Klik pada peta untuk menentukan titik lokasi pengiriman yang tepat.</p>
                                <div id="eventMap"></div>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                <div class="flex justify-between items-center mt-2">
                                    <p class="text-xs text-gray-400" id="coord-display">Koordinat belum dipilih</p>
                                </div>
                                <span id="geocode-status" class="hidden"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Catatan & Pembayaran --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">💳 Pembayaran & Catatan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" placeholder="Catatan untuk pesanan event ini...">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800 font-medium">💳 Transfer Bank (Midtrans)</p>
                                <p class="text-xs text-blue-600">Semua pembayaran dilakukan melalui Midtrans Payment Gateway.</p>
                            </div>
                            <input type="hidden" name="payment_method" value="transfer">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div>
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pesanan</h3>

                    {{-- Service & Package Info --}}
                    <div class="mb-4 pb-4 border-b border-gray-100">
                        <p class="text-sm text-gray-500">Layanan</p>
                        <p class="font-semibold text-gray-900">{{ $service->name ?? '-' }}</p>
                        @if($packageItem && $packageItem->cateringPackage)
                            <p class="text-sm text-orange-600 font-medium mt-1">Paket: {{ $packageItem->cateringPackage->name }}</p>
                        @else
                            <p class="text-sm text-blue-600 font-medium mt-1">Custom Menu</p>
                        @endif
                    </div>

                    {{-- Items --}}
                    <div class="divide-y divide-gray-100 mb-4 max-h-48 overflow-y-auto">
                        @foreach($menuItems as $cart)
                        <div class="py-2 flex justify-between text-sm">
                            <div>
                                <p class="text-gray-700">{{ $cart->customOption->name ?? 'Item' }}</p>
                                <p class="text-xs text-gray-500">× {{ $cart->quantity }}
                                    @if($cart->item_type === 'package_item')
                                        <span class="text-green-600">(termasuk)</span>
                                    @endif
                                </p>
                            </div>
                            <p class="font-medium {{ $cart->item_type === 'package_item' ? 'text-green-600' : 'text-gray-900' }}">
                                {{ $cart->item_type === 'package_item' ? 'Rp 0' : 'Rp ' . number_format($cart->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                        @endforeach

                        @foreach($additionItems as $cart)
                        <div class="py-2 flex justify-between text-sm">
                            <div>
                                <p class="text-gray-700">{{ $cart->customOption->name ?? 'Extra' }}</p>
                                <p class="text-xs text-gray-500">× {{ $cart->quantity }}</p>
                            </div>
                            <p class="font-medium text-gray-900">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>

                    @if($servingType)
                    <div class="mb-4 pb-4 border-b border-gray-100">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">🍲 Penyajian</span>
                            <span class="font-medium text-gray-700">{{ $servingType->name }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Totals --}}
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm" id="eventRowShipping">
                            <span class="text-gray-500">Ongkos Kirim</span>
                            <span class="font-medium" id="eventShippingDisplay">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-orange-600" id="eventTotalDisplay">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-4 px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-lg rounded-xl hover:shadow-lg transition-all">
                        Pesan & Bayar via Midtrans
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    const eventSubtotal = {{ $subtotal }};
    let eventShippingCost = 0;
    let eventMap, eventMarker;
    const defaultLat = -0.0263;
    const defaultLng = 109.3425;

    function initEventMap() {
        const pontianakBounds = [
            [-0.15, 109.20],
            [0.08, 109.45]
        ];

        eventMap = L.map('eventMap', {
            maxBounds: pontianakBounds,
            maxBoundsViscosity: 1.0,
            minZoom: 11
        }).setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
            bounds: pontianakBounds
        }).addTo(eventMap);

        eventMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(eventMap);

        eventMarker.on('dragend', function (e) {
            const pos = eventMarker.getLatLng();
            setEventCoordinates(pos.lat, pos.lng);
            reverseGeocode(pos.lat, pos.lng);
        });

        eventMap.on('click', function (e) {
            eventMarker.setLatLng(e.latlng);
            setEventCoordinates(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        setTimeout(() => eventMap.invalidateSize(), 300);
    }

    function setEventCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        document.getElementById('coord-display').textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
    }

    function reverseGeocode(lat, lng) {
        const statusEl = document.getElementById('geocode-status');
        statusEl.innerHTML = '⏳ Mendeteksi kecamatan...';
        statusEl.className = 'text-xs text-orange-500 font-medium mt-2 block';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.address) {
                    let districtName = data.address.city_district || data.address.suburb || data.address.town || data.address.county || '';
                    
                    if (!districtName) {
                        statusEl.innerHTML = '❌ Gagal mendeteksi wilayah. Silakan geser pin ke area permukiman.';
                        statusEl.className = 'text-xs text-red-500 font-medium mt-2 block';
                        document.getElementById('district_id').value = "";
                        loadVillages("");
                        return;
                    }

                    if (!districtName.toLowerCase().startsWith('kecamatan')) {
                        districtName = 'Kecamatan ' + districtName;
                    }

                    const select = document.getElementById('district_id');
                    let matchFound = false;
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].text.toLowerCase() === districtName.toLowerCase()) {
                            select.selectedIndex = i;
                            matchFound = true;
                            loadVillages(select.options[i].value);
                            break;
                        }
                    }

                    if (matchFound) {
                        statusEl.innerHTML = `✅ Lokasi: <b>${districtName}</b>`;
                        statusEl.className = 'text-xs text-green-600 font-medium mt-2 block';
                    } else {
                        statusEl.innerHTML = `⚠️ Lokasi terdeteksi sebagai <b>${districtName}</b> (Di luar jangkauan wilayah kami)`;
                        statusEl.className = 'text-xs text-red-500 font-medium mt-2 block';
                        document.getElementById('district_id').value = "";
                        loadVillages("");
                    }
                }
            })
            .catch(err => {
                statusEl.innerHTML = '❌ Gagal menghubungi server peta.';
                statusEl.className = 'text-xs text-red-500 font-medium mt-2 block';
            });
    }

    function toggleEventAddress(show) {
        document.getElementById('eventAddressSection').style.display = show ? 'block' : 'none';
        document.getElementById('eventRowShipping').style.display = show ? 'flex' : 'none';
        if (!show) {
            eventShippingCost = 0;
            updateEventTotal();
        } else {
            loadEventShippingCost();
            if (!eventMap) {
                setTimeout(initEventMap, 100);
            } else {
                setTimeout(() => eventMap.invalidateSize(), 100);
            }
        }
    }

    function loadVillages(districtId) {
        const villageSelect = document.getElementById('eventVillage');
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
            loadEventShippingCost();
        } else {
            villageSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            loadEventShippingCost();
        }
    }

    function loadEventShippingCost() {
        const districtId = document.getElementById('eventDistrict').value;
        const pickupMethod = document.querySelector('input[name="pickup_method"]:checked')?.value;

        if (districtId && pickupMethod === 'delivery') {
            fetch(`/api/shipping-cost/${districtId}`)
                .then(res => res.json())
                .then(data => {
                    eventShippingCost = parseFloat(data.cost) || 0;
                    document.getElementById('eventShippingDisplay').textContent = 'Rp ' + Number(eventShippingCost).toLocaleString('id-ID');
                    updateEventTotal();
                });
        } else {
            eventShippingCost = 0;
            document.getElementById('eventShippingDisplay').textContent = 'Rp 0';
            updateEventTotal();
        }
    }

    function updateEventTotal() {
        const total = eventSubtotal + eventShippingCost;
        document.getElementById('eventTotalDisplay').textContent = 'Rp ' + Number(total).toLocaleString('id-ID');
    }

    // Init map on page load (delivery is default)
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initEventMap, 200);
    });
</script>
@endpush
@endsection
