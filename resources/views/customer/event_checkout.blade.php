@extends('layouts.app')
@section('title', 'Checkout Event')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>#eventMap{height:250px;border-radius:12px;z-index:0;}</style>
@endpush
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('customer.cart', ['tab' => 'event']) }}" class="inline-flex items-center text-sm text-orange-500 hover:text-orange-600">
            <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Keranjang</span>
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Checkout <span class="text-orange-500">Event</span></h2>
    </div>

    <form id="event-checkout-form" action="{{ route('customer.event.checkout.store', $groupId) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Section --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Acara --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Informasi Acara</h3>
                    <div>
                        <div>
                            @php
                                $minDays = $minDays ?? ($service?->minimal_order_days ?? 1);
                                $minDate = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay()->addDays($minDays)->format('Y-m-d');
                            @endphp
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Acara * (Minimal H-{{ $minDays }})</label>
                            <input type="date" lang="id-ID" name="order_date" required min="{{ $minDate }}" value="{{ old('order_date') }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 @error('order_date') border-red-400 @enderror">
                            @error('order_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Pengiriman --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Pengiriman</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pengiriman *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 transition-all [&:has(input:checked)]:border-orange-500 [&:has(input:checked)]:bg-orange-50">
                                    <input type="radio" name="pickup_method" value="delivery" checked class="absolute top-4 right-4 text-orange-500 focus:ring-orange-500" onchange="toggleEventAddress(true)">
                                    <span class="font-bold text-gray-900 mb-1">Diantar (Delivery)</span>
                                    <span class="text-sm text-gray-500">Pesanan akan diantar ke lokasi acara Anda.</span>
                                </label>
                                <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 transition-all [&:has(input:checked)]:border-orange-500 [&:has(input:checked)]:bg-orange-50">
                                    <input type="radio" name="pickup_method" value="pickup" class="absolute top-4 right-4 text-orange-500 focus:ring-orange-500" onchange="toggleEventAddress(false)">
                                    <span class="font-bold text-gray-900 mb-1">Ambil Sendiri (Pickup)</span>
                                    <span class="text-sm text-gray-500">Ambil pesanan langsung di dapur kami.</span>
                                </label>
                            </div>
                        </div>

                        <div id="eventAddressSection" class="space-y-6 mt-6">
                            <!-- Peta Lokasi (Leaflet.js) -->
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tandai Lokasi Acara di Peta</label>
                                <p class="text-sm text-gray-500 mb-3">Geser peta dan klik untuk menentukan titik lokasi pengiriman yang tepat.</p>
                                <div id="eventMap" class="w-full h-[400px] rounded-xl border-2 border-gray-200 z-0 shadow-sm"></div>
                                <p id="location-validation-msg" class="text-sm font-medium mt-2 hidden"></p>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                <div class="flex justify-between items-center mt-2 hidden">
                                    <p class="text-xs text-gray-400" id="coord-display">Koordinat belum dipilih</p>
                                </div>
                                <span id="geocode-status" class="hidden"></span>
                                <p id="map_error" class="text-sm text-red-500 mt-2 font-medium hidden">Anda wajib menandai lokasi pengiriman di peta.</p>
                                @error('district_id')
                                    <p class="text-sm text-red-500 mt-2 font-medium">Anda harus menandai lokasi pengiriman di peta dengan benar.</p>
                                @enderror
                            </div>

                            <!-- Detail Alamat -->
                            <div class="space-y-4">
                                <select name="district_id" id="district_id" class="hidden">
                                    <option value="">-- Pilih dari peta di bawah --</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" data-lat="{{ $district->latitude ?? '' }}" data-lng="{{ $district->longitude ?? '' }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Berdasarkan Peta</label>
                                    <div id="osm-address-display" class="p-4 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 min-h-[56px] flex items-center">
                                        Lokasi belum ditandai di peta.
                                    </div>
                                    <input type="hidden" name="osm_address" id="osm_address">
                                </div>

                                <!-- <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Detail Patokan/Blok/No. Rumah (Opsional)</label>
                                    <textarea name="address_detail" id="address_detail_input" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 @error('address_detail') border-red-400 @enderror" placeholder="Contoh: Rumah cat putih pagar hitam, dekat masjid..." oninput="validateEventCheckout()">{{ old('address_detail') }}</textarea>
                                    <p id="address_error" class="text-sm text-red-500 mt-1 hidden"></p>
                                    @error('address_detail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Catatan & Pembayaran --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Pembayaran & Catatan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" placeholder="Catatan untuk pesanan event ini...">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800 font-medium">Transfer Bank (Midtrans)</p>
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

                    {{-- Groups List --}}
                    <div class="space-y-4 mb-4 pb-4 border-b border-gray-100 max-h-72 overflow-y-auto">
                        @foreach($eventGroups as $gId => $gItems)
                        @php
                            $gPkg = $gItems->firstWhere('item_type', 'package');
                            $gMenus = $gItems->whereIn('item_type', ['package_item', 'custom_menu']);
                            $gAdditions = $gItems->where('item_type', 'addition');
                            $gService = $gItems->first()->cateringService;
                            $gServing = $gItems->first()->servingType;
                        @endphp
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <div class="pb-2 mb-2 border-b border-gray-200">
                                <p class="font-bold text-gray-900 text-sm">{{ $gService->name ?? '-' }}</p>
                                @if($gPkg && $gPkg->cateringPackage)
                                    <p class="text-xs text-orange-600 font-semibold mt-0.5">Paket: {{ $gPkg->cateringPackage->name }}</p>
                                @else
                                    <p class="text-xs text-blue-600 font-semibold mt-0.5">Custom Menu</p>
                                @endif
                            </div>

                            <div class="divide-y divide-gray-200/60 space-y-1">
                                @foreach($gMenus as $cart)
                                <div class="pt-1 flex justify-between text-xs">
                                    <div>
                                        <span class="text-gray-700 font-medium">{{ $cart->customOption->name ?? 'Item' }}</span>
                                        <span class="text-gray-500 ml-1">× {{ $cart->quantity }}</span>
                                        @if($cart->item_type === 'package_item')
                                            <span class="text-green-600 font-medium">(termasuk)</span>
                                        @endif
                                    </div>
                                    <span class="font-semibold {{ $cart->item_type === 'package_item' ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ $cart->item_type === 'package_item' ? 'Rp 0' : 'Rp ' . number_format($cart->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                                @endforeach

                                @foreach($gAdditions as $cart)
                                <div class="pt-1 flex justify-between text-xs">
                                    <div>
                                        <span class="text-gray-700 font-medium">{{ $cart->customOption->name ?? 'Extra' }}</span>
                                        <span class="text-gray-500 ml-1">× {{ $cart->quantity }}</span>
                                    </div>
                                    <span class="font-semibold text-gray-900">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>

                            @if($gServing)
                            <div class="mt-2 pt-2 border-t border-gray-200/60 flex justify-between text-xs text-gray-600">
                                <span>Penyajian:</span>
                                <span class="font-medium text-gray-800">{{ $gServing->name }}</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

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

                    <button type="submit" id="event-submit-btn" class="w-full mt-4 px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-lg rounded-xl hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        Bayar Sekarang
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
    let isLocationValid = false;
    const defaultLat = -0.0263;
    const defaultLng = 109.3425;

    function initEventMap() {
        const pontianakBounds = [
            [-0.15, 109.20],
            [0.08, 109.45]
        ];

        const initialLat = parseFloat(document.getElementById('latitude').value) || defaultLat;
        const initialLng = parseFloat(document.getElementById('longitude').value) || defaultLng;

        eventMap = L.map('eventMap', {
            maxBounds: pontianakBounds,
            maxBoundsViscosity: 1.0,
            minZoom: 11
        }).setView([initialLat, initialLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
            bounds: pontianakBounds
        }).addTo(eventMap);

        eventMarker = L.marker([initialLat, initialLng], { draggable: true }).addTo(eventMap);

        eventMarker.on('dragend', function (e) {
            const pos = eventMarker.getLatLng();
            setEventCoordinates(pos.lat, pos.lng);
            checkLocationRealtime(pos.lat, pos.lng);
            reverseGeocode(pos.lat, pos.lng);
        });

        eventMap.on('click', function (e) {
            eventMarker.setLatLng(e.latlng);
            setEventCoordinates(e.latlng.lat, e.latlng.lng);
            checkLocationRealtime(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        setTimeout(() => eventMap.invalidateSize(), 300);

        setEventCoordinates(initialLat, initialLng);
        checkLocationRealtime(initialLat, initialLng);
        reverseGeocode(initialLat, initialLng);
    }

    function setEventCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        document.getElementById('coord-display').textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
    }

    function checkLocationRealtime(lat, lng, districtId = '', districtName = '', address = '') {
        const msgEl = document.getElementById('location-validation-msg');
        if (!msgEl) return;

        fetch(`/api/validate-location?latitude=${lat}&longitude=${lng}&district_id=${districtId}&district_name=${encodeURIComponent(districtName)}&address=${encodeURIComponent(address)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.is_in_pontianak) {
                    isLocationValid = true;
                    msgEl.textContent = 'Lokasi berada di wilayah Pontianak.';
                    msgEl.className = 'text-sm font-medium mt-2 text-green-600 block';
                    if (data.district_id) {
                        const select = document.getElementById('district_id');
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].value == data.district_id) {
                                select.selectedIndex = i;
                                loadEventShippingCost();
                                break;
                            }
                        }
                    }
                } else {
                    isLocationValid = false;
                    msgEl.textContent = 'Lokasi berada di luar wilayah Pontianak.';
                    msgEl.className = 'text-sm font-medium mt-2 text-red-600 block';
                    document.getElementById('district_id').value = '';
                    loadEventShippingCost();
                }
                validateEventCheckout();
            })
            .catch(err => {
                console.error('Validation error:', err);
                isLocationValid = false;
                msgEl.textContent = 'Lokasi berada di luar wilayah Pontianak.';
                msgEl.className = 'text-sm font-medium mt-2 text-red-600 block';
                validateEventCheckout();
            });
    }

    function reverseGeocode(lat, lng) {
        const statusEl = document.getElementById('geocode-status');
        const addressDisplay = document.getElementById('osm-address-display');
        const osmAddressInput = document.getElementById('osm_address');
        
        if (statusEl) statusEl.classList.add('hidden');
        addressDisplay.innerHTML = '<span class="text-gray-400 italic">Mengambil alamat dari peta...</span>';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.address) {
                    let districtName = data.address.city_district || data.address.suburb || data.address.town || data.address.county || '';
                    
                    if (data.display_name) {
                        addressDisplay.innerHTML = `<span class="font-medium text-gray-800">${data.display_name}</span>`;
                        osmAddressInput.value = data.display_name;
                    } else {
                        addressDisplay.innerHTML = '<span class="text-red-500">Alamat tidak dapat diurai secara detail.</span>';
                        osmAddressInput.value = '';
                    }

                    if (!districtName.toLowerCase().startsWith('kecamatan') && districtName) {
                        districtName = 'Kecamatan ' + districtName;
                    }

                    const select = document.getElementById('district_id');
                    let matchFound = false;
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].text.toLowerCase() === districtName.toLowerCase()) {
                            select.selectedIndex = i;
                            matchFound = true;
                            loadEventShippingCost();
                            break;
                        }
                    }

                    checkLocationRealtime(lat, lng, matchFound ? select.value : '', districtName, data.display_name);
                }
            })
            .catch(err => {
                addressDisplay.innerHTML = '<span class="text-red-500">Gagal mengambil alamat dari peta.</span>';
                osmAddressInput.value = '';
                checkLocationRealtime(lat, lng);
            });
    }

    function validateEventCheckout() {
        const method = document.querySelector('input[name="pickup_method"]:checked')?.value;
        const btn = document.getElementById('event-submit-btn');
        
        if (method === 'delivery') {
            const addressError = document.getElementById('address_error');
            const mapError = document.getElementById('map_error');
            if (addressError) addressError.classList.add('hidden');
            if (mapError) mapError.classList.add('hidden');
            
            btn.disabled = !isLocationValid;
        } else {
            const addressError = document.getElementById('address_error');
            const mapError = document.getElementById('map_error');
            if (addressError) addressError.classList.add('hidden');
            if (mapError) mapError.classList.add('hidden');
            const msgEl = document.getElementById('location-validation-msg');
            if (msgEl) msgEl.classList.add('hidden');
            btn.disabled = false;
        }
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
                setTimeout(() => {
                    eventMap.invalidateSize();
                    const pos = eventMarker ? eventMarker.getLatLng() : { lat: defaultLat, lng: defaultLng };
                    checkLocationRealtime(pos.lat, pos.lng);
                }, 100);
            }
        }
        validateEventCheckout();
    }

    function loadEventShippingCost() {
        const districtId = document.getElementById('district_id').value;
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
        validateEventCheckout();
    });

    let currentSnapToken = @json($existingOrder?->midtrans_snap_token ?? null);
    let currentRedirectUrl = @json($existingOrder ? route('customer.orders.show', $existingOrder) : null);

    document.getElementById('event-checkout-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('event-submit-btn');
        const originalText = btn.innerHTML;

        if (currentSnapToken) {
            let attempts = 0;
            while (typeof window.snap === 'undefined' && attempts < 20) {
                await new Promise(resolve => setTimeout(resolve, 200));
                attempts++;
            }

            if (typeof window.snap !== 'undefined') {
                window.snap.pay(currentSnapToken, {
                    onSuccess: function(result) {
                        window.location.href = currentRedirectUrl;
                    },
                    onPending: function(result) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal atau dibatalkan.');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    },
                    onClose: function() {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
            } else {
                alert('Sistem pembayaran Midtrans sedang dimuat atau terblokir.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
            return;
        }

        validateEventCheckout();
        if (btn.disabled) {
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>Memproses...</span></span>';

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                let errorMsg = data.message || 'Terjadi kesalahan saat memproses pesanan.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('\n');
                }
                alert(errorMsg);
                btn.disabled = false;
                btn.innerHTML = originalText;
                return;
            }

            if (data.success && data.snap_token) {
                currentSnapToken = data.snap_token;
                currentRedirectUrl = data.redirect_url;

                let attempts = 0;
                while (typeof window.snap === 'undefined' && attempts < 20) {
                    await new Promise(resolve => setTimeout(resolve, 200));
                    attempts++;
                }

                if (typeof window.snap !== 'undefined') {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            window.location.href = data.redirect_url;
                        },
                        onPending: function(result) {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal atau dibatalkan.');
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        },
                        onClose: function() {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    });
                } else {
                    alert('Sistem pembayaran Midtrans sedang dimuat atau terblokir. Silakan coba klik tombol Bayar Sekarang lagi.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } else {
                alert(data.message || 'Gagal membuat pesanan atau token pembayaran.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi saat memproses pesanan.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>
@push('styles')
<style>
@media (max-width: 768px) {
    #snap-midtrans {
        width: 100vw !important;
        height: 100vh !important;
        left: 0 !important;
        top: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
    }
}
</style>
@endpush
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush
@endsection
