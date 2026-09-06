@extends('layouts.app')
@section('title', 'Pilih Tanggal dan Lokasi Acara')

@push('styles')
<!-- memanggil CSS leaflet dari CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .page-header {
        position: relative;
        padding: 80px 0 40px;
        background-color: var(--bg-cream);
        overflow: hidden;
    }
    
    @import url('https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&display=swap');
    .section-title p {
        margin: 0;
        font-size: clamp(48px, 6vw, 64px);
        font-weight: 700;
        font-family: 'Amatic SC', sans-serif;
        color: #37373f;
    }
    .section-title p span {
        color: #ce1212;
    }

    
    .blob-header {
        position: absolute;
        top: -50px; left: -10%;
        width: 400px; height: 400px;
        background: var(--forest-green);
        opacity: 0.05;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        animation: morph 15s ease-in-out infinite alternate;
        z-index: 0;
    }

    .bento-box {
        background: white;
        border-radius: var(--bento-radius);
        box-shadow: var(--soft-shadow);
        padding: 40px;
        position: relative;
        overflow: hidden;
    }

    .method-card {
        border: 2px solid #E2E8F0;
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        background: var(--bg-cream);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 16px;
    }

    .method-card:hover {
        border-color: rgba(44, 74, 59, 0.3);
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }

    .method-card.selected {
        border-color: var(--primary-terracotta);
        background: white;
        box-shadow: 0 15px 35px rgba(224, 93, 54, 0.1);
    }

    .method-icon-wrapper {
        width: 80px; height: 80px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        font-size: 2rem;
        transition: all 0.3s ease;
    }

    .method-card.selected .method-icon-wrapper {
        background: var(--primary-terracotta);
        color: white !important;
    }

    .method-card.selected .method-icon-wrapper i {
        color: white !important;
    }

    #map {
        border-radius: 24px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
        border: 4px solid white;
    }
    
    .btn-fixed-bottom {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        box-shadow: 0 15px 35px rgba(224, 93, 54, 0.4);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="blob-header"></div>
    <div class="container position-relative z-1">
        <div class="text-center mb-4 section-title">
            <p>Tanggal & Lokasi <span>Acara</span></p>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-4 pb-5">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                    <ul class="mb-0 fw-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pelanggan.acara.simpan_lokasi') }}" method="POST" id="formPilihLokasi">
                @csrf
                <input type="hidden" name="latitude" id="input_latitude" value="">
                <input type="hidden" name="longitude" id="input_longitude" value="">
                
                <div class="bento-box mb-5">
                    <!-- Input Datepicker -->
                    <div class="mb-5 border-bottom pb-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-regular fa-calendar-check text-dark fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Tanggal Acara</h4>
                                <span class="text-secondary small">Pemesanan minimal H-5 sebelum tanggal acara.</span>
                            </div>
                        </div>
                        
                        @php
                            $minDate = \Carbon\Carbon::now()->addDays(5)->format('Y-m-d');
                        @endphp
                        <div class="input-group input-group-lg w-100 bg-white shadow-sm rounded-pill p-2" style="border: 2px solid #E2E8F0;">
                            <span class="input-group-text bg-transparent border-0 pe-1 ps-4"><i class="fa-regular fa-calendar text-dark fs-5"></i></span>
                            <input type="date" name="tanggal_acara" id="tanggal_acara" class="form-control border-0 shadow-none py-2 fs-5 fw-bold text-dark" required min="{{ $minDate }}" style="background: transparent;">
                        </div>
                    </div>

                    <h3 class="fw-bold text-dark mb-4 text-center fs-4">Pilih Opsi Pengiriman</h3>
                    
                    <div class="d-flex flex-column align-items-center gap-3 mb-5">
                        <div class="form-check d-flex align-items-center" style="width: 200px;">
                            <input class="form-check-input border-dark bg-dark" type="radio" name="metode_pengambilan" id="radio_ambil" value="ambil_sendiri" checked style="width: 1.2rem; height: 1.2rem; cursor: pointer; box-shadow: none;">
                            <label class="form-check-label fw-bold text-dark ms-2" for="radio_ambil" style="font-size: 1.1rem; cursor: pointer;">
                                Ambil Sendiri
                            </label>
                        </div>
                        <div class="form-check d-flex align-items-center" style="width: 200px;">
                            <input class="form-check-input border-dark" type="radio" name="metode_pengambilan" id="radio_antar" value="diantar_ke_tempat" style="width: 1.2rem; height: 1.2rem; cursor: pointer; box-shadow: none;">
                            <label class="form-check-label fw-bold text-dark ms-2" for="radio_antar" style="font-size: 1.1rem; cursor: pointer;">
                                Diantar ke Lokasi
                            </label>
                        </div>
                    </div>

                    <!-- Wadah Peta -->
                    <div id="wadah_peta" style="display: none;" class="mt-5 pt-4 border-top border-light border-2">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-map-location-dot text-dark fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Tentukan Lokasi</h4>
                                <span class="text-secondary small">Klik pada peta untuk menetapkan titik pengantaran.</span>
                            </div>
                        </div>
                        
                        <div class="p-2 bg-light rounded-4 mb-4">
                            <div id="map" class="w-100 bg-white" style="height: 50vh; min-height: 400px; z-index: 1;"></div>
                        </div>
                        
                        <!-- Alert Error Jika Lokasi Ditolak -->
                        <div id="alert_lokasi" class="alert alert-danger mt-3 rounded-4 fw-bold shadow-sm border-0" style="display: none;">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <strong>Maaf!</strong> Lokasi di luar jangkauan (Hanya melayani Pontianak Barat, Kota, Selatan, dan Tenggara).
                        </div>

                        <!-- Menampilkan Hasil Peta -->
                        <div class="mt-4 bg-cream p-4 rounded-4" style="background-color: var(--bg-cream);">
                            <label class="form-label fw-bold text-dark mb-3"><i class="fa-solid fa-house text-dark me-2"></i> Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="alamat_satelit" id="alamat_satelit" class="form-control form-control-mc" rows="3" required placeholder="Klik peta untuk melacak alamat otomatis, Anda juga dapat mengeditnya."></textarea>
                        </div>
                    </div>
                    
                    <!-- Button Lanjutkan inside Card -->
                    <div class="mt-4 pt-4 border-top border-light border-2">
                        <button type="submit" id="btn_lanjut" class="btn btn-dark btn-lg rounded-pill w-100 py-3 d-flex align-items-center justify-content-center gap-3 shadow-sm hover-scale">
                            <span class="fs-5 fw-bold text-white">Lanjutkan Pemesanan</span>
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                <i class="fa-solid fa-arrow-right text-dark fs-7"></i>
                            </div>
                        </button>
                    </div>
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
            // Update warna latar radio berdasarkan status checked agar tetap hitam
            radioAmbil.classList.toggle('bg-dark', radioAmbil.checked);
            radioAntar.classList.toggle('bg-dark', radioAntar.checked);

            if (radioAntar.checked) {
                
                wadahPeta.style.display = 'block';
                inputLat.setAttribute('required', 'required');
                inputLng.setAttribute('required', 'required');
                document.getElementById('alamat_satelit').setAttribute('required', 'required');
                
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

                                if (marker) {
                                    marker.setLatLng(e.latlng);
                                } else {
                                    marker = L.marker(e.latlng).addTo(map);
                                }
                                document.getElementById('input_latitude').value = lat;
                                document.getElementById('input_longitude').value = lng;
                                
                                document.getElementById('btn_lanjut').disabled = true;
                                document.getElementById('btn_lanjut').style.opacity = '0.5';
                                document.getElementById('alamat_satelit').value = "Sedang melacak lokasi...";
                                document.getElementById('alert_lokasi').style.display = 'none';

                                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        let fullAddress = data.display_name || "Alamat tidak ditemukan";

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
                                            document.getElementById('btn_lanjut').style.opacity = '1';
                                            document.getElementById('alert_lokasi').style.display = 'none';
                                        } else {
                                            document.getElementById('btn_lanjut').disabled = true;
                                            document.getElementById('btn_lanjut').style.opacity = '0.5';
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
                            map.invalidateSize(); 
                        }
                    } catch (err) {
                        console.error("Gagal menginisialisasi peta Leaflet:", err);
                    }
                }, 400); 
            } else {
                
                wadahPeta.style.display = 'none';
                inputLat.removeAttribute('required');
                inputLng.removeAttribute('required');
                document.getElementById('alamat_satelit').removeAttribute('required');
                inputLat.value = '';
                inputLng.value = '';
                document.getElementById('btn_lanjut').disabled = false;
                document.getElementById('btn_lanjut').style.opacity = '1';
            }
        }

        radioAmbil.addEventListener('change', togglePeta);
        radioAntar.addEventListener('change', togglePeta);
        
        togglePeta();

        document.getElementById('formPilihLokasi').addEventListener('submit', function(e) {
            if (radioAntar.checked && (!inputLat.value || !inputLng.value)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan tentukan titik lokasi pengantaran pada peta.',
                    confirmButtonColor: '#f97316'
                });
            }
        });
    });
</script>
@endpush
