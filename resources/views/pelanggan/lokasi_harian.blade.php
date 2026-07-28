@extends('layouts.app')
@section('title', 'Pemesanan Katering Harian')

@section('content')
<!-- memanggil CSS leaflet dari CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<div class="container mx-auto px-4 py-8">
<div class="bg-white rounded-2xl shadow-sm border p-6">
    <h2 class="fs-3 fw-bold mb-4">Peta Lokasi Saya</h2>

            <!-- Form Pengiriman Data ke Laravel -->
    <form action="{{ route('pelanggan.simpan-lokasi-peta') }}" method="POST" id="formLokasi">
        <!-- Wajib ada untuk keamanan Laravel -->
        @csrf 
        
        <!-- Input yang disembunyikan dari mata pelanggan (Hidden) -->
        <input type="hidden" name="latitude" id="input_latitude">
        <input type="hidden" name="longitude" id="input_longitude">

        <!-- Tombol untuk mengirim data form -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                <i>*Silakan klik pada peta untuk menentukan titik pengiriman</i>
            </span>
            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                Simpan Lokasi Ini
            </button>
        </div>
    </form>


      <!-- Ini adalah kanvas untuk peta yang sudah dipercantik & responsif -->
    <div id="map" class="w-100 rounded border border-2 shadow-sm" style="height: 50vh; min-height: 400px; z-index: 1;"></div>

</div>
</div>

<!-- Memanggil javascript leaflet dari CDN -->
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

 <!-- Script Peta kita -->
  <script>
    //angka 2 adalah zoom level (semakin besar semakin dekat, semakin kecil semakin jauh)
    var map = L.map('map').setView([-0.03194, 109.325], 14);

    // Menambahkan Lapisan Ubin (Tile Layer) dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

        // Menambahkan marker (penanda) di atas peta dan mengikat Popup padanya
    var marker = L.marker([-0.03194, 109.325])
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
  </script>

@endsection