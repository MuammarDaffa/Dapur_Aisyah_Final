@extends('layouts.app')
@section('title', 'Checkout')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>#map{height:250px;border-radius:12px;z-index:0;}</style>
@endpush
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 <span class="text-orange-500">Checkout</span></h2>
    <form action="{{ route('customer.checkout.store') }}" method="POST" id="checkout-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Info -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Detail Pengiriman</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengiriman</label>
                            <input type="hidden" name="order_date" value="{{ $orderDate }}">
                            <p class="text-sm font-medium text-gray-700 bg-orange-50 p-3 rounded-lg border border-orange-200">Pengiriman mengikuti jadwal yang tertera pada menu yang dipilih.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pengambilan *</label>
                            <select name="pickup_method" id="pickup_method" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" onchange="toggleDelivery()">
                                <option value="pickup" {{ old('pickup_method') == 'pickup' ? 'selected' : '' }}>Ambil di Tempat (Pick Up)</option>
                                <option value="delivery" {{ old('pickup_method') == 'delivery' ? 'selected' : '' }}>Delivery (Antar ke Alamat)</option>
                            </select>
                        </div>
                        <div id="delivery-fields" class="{{ old('pickup_method') == 'delivery' ? '' : 'hidden' }} space-y-4">
                                <select name="district_id" id="district_id" class="hidden">
                                    <option value="">-- Pilih dari peta di bawah --</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" data-lat="{{ $district->latitude ?? '' }}" data-lng="{{ $district->longitude ?? '' }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            
                            

                         
                            <!-- Peta Lokasi (Leaflet.js) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tandai Lokasi Pengiriman di Peta</label>
                                <!-- <p class="text-xs text-gray-500 mb-2">Klik pada peta untuk menentukan titik lokasi pengiriman yang tepat.</p> -->
                                <div id="map"></div>
                                <p id="location-validation-msg" class="text-sm font-medium mt-2 hidden"></p>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                <div class="flex justify-between items-center mt-2 hidden">
                                    <p class="text-xs text-gray-400" id="coord-display">Koordinat belum dipilih</p>
                                </div>
                                <span id="geocode-status" class="hidden"></span>
                                <p id="map_error" class="text-sm text-red-500 mt-2 font-medium hidden">⚠️ Anda wajib menandai lokasi pengiriman di peta.</p>
                                @error('district_id')
                                    <p class="text-sm text-red-500 mt-2 font-medium">⚠️ Anda harus menandai lokasi pengiriman di peta dengan benar.</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Berdasarkan Peta</label>
                                <div id="osm-address-display" class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600 min-h-[42px]">
                                    Lokasi belum ditandai di peta.
                                </div>
                                <input type="hidden" name="osm_address" id="osm_address">
                            </div>
                            <!-- detail field -->
                            <!-- <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Detail Patokan/Blok/No. Rumah (Opsional)</label>
                                <textarea name="address_detail" id="address_detail_input" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400 @error('address_detail') border-red-400 @enderror" placeholder="Contoh: Rumah cat putih pagar hitam, dekat masjid..." oninput="validateCheckout()">{{ old('address_detail') }}</textarea>
                                <p id="address_error" class="text-sm text-red-500 mt-1 hidden"></p>
                                @error('address_detail') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div> -->
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pembayaran</label>
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800 font-medium">Transfer Bank</p>
                                <!-- <p class="text-xs text-blue-600">Semua pembayaran dilakukan melalui Midtrans Payment Gateway.</p> -->
                            </div>
                            <input type="hidden" name="payment_method" value="transfer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                    <div class="divide-y divide-gray-100 mb-4 max-h-64 overflow-y-auto">
                        @foreach($groupedCarts as $groupKey => $groupItems)
                            @php
                                $isEvent = $groupItems->first()->cart_group_id !== null;
                                $pkgItem = $isEvent ? $groupItems->firstWhere('item_type', 'package') : null;
                            @endphp

                            @if($isEvent)
                                {{-- Event group summary --}}
                                @if($pkgItem && $pkgItem->cateringPackage)
                                <div class="py-3">
                                    <p class="font-medium text-gray-900">📋 {{ $pkgItem->cateringPackage->name }}</p>
                                    <p class="text-xs text-orange-600 font-medium">Rp {{ number_format($pkgItem->cateringPackage->price, 0, ',', '.') }}</p>
                                </div>
                                @endif
                                @foreach($groupItems->where('item_type', '!=', 'package') as $cart)
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
                            @else
                                {{-- Regular items --}}
                                @foreach($groupItems as $cart)
                                <div class="py-3 flex justify-between text-sm">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $cart->product->name ?? ($cart->customOption->name ?? 'Item') }}</p>
                                        <p class="text-xs text-gray-500">× {{ $cart->quantity }}</p>
                                    </div>
                                    <p class="font-medium text-gray-900">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</p>
                                </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Ongkos Kirim</span>
                            <span class="font-medium" id="shipping-display">Gratis</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-orange-600" id="total-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <button type="submit" id="submit-btn" class="w-full mt-4 px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
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
// === Peta Leaflet & Validasi Area ===
let map, marker;
let isLocationValid = false;
const defaultLat = -0.0263; // Pontianak
const defaultLng = 109.3425;

