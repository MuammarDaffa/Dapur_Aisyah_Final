@extends('layouts.app')
@section('title', 'Pilih Metode Pengambilan Harian')

@push('styles')
<!-- memanggil CSS leaflet dari CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <h2 class="fs-3 fw-bold text-dark mt-2 mb-1">Pilih Metode Pengambilan</h2>
                <p class="text-muted">Silakan tentukan bagaimana Anda ingin pesanan katering harian Anda dikirimkan.</p>
            </div>

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

            <form action="{{ route('pelanggan.harian.simpan_lokasi') }}" method="POST" id="formPilihLokasi">
                @csrf
                <input type="hidden" name="latitude" id="input_latitude" value="">
                <input type="hidden" name="longitude" id="input_longitude" value="">
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-black">Metode Pengambilan:</label>
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
                            <!-- Alert Error Jika Lokasi Ditolak -->
<div id="alert_lokasi" class="alert alert-danger mt-3" style="display: none;">
    <strong>Maaf!</strong> Lokasi yang ditandai berada di luar jangkauan antar kami (Hanya melayani Pontianak Barat, Kota, Selatan, dan Tenggara).
</div>

<!-- Menampilkan Hasil Peta -->
<div class="mt-3 p-3 bg-light border rounded">
    <label class="form-label fw-bold text-secondary">Alamat Lengkap (Satelit):</label>
    <textarea name="alamat_satelit" id="alamat_satelit" class="form-control mb-3" rows="2" readonly placeholder="Alamat otomatis akan muncul di sini..."></textarea>
    
    <label class="form-label fw-bold text-secondary">Nomor Rumah <span class="text-danger">*</span></label>
    <input type="text" name="nomor_rumah" id="nomor_rumah" class="form-control" placeholder="Contoh: No. 12A / Blok C4" required>
</div>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5 pb-5">
                   <button type="submit" id="btn_lanjut" class="btn btn-primary px-4 py-2 fw-bold w-100">
    Lanjut ke Menu Harian
</button>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
                document.getElementById('nomor_rumah').setAttribute('required', 'required');
                
                // Tambahkan sedikit jeda agar DOM me-render container dengan benar sebelum peta dipanggil
                setTimeout(() => {
                    try {
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

                            map.on('click', function(e) {
                                let lat = e.latlng.lat;
                                let lng = e.latlng.lng;

                                // 1. Pindahkan marker dan simpan koordinat ke input hidden
                                if (marker) {
                                    marker.setLatLng(e.latlng);
                                } else {
                                    marker = L.marker(e.latlng).addTo(map);
                                }
                                document.getElementById('input_latitude').value = lat;
                                document.getElementById('input_longitude').value = lng;
                                
                                // Matikan sementara tombol lanjut sembari menunggu loading API
                                document.getElementById('btn_lanjut').disabled = true;
                                document.getElementById('alamat_satelit').value = "Sedang melacak lokasi...";
                                document.getElementById('alert_lokasi').style.display = 'none';

                                // 2. Memanggil API Nominatim (OpenStreetMap) untuk Reverse Geocoding
                                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        let fullAddress = data.display_name || "Alamat tidak ditemukan";

                                        // 3. Logika Validasi Wilayah (Internal)
                                        let allowedKecamatan = ["Pontianak Barat", "Pontianak Kota", "Pontianak Selatan", "Pontianak Tenggara"];
                                        let isAllowed = false;

                                        allowedKecamatan.forEach(function(kecamatan) {
                                            if (fullAddress.includes(kecamatan)) {
                                                isAllowed = true;
                                            }
                                        });

                                        document.getElementById('alamat_satelit').value = fullAddress;

                                        if (isAllowed) {
                                            document.getElementById('btn_lanjut').disabled = false;
                                            document.getElementById('alert_lokasi').style.display = 'none';
                                        } else {
                                            document.getElementById('btn_lanjut').disabled = true;
                                            document.getElementById('alert_lokasi').style.display = 'block';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error memuat alamat:', error);
                                        document.getElementById('alamat_satelit').value = "Gagal memuat alamat. Pastikan ada koneksi internet.";
                                    });
                            });
                        }
                        if (map) {
                            map.invalidateSize(); // Segarkan tampilan Leaflet
                        }
                    } catch (err) {
                        console.error("Gagal menginisialisasi peta Leaflet:", err);
                        alert("Gagal memuat peta. Silakan periksa koneksi internet Anda atau muat ulang halaman.");
                    }
                }, 400); // 400ms delay agar lebih aman
            } else {
                wadahPeta.style.display = 'none';
                inputLat.removeAttribute('required');
                inputLng.removeAttribute('required');
                inputLat.value = '';
                inputLng.value = '';
                
                let inputNomor = document.getElementById('nomor_rumah');
                if (inputNomor) {
                    inputNomor.removeAttribute('required');
                    inputNomor.value = '';
                }
                document.getElementById('btn_lanjut').disabled = false;
            }
        }

        radioAmbil.addEventListener('change', togglePeta);
        radioAntar.addEventListener('change', togglePeta);
        
        // Panggil fungsi toggle saat halaman pertama kali dimuat
        togglePeta();

        document.getElementById('formPilihLokasi').addEventListener('submit', function(e) {
            if (radioAntar.checked && (!inputLat.value || !inputLng.value)) {
                e.preventDefault();
                alert('Silakan tentukan titik lokasi pengantaran pada peta.');
            }
        });
    });
</script>
@endpush
