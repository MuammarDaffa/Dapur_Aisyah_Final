@extends('layouts.app')
@section('title', 'Lanjut Ke Pembayaran')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>#map{height:250px;border-radius:12px;z-index:0;}</style>
@endpush
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="fs-3 fw-bold text-secondary mb-6 d-flex align-items-center">
        <svg class="w-7 h-7 text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        <span>Lanjut Ke Pembayaran</span>
    </h2>
    <form action="{{ route('pelanggan.checkout.store') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row row-cols-1 lg:row-cols-3 g-3">
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Info -->
                <div class="card shadow-sm mb-4 p-4">
                    <h3 class="fw-bold text-secondary mb-4">Detail Pengiriman</h3>
                    <div class="d-flex flex-column gap-3">
                        <div class="mb-3">
            <label class="form-label fw-bold">Tanggal Pengiriman</label>
                            <input type="hidden" name="tanggal_pesanan" value="{{ $orderDate }}">
                            <p class="fs-6 fw-medium text-secondary bg-primary text-white p-3 rounded border border border-primary">Pengiriman mengikuti jadwal yang tertera pada menu yang dipilih.</p>
                        </div>
                        <div class="mb-3">
            <label class="form-label fw-bold">Metode Pengambilan *</label>
                            <select name="metode_pengambilan" id="metode_pengambilan" required class="form-select w-100 px-4 py-2 rounded border border border-secondary focus:border border-primary" onchange="toggleDelivery()">
                                <option value="pickup" {{ old('metode_pengambilan') == 'pickup' ? 'selected' : '' }}>Ambil di Tempat (Pick Up)</option>
                                <option value="delivery" {{ old('metode_pengambilan') == 'delivery' ? 'selected' : '' }}>Delivery (Antar ke Alamat)</option>
                            </select>
                        </div>
                        <div id="delivery-fields" class="{{ old('metode_pengambilan') == 'delivery' ? '' : 'd-none' }} d-flex flex-column gap-3">
                                <select name="kecamatan_id" id="kecamatan_id" class="form-select d-none">
                                    <option value="">-- Pilih dari peta di bawah --</option>
                                    @foreach($kecamatan as $kecamatan)
                                        <option value="{{ $kecamatan->id }}" data-lat="{{ $kecamatan->latitude ?? '' }}" data-lng="{{ $kecamatan->longitude ?? '' }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->name }}</option>
                                    @endforeach
                                </select>
                            
                            

                         
                            <!-- Peta Lokasi (Leaflet.js) -->
                            <div class="mb-3">
            <label class="form-label fw-bold">Tandai Lokasi Pengiriman di Peta</label>
                                <!-- <p class="small text-secondary mb-2">Klik pada peta untuk menentukan titik lokasi pengiriman yang tepat.</p> -->
                                <div id="map"></div>
                                <p id="location-validation-msg" class="fs-6 fw-medium mt-2 d-none"></p>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                <div class="d-flex justify-content-between align-items-center mt-2 d-none">
                                    <p class="small text-secondary" id="coord-display">Koordinat belum dipilih</p>
                                </div>
                                <span id="geocode-status" class="d-none"></span>
                                <p id="map_error" class="fs-6 text-danger mt-2 fw-medium d-none">
                                    <svg style="width: 16px; height: 16px;" class="d-inline-block text-danger me-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Anda wajib menandai lokasi pengiriman di peta.
                                </p>
                                @error('kecamatan_id')
                                    <p class="fs-6 text-danger mt-2 fw-medium">
                                        <svg style="width: 16px; height: 16px;" class="d-inline-block text-danger me-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Anda harus menandai lokasi pengiriman di peta dengan benar.
                                    </p>
                                @enderror
                            </div>
                            <div class="mb-3">
            <label class="form-label fw-bold">Alamat Berdasarkan Peta</label>
                                <div id="osm-address-display" class="p-3 bg-light border border border-secondary rounded fs-6 text-secondary min-h-[42px]">
                                    Lokasi belum ditandai di peta.
                                </div>
                                <input type="hidden" name="osm_address" id="osm_address">
                            </div>
                            <!-- detail field -->
                            <!-- <div class="mb-3">
            <label class="form-label fw-bold">Detail Patokan/Blok/No. Rumah (Opsional)</label>
                                <textarea name="detail_alamat" id="address_detail_input" rows="2" class="form-control w-100 px-4 py-2 rounded border border border-secondary focus:border border-primary @error('detail_alamat') border-red-400 @enderror" placeholder="Contoh: Rumah cat putih pagar hitam, dekat masjid..." oninput="validateCheckout()">{{ old('detail_alamat') }}</textarea>
                                <p id="address_error" class="fs-6 text-danger mt-1 d-none"></p>
                                @error('detail_alamat') <p class="fs-6 text-danger mt-1">{{ $message }}</p> @enderror
                            </div> -->
                        </div>
                        <div class="mb-3">
            <label class="form-label fw-bold">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="2" class="form-control w-100 px-4 py-2 rounded border border border-secondary focus:border border-primary" placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                        </div>
                        <div class="mb-3">
            <label class="form-label fw-bold">Pembayaran</label>
                            <div class="p-3 bg-info text-white border border-blue-200 rounded">
                                <p class="fs-6 text-info fw-medium">Transfer Bank</p>
                                <!-- <p class="small text-info">Semua pembayaran dilakukan melalui Midtrans Payment Gateway.</p> -->
                            </div>
                            <input type="hidden" name="metode_pembayaran" value="transfer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pesanan Summary -->
            <div>
                <div class="card shadow-sm mb-4 p-4">
                    <h3 class="fw-bold text-secondary mb-4">Ringkasan Pesanan</h3>
                    <div style="height: 256px;" class="divide-y divide-gray-100 mb-4 max- overflow-y-auto">
                        @foreach($groupedCarts as $groupKey => $groupItems)
                            @php
                                $isEvent = $groupItems->first()->cart_group_id !== null;
                                $pkgItem = $isEvent ? $groupItems->firstWhere('item_type', 'package') : null;
                            @endphp

                            @if($isEvent)
                                {{-- Event group summary --}}
                                @if($pkgItem && $pkgItem->paketKatering)
                                <div class="py-3">
                                    <p class="fw-medium text-secondary d-flex align-items-center">
                                        <svg style="width: 16px; height: 16px;" class="text-primary me-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <span>{{ $pkgItem->paketKatering->name }}</span>
                                    </p>
                                    <p class="small text-primary fw-medium">Rp {{ number_format($pkgItem->paketKatering->harga, 0, ',', '.') }}</p>
                                </div>
                                @endif
                                @foreach($groupItems->where('item_type', '!=', 'package') as $keranjang)
                                <div class="py-2 d-flex justify-content-between fs-6">
                                    <div>
                                        <p class="text-secondary">{{ $keranjang->opsiKustom->name ?? 'Item' }}</p>
                                        <p class="small text-secondary">× {{ $keranjang->jumlah }}
                                            @if($keranjang->item_type === 'package_item')
                                                <span class="text-success">(termasuk)</span>
                                            @endif
                                        </p>
                                    </div>
                                    <p class="fw-medium {{ $keranjang->item_type === 'package_item' ? 'text-success' : 'text-secondary' }}">
                                        {{ $keranjang->item_type === 'package_item' ? 'Rp 0' : 'Rp ' . number_format($keranjang->subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                                @endforeach
                            @else
                                {{-- Regular items --}}
                                @foreach($groupItems as $keranjang)
                                <div class="py-3 d-flex justify-content-between fs-6">
                                    <div>
                                        <p class="fw-medium text-secondary">{{ $keranjang->produk->name ?? ($keranjang->opsiKustom->name ?? 'Item') }}</p>
                                        <p class="small text-secondary">× {{ $keranjang->jumlah }}</p>
                                    </div>
                                    <p class="fw-medium text-secondary">Rp {{ number_format($keranjang->subtotal, 0, ',', '.') }}</p>
                                </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                    <div class="border-t border border-secondary pt-4 d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between fs-6">
                            <span class="text-secondary">Subtotal</span>
                            <span class="fw-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="d-flex justify-content-between fs-5 fw-bold pt-2 border-t border border-secondary">
                            <span>Total</span>
                            <span class="text-primary" id="total-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <button type="submit" id="submit-btn" class="w-100 mt-4 px-6 py-3.5 text-white fw-bold rounded hover:shadow disabled:opacity-50 disabled:cursor-not-allowed">
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

    fetch(`/api/validate-location?latitude=${lat}&longitude=${lng}&kecamatan_id=${districtId}&district_name=${encodeURIComponent(districtName)}&address=${encodeURIComponent(address)}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.is_in_pontianak) {
                isLocationValid = true;
                msgEl.textContent = 'Lokasi berada di wilayah Pontianak.';
                msgEl.className = 'text-sm font-medium mt-2 text-green-600 block';
                if (data.kecamatan_id) {
                    const select = document.getElementById('kecamatan_id');
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value == data.kecamatan_id) {
                            select.selectedIndex = i;
                            break;
                        }
                    }
                }
            } else {
                isLocationValid = false;
                msgEl.textContent = 'Lokasi berada di luar wilayah Pontianak.';
                msgEl.className = 'text-sm font-medium mt-2 text-red-600 block';
                document.getElementById('kecamatan_id').value = '';
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
    addressDisplay.innerHTML = '<span class="text-secondary italic">Mengambil alamat dari peta...</span>';

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.address) {
                let districtName = data.address.city_district || data.address.suburb || data.address.town || data.address.county || '';
                
                if (data.display_name) {
                    addressDisplay.innerHTML = `<span class="fw-medium text-secondary">${data.display_name}</span>`;
                    osmAddressInput.value = data.display_name;
                } else {
                    addressDisplay.innerHTML = '<span class="text-danger">Alamat tidak dapat diurai secara detail.</span>';
                    osmAddressInput.value = '';
                }

                if (!districtName.toLowerCase().startsWith('kecamatan') && districtName) {
                    districtName = 'Kecamatan ' + districtName;
                }

                const select = document.getElementById('kecamatan_id');
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
            addressDisplay.innerHTML = '<span class="text-danger">Gagal mengambil alamat dari peta.</span>';
            osmAddressInput.value = '';
            checkLocationRealtime(lat, lng);
        });
}

// === Validate Form ===
function validateCheckout() {
    const method = document.getElementById('metode_pengambilan').value;
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
    const method = document.getElementById('metode_pengambilan').value;
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


// Init map jika delivery sudah dipilih (misal old value)
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('metode_pengambilan').value === 'delivery') {
        setTimeout(initMap, 200);
    }
    validateCheckout();
});

let currentSnapToken = @json($existingOrder?->midtrans_snap_token ?? null);
let currentRedirectUrl = @json($existingOrder ? route('pelanggan.pesanan.show', $existingOrder) : null);

document.getElementById('checkout-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
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

    validateCheckout();
    if (btn.disabled) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = 'Memproses...';

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
