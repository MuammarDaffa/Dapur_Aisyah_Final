@extends('layouts.app')
@section('title', 'Pilih Menu Harian')

@section('content')
<!-- memanggil CSS leaflet dari CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<div class="container mx-auto px-4 py-8 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <h2 class="fs-3 fw-bold text-dark mt-2 mb-1">Pilih Jadwal Katering Harian</h2>
                <p class="text-muted">Pilih jadwal pengantaran yang tersedia mulai besok ke depan.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pelanggan.harian.simpan') }}" method="POST" id="formPilihMenu">
                @csrf
                <input type="hidden" name="pesanan_id" value="{{ isset($pesanan) ? $pesanan->id : '' }}">
                <input type="hidden" name="layanan_id" value="{{ $service->id }}">
                <input type="hidden" name="latitude" id="input_latitude" value="{{ isset($pesanan) ? $pesanan->latitude : '' }}">
                <input type="hidden" name="longitude" id="input_longitude" value="{{ isset($pesanan) ? $pesanan->longitude : '' }}">
                
                <!-- Pilihan Radio Button -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-black">Pilih Metode Pengambilan:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="radio_ambil" value="ambil_sendiri" {{ (!isset($pesanan) || $pesanan->metode_pengambilan === 'ambil_sendiri') ? 'checked' : '' }}>
                                <label class="form-check-label text-black" for="radio_ambil">
                                    Ambil Sendiri 
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="radio_antar" value="diantar_ke_tempat" {{ (isset($pesanan) && $pesanan->metode_pengambilan === 'diantar_ke_tempat') ? 'checked' : '' }}>
                                <label class="form-check-label text-black" for="radio_antar">
                                    Di Antar ke Lokasi  
                                </label>
                            </div>
                        </div>

                        <!-- Wadah Peta -->
                        <div id="wadah_peta" style="display: none;">
                            <hr class="my-4">
                            <h5 class="fw-bold mb-3">Tentukan Lokasi Pengantaran</h5>
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i>*Silakan klik pada peta untuk memilih lokasi pengantaran</i></span>
                            </div>
                            <div id="map" class="w-100 rounded border border-2 shadow-sm" style="height: 50vh; min-height: 400px; z-index: 1;"></div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h4 class="fw-bold mb-0">Daftar Jadwal Menu Harian</h4>
                    </div>
                    <div class="card-body p-4">
                        @forelse($jadwals as $jadwal)
                            @php
                                $porsiValue = 0;
                                $detailLama = null;
                                if (isset($pesanan)) {
                                    $tanggalJadwalStr = \Carbon\Carbon::parse($jadwal->tanggal)->format('Y-m-d');
                                    $detailLama = $pesanan->detailPesanans->first(function ($detail) use ($tanggalJadwalStr) {
                                        return \Carbon\Carbon::parse($detail->tanggal_pengiriman)->format('Y-m-d') === $tanggalJadwalStr;
                                    });
                                    if ($detailLama) {
                                        $porsiValue = $detailLama->porsi;
                                    } else {
                                        $porsiValue = 0; 
                                    }
                                }
                            @endphp
                            <div class="jadwal-section" data-jadwal-id="{{ $jadwal->id }}">
                                <input type="hidden" name="jadwal_ids[]" value="{{ $jadwal->id }}">
                                
                                <h5 class="fw-bold text-dark mb-3">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</h5>
                                
                                <div class="mb-3">
                                    <span class="fw-semibold d-block">Menu :</span>
                                    <span class="text-dark">{{ $jadwal->menu->nama_menu }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="fw-semibold d-block">Terdiri dari :</span>
                                    <ul class="list-unstyled mb-0 ms-2">
                                        @if($jadwal->menu->items->count() > 0)
                                            @foreach($jadwal->menu->items as $item)
                                                <li>- {{ $item->nama }}</li>
                                            @endforeach
                                        @else
                                            <li>- {{ $jadwal->menu->deskripsi }}</li>
                                        @endif
                                    </ul>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="fw-semibold d-block mb-2">Tambahan :</span>
                                    @if($jadwal->menu->items->count() > 0)
                                        @foreach($jadwal->menu->items as $item)
                                            @php
                                                $itemCount = 0;
                                                if ($detailLama && $detailLama->menuItems) {
                                                    $itemCount = $detailLama->menuItems->where('id', $item->id)->count();
                                                }
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center mb-2 ms-2" style="max-width: 300px;">
                                                <div>
                                                    <span class="d-block">{{ $item->nama }}</span>
                                                    <span class="text-dark small">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm text-center" name="items_{{ $jadwal->id }}[{{ $item->id }}]" value="{{ $itemCount }}" min="0" style="width: 70px;">
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted small ms-2 mb-0">Tidak ada menu tambahan.</p>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <span class="fw-semibold d-block mb-2">Jumlah</span>
                                    <input type="number" class="form-control form-control-sm text-center ms-2" name="porsi_{{ $jadwal->id }}" value="{{ $porsiValue }}" min="0" style="width: 100px;">
                                </div>
                                
                                <div class="mb-2">
                                    <span class="text-dark">Stok : {{ $jadwal->stok_tersisa }} Porsi</span>
                                </div>
                                
                                @if(!$loop->last)
                                    <hr class="border-secondary opacity-25 my-4">
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <p class="text-dark mb-0">Belum ada jadwal harian yang tersedia mulai besok ke depan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5 pb-5">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="btnSimpan">
                        Buat Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script Leaflet (Sama dengan katering acara) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radioAmbil = document.getElementById('radio_ambil');
        const radioAntar = document.getElementById('radio_antar');
        const wadahPeta = document.getElementById('wadah_peta');
        const inputLat = document.getElementById('input_latitude');
        const inputLng = document.getElementById('input_longitude');
        
        let map;
        let marker;

        function togglePeta() {
            if (radioAntar.checked) {
                wadahPeta.style.display = 'block';
                inputLat.setAttribute('required', 'required');
                inputLng.setAttribute('required', 'required');
                
                if (!map) {
                    var initLat = {{ isset($pesanan) && $pesanan->latitude ? $pesanan->latitude : -0.03194 }};
                    var initLng = {{ isset($pesanan) && $pesanan->longitude ? $pesanan->longitude : 109.325 }};
                    map = L.map('map').setView([initLat, initLng], 14);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    marker = L.marker([initLat, initLng])
                        .addTo(map)
                        .bindPopup("<b>Halo!</b><br>Pesanan akan diantar kesini.");

                    // Default input values if empty
                    if (!inputLat.value) {
                        inputLat.value = initLat;
                        inputLng.value = initLng;
                    }

                    map.on('click', function(e) {
                        const lat = e.latlng.lat;
                        const lng = e.latlng.lng;
                        
                        inputLat.value = lat;
                        inputLng.value = lng;

                        marker.setLatLng(e.latlng);
                    });
                }
                setTimeout(() => map.invalidateSize(), 100);
            } else {
                wadahPeta.style.display = 'none';
                inputLat.removeAttribute('required');
                inputLng.removeAttribute('required');
                inputLat.value = '';
                inputLng.value = '';
            }
        }

        radioAmbil.addEventListener('change', togglePeta);
        radioAntar.addEventListener('change', togglePeta);
        togglePeta();

        document.getElementById('formPilihMenu').addEventListener('submit', function(e) {
            if (radioAntar.checked && (!inputLat.value || !inputLng.value)) {
                e.preventDefault();
                alert('Silakan tentukan titik lokasi pengantaran pada peta.');
                return;
            }

            // Validasi minimal ada input hidden jadwal dan total porsi > 0
            let totalPorsi = 0;
            let hasTambahanWithoutPorsi = false;
            let firstInvalidDate = "";

            const jadwalSections = document.querySelectorAll('.jadwal-section');
            jadwalSections.forEach(section => {
                const jadwalId = section.getAttribute('data-jadwal-id');
                const porsiInput = section.querySelector(`input[name="porsi_${jadwalId}"]`);
                const porsi = parseInt(porsiInput.value) || 0;
                
                totalPorsi += porsi;
                
                const itemsInputs = section.querySelectorAll(`input[name^="items_${jadwalId}"]`);
                let totalItems = 0;
                itemsInputs.forEach(input => {
                    totalItems += parseInt(input.value) || 0;
                });
                
                if (totalItems > 0 && porsi === 0) {
                    hasTambahanWithoutPorsi = true;
                    const dateEl = section.querySelector('h5.text-dark');
                    if (dateEl && !firstInvalidDate) {
                        firstInvalidDate = dateEl.innerText.trim();
                    }
                }
            });

            if (hasTambahanWithoutPorsi) {
                e.preventDefault();
                alert(`Anda memesan menu tambahan pada jadwal ${firstInvalidDate || 'tertentu'}, namun belum mengisi jumlah porsi utamanya. Silakan isi jumlah porsi utama minimal 1.`);
                return;
            }
            
            if (totalPorsi === 0) {
                e.preventDefault();
                alert('Silakan masukkan jumlah porsi utama (minimal 1) pada jadwal katering yang Anda inginkan.');
                return;
            }
        });
    });
</script>
@endsection