function initMap() {
    const pontianakBounds = [
        [-0.15, 109.20], // South West
        [0.08, 109.45]   // North East
    ];

    const initialLat = parseFloat(document.getElementById('latitude').value) || defaultLat;
    const initialLng = parseFloat(document.getElementById('longitude').value) || defaultLng;

    map = L.map('map', {
        maxBounds: pontianakBounds,
        maxBoundsViscosity: 1.0,
        minZoom: 11
    }).setView([initialLat, initialLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
        bounds: pontianakBounds
    }).addTo(map);

    marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

    marker.on('dragend', function (e) {
        const pos = marker.getLatLng();
        setCoordinates(pos.lat, pos.lng);
        checkLocationRealtime(pos.lat, pos.lng);
        reverseGeocode(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        setCoordinates(e.latlng.lat, e.latlng.lng);
        checkLocationRealtime(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    setTimeout(() => map.invalidateSize(), 300);

    setCoordinates(initialLat, initialLng);
    checkLocationRealtime(initialLat, initialLng);
    reverseGeocode(initialLat, initialLng);
}

function setCoordinates(lat, lng) {
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
                            loadShippingCost(data.district_id);
                            break;
                        }
                    }
                }
            } else {
                isLocationValid = false;
                msgEl.textContent = 'Lokasi berada di luar wilayah Pontianak.';
                msgEl.className = 'text-sm font-medium mt-2 text-red-600 block';
                document.getElementById('district_id').value = '';
                loadShippingCost('');
            }
            validateCheckout();
        })
        .catch(err => {
            console.error('Validation error:', err);
            isLocationValid = false;
            msgEl.textContent = 'Lokasi berada di luar wilayah Pontianak.';
            msgEl.className = 'text-sm font-medium mt-2 text-red-600 block';
            validateCheckout();
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
                        loadShippingCost(select.options[i].value);
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

// === Validate Form ===
function validateCheckout() {
    const method = document.getElementById('pickup_method').value;
    const btn = document.getElementById('submit-btn');
    
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

// === Toggle Delivery ===
function toggleDelivery() {
    const method = document.getElementById('pickup_method').value;
    document.getElementById('delivery-fields').classList.toggle('hidden', method !== 'delivery');
    if (method !== 'delivery') {
        document.getElementById('shipping-display').textContent = 'Gratis';
        document.getElementById('total-display').textContent = 'Rp {{ number_format($subtotal, 0, ",", ".") }}';
    } else {
        if (!map) {
            setTimeout(initMap, 100);
        } else {
            setTimeout(() => {
                map.invalidateSize();
                const pos = marker ? marker.getLatLng() : { lat: defaultLat, lng: defaultLng };
                checkLocationRealtime(pos.lat, pos.lng);
            }, 100);
        }
    }
    validateCheckout();
}

// === Load Shipping ===
function loadShippingCost(districtId) {
    if (!districtId) {
        document.getElementById('shipping-display').textContent = 'Rp 0';
        document.getElementById('total-display').textContent = 'Rp {{ number_format($subtotal, 0, ",", ".") }}';
        return;
    }

    fetch(`/api/shipping-cost/${districtId}`)
        .then(r => r.json())
        .then(data => {
            const cost = data.cost;
            document.getElementById('shipping-display').textContent = 'Rp ' + Number(cost).toLocaleString('id-ID');
            const total = {{ $subtotal }} + cost;
            document.getElementById('total-display').textContent = 'Rp ' + Number(total).toLocaleString('id-ID');
        });
}

// Init map jika delivery sudah dipilih (misal old value)
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('pickup_method').value === 'delivery') {
        setTimeout(initMap, 200);
    }
    validateCheckout();
});

document.getElementById('checkout-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    validateCheckout();
    const btn = document.getElementById('submit-btn');
    if (btn.disabled) {
        return;
    }

    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '⏳ Memproses...';

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

        if (data.success && data.snap_token && window.snap) {
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    window.location.href = data.redirect_url;
                },
                onPending: function(result) {
                    window.location.href = data.redirect_url;
                },
                onError: function(result) {
                    alert('Pembayaran gagal atau dibatalkan.');
                    window.location.href = data.redirect_url;
                },
                onClose: function() {
                    window.location.href = data.redirect_url;
                }
            });
        } else {
            window.location.href = data.redirect_url || '{{ route("customer.orders") }}';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi saat memproses pesanan.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});
</script>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush
@endsection
