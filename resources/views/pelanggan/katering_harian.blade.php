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
                <input type="hidden" name="layanan_id" value="{{ $service->id }}">
                <input type="hidden" name="latitude" id="input_latitude" value="">
                <input type="hidden" name="longitude" id="input_longitude" value="">
                
                <!-- Pilihan Radio Button -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-black">Pilih Metode Pengambilan:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="radio_ambil" value="ambil_sendiri" checked>
                                <label class="form-check-label text-black" for="radio_ambil">
                                    Ambil Sendiri 
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="radio_antar" value="diantar_ke_tempat">
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

                <div class="mb-4">
                    <h4 class="fw-bold mb-4">Daftar Jadwal Menu Harian</h4>
                    
                    <div>
                        @forelse($jadwals as $jadwal)
                            <div class="jadwal-section mb-4" data-jadwal-id="{{ $jadwal->id }}">
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
                                            <div class="d-flex justify-content-between align-items-center mb-2 ms-2" style="max-width: 300px;">
                                                <div>
                                                    <span class="d-block">{{ $item->nama }}</span>
                                                    <span class="text-dark small">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm text-center" name="items_{{ $jadwal->id }}[{{ $item->id }}]" value="0" min="0" style="width: 70px;">
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted small ms-2 mb-0">Tidak ada menu tambahan.</p>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <span class="fw-semibold d-block mb-2">Jumlah</span>
                                    <input type="number" class="form-control form-control-sm text-center ms-2" name="porsi_{{ $jadwal->id }}" value="1" min="1" style="width: 100px;">
                                </div>
                                
                                <div class="mb-4">
                                    <span class="text-dark">Stok : {{ $jadwal->stok_tersisa }} Porsi</span>
                                </div>
                                
                                <hr class="border-secondary opacity-25 my-4">
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
                    var initLat = -0.03194;
                    var initLng = 109.325;
                    map = L.map('map').setView([initLat, initLng], 14);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    marker = L.marker([initLat, initLng])
                        .addTo(map)
                        .bindPopup("<b>Halo!</b><br>Pesanan akan diantar kesini.");

                    // Default input values
                    inputLat.value = initLat;
                    inputLng.value = initLng;

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

            // Validasi minimal ada input hidden jadwal
            const anyJadwal = document.querySelectorAll('input[name="jadwal_ids[]"]').length > 0;
            if(!anyJadwal) {
                e.preventDefault();
                alert('Belum ada jadwal Katering Harian yang tersedia.');
                return;
            }
        });
    });
</script>
@endsection
