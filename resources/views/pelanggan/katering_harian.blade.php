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

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h4 class="fw-bold mb-0">Daftar Jadwal Menu Harian</h4>
                    </div>
                    <div class="card-body p-4">
                        @forelse($jadwals as $jadwal)
                            <div class="jadwal-section mb-4 p-3 border rounded" data-jadwal-id="{{ $jadwal->id }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input jadwal-checkbox" type="checkbox" name="jadwal_ids[]" value="{{ $jadwal->id }}" id="jadwal_{{ $jadwal->id }}" style="transform: scale(1.5); margin-right: 10px; margin-top: 5px;">
                                        <label class="form-check-label" for="jadwal_{{ $jadwal->id }}">
                                            <h5 class="fw-bold text-dark mb-0">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</h5>
                                            <span class="text-primary fw-semibold fs-5">{{ $jadwal->menu->nama_menu }}</span>
                                        </label>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">Tersedia: {{ $jadwal->stok_tersisa }} Porsi</span>
                                    </div>
                                </div>
                                
                                <p class="text-dark small ms-4 ps-2 mb-3">
                                    Terdiri dari: {{ $jadwal->menu->deskripsi }}
                                    <br>
                                    <strong>Rp {{ number_format($jadwal->menu->harga, 0, ',', '.') }}</strong> / porsi dasar
                                </p>
                                
                                <div class="ms-4 ps-2 extras-container" style="display: none;">
                                    <h6 class="fw-bold small">Menu Tambahan (Opsional)</h6>
                                    @if($jadwal->menu->items->count() > 0)
                                        @foreach($jadwal->menu->items as $item)
                                            <div class="row mb-2 align-items-center">
                                                <div class="col-6">
                                                    <span class="text-dark small">{{ $item->nama }}</span>
                                                    <span class="text-dark small d-block">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group input-group-sm" style="max-width: 120px;">
                                                        <span class="input-group-text">Jml</span>
                                                        <input type="number" class="form-control" name="items_{{ $jadwal->id }}[{{ $item->id }}]" value="0" min="0">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted small">Tidak ada menu tambahan.</p>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <label class="form-label fw-semibold small">Jumlah Paket Utama</label>
                                        <input type="number" class="form-control form-control-sm" style="max-width: 120px;" name="porsi_{{ $jadwal->id }}" value="1" min="1">
                                    </div>
                                </div>
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

        // Tampilkan/sembunyikan extras saat checkbox jadwal diubah
        const jadwalCheckboxes = document.querySelectorAll('.jadwal-checkbox');
        jadwalCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const section = this.closest('.jadwal-section');
                const extrasContainer = section.querySelector('.extras-container');
                if (this.checked) {
                    extrasContainer.style.display = 'block';
                } else {
                    extrasContainer.style.display = 'none';
                    // Reset inputs
                    const numberInputs = extrasContainer.querySelectorAll('input[type="number"]');
                    numberInputs.forEach(i => i.value = (i.name.startsWith('porsi') ? '1' : '0'));
                }
            });
        });

        document.getElementById('formPilihMenu').addEventListener('submit', function(e) {
            if (radioAntar.checked && (!inputLat.value || !inputLng.value)) {
                e.preventDefault();
                alert('Silakan tentukan titik lokasi pengantaran pada peta.');
                return;
            }

            // Validasi minimal 1 jadwal dipilih
            const anyChecked = document.querySelectorAll('.jadwal-checkbox:checked').length > 0;
            if(!anyChecked) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 jadwal Katering Harian.');
                return;
            }
        });
    });
</script>
@endsection
