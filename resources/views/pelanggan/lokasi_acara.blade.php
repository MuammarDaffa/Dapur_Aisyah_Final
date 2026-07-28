@extends('layouts.app')
@section('title', 'Pemesanan Katering Harian')

@section('content')
<!-- memanggil CSS leaflet dari CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<div class="container mx-auto px-4 py-8">
<div class="bg-white rounded-2xl shadow-sm border p-6">
       <h2 class="fs-3 fw-bold mb-4 text-black">Metode Pengambilan & Tanggal</h2>

    <form action="{{ route('pelanggan.acara.lanjut') }}" method="POST" id="formLanjutAcara" onsubmit="return validateForm()">
        @csrf
        <input type="hidden" name="layanan_id" value="{{ isset($service) ? $service->id : '' }}">
        <input type="hidden" name="pesanan_id" value="{{ isset($pesanan) ? $pesanan->id : '' }}">
        <input type="hidden" name="latitude" id="input_latitude" value="{{ isset($pesanan) && $pesanan->latitude ? $pesanan->latitude : '' }}">
        <input type="hidden" name="longitude" id="input_longitude" value="{{ isset($pesanan) && $pesanan->longitude ? $pesanan->longitude : '' }}">
        
        <!-- Pilihan Radio Button -->
        <div class="mb-4">
            <label class="form-label fw-bold text-black">Pilih Metode Pengambilan:</label>
            <div class="form-check">
                <!-- Kita atur Ambil Sendiri sebagai pilihan default (checked) -->
                <input class="form-check-input" type="radio" name="metode_pengambilan" id="radio_ambil" value="ambil_sendiri" {{ (!isset($pesanan) || $pesanan->metode_pengambilan == 'ambil_sendiri') ? 'checked' : '' }}>
                <label class="form-check-labe text-black" for="radio_ambil">
                    Ambil Sendiri 
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metode_pengambilan" id="radio_antar" value="diantar_ke_tempat" {{ (isset($pesanan) && $pesanan->metode_pengambilan == 'diantar_ke_tempat') ? 'checked' : '' }}>
                <label class="form-check-label text-black" for="radio_antar">
                    Di Antar ke Lokasi  
                </label>
            </div>
        </div>

        <!-- Input Datepicker Khusus Katering Acara (Selalu Tampil di bawah Radio) -->
        <div class="mb-4">
            <label class="form-label fw-bold text-black">Pilih Tanggal Acara</label>
            <input type="date" name="tanggal_acara" id="tanggal_acara" class="form-control border-danger" required value="{{ isset($pesanan) ? \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('Y-m-d') : '' }}">
        </div>

    <!-- BUNGKUS SELURUH FORM & PETA KE DALAM KOTAK INI (Awalnya Disembunyikan) -->
    <div id="wadah_peta" style="display: none;">
        <hr class="my-4">
        <h5 class="fw-bold mb-3">Tentukan Lokasi Pengantaran</h5>
        
        <!-- === INI FORM LOKASI LAMA ANDA === -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <span class="text-muted small"><i>*Silakan klik pada peta untuk memilih lokasi pengantaran</i></span>
        </div>

        <!-- === INI KANVAS MAP LAMA ANDA === -->
        <div id="map" class="w-100 rounded border border-2 shadow-sm" style="height: 50vh; min-height: 400px; z-index: 1;"></div>
        
    </div> <!-- /Penutup wadah_peta -->

    <!-- Tombol Lanjut -->
    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">Lanjut</button>
    </div>
    
    </form>

</div>
</div>

<!-- Memanggil javascript leaflet dari CDN -->
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

 <!-- Script Peta kita -->
  <script>
    //angka 2 adalah zoom level (semakin besar semakin dekat, semakin kecil semakin jauh)
    var initLat = {{ isset($pesanan) && $pesanan->latitude ? $pesanan->latitude : -0.03194 }};
    var initLng = {{ isset($pesanan) && $pesanan->longitude ? $pesanan->longitude : 109.325 }};
    var map = L.map('map').setView([initLat, initLng], 14);

    // Menambahkan Lapisan Ubin (Tile Layer) dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

        // Menambahkan marker (penanda) di atas peta dan mengikat Popup padanya
    var marker = L.marker([initLat, initLng])
                  .addTo(map)
                  .bindPopup("<b>Halo!</b><br>Pesanan akan diantar kesini.");


    // Menambahkan fungsi deteksi klik pada peta (Event listener)
    map.on('click', function(e){
      //1. ambil koordinat titik yang baru saja diklik
      var lokasiBaru = e.latlng;

      //2. perintahkan pin marker untuk pindah ke lokasi klik tersebut
      marker.setLatLng(lokasiBaru);

      //3. pecah dan ambil angka lintang dan bujurnya secara terpisah
      var lat = lokasiBaru.lat;
      var lng = lokasiBaru.lng;

      // 4. Cetak angka tersebut diam diam ke console (buku catatan browser)
      document.getElementById('input_latitude').value = lat;
      document.getElementById('input_longitude').value = lng;
    }); 

        // === LOGIKA SHOW/HIDE PETA ===
    const radioAmbil = document.getElementById('radio_ambil');
    const radioAntar = document.getElementById('radio_antar');
    const wadahPeta = document.getElementById('wadah_peta');

    // Fungsi untuk mengecek tombol mana yang sedang dipilih
    function aturTampilanPeta() {
        if (radioAntar.checked) {
            // Jika "Di Antar" dipilih, munculkan kotaknya
            wadahPeta.style.display = 'block';
            
            // Beri tahu Leaflet untuk menggambar ulang ukurannya (Jurus Rahasia)
            setTimeout(function(){ 
                map.invalidateSize(); 
            }, 100);
        } else {
            // Jika "Ambil Sendiri", hilangkan kotaknya dari layar
            wadahPeta.style.display = 'none';
        }
    }

    // Panggil saat load untuk mengecek apakah radio diantar_ke_tempat sudah terpilih dari awal
    aturTampilanPeta();

    // Pasang "sensor/telinga" perubahan pada kedua tombol radio tersebut
    radioAmbil.addEventListener('change', aturTampilanPeta);
    radioAntar.addEventListener('change', aturTampilanPeta);

    // Validasi Form sebelum Lanjut
    function validateForm() {
        const tanggalAcara = document.getElementById('tanggal_acara').value;
        const lat = document.getElementById('input_latitude').value;
        const lng = document.getElementById('input_longitude').value;
        const metodeAntar = radioAntar.checked;

        if (!tanggalAcara) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan pilih Tanggal Acara terlebih dahulu.'
            });
            return false;
        }

        if (metodeAntar && (!lat || !lng)) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Karena Anda memilih Di Antar ke Lokasi, silakan klik pada peta untuk menentukan lokasi pengantaran.'
            });
            return false;
        }

        return true;
    }

  </script>

@endsection