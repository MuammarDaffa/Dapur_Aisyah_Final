@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container mx-auto px-4 py-8 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="fs-3 fw-bold text-dark mt-2 mb-4">Detail Pesanan Anda ({{ $pesanan->nomor_pesanan }})</h2>

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

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold fs-5">
                    Ringkasan Acara
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 200px;">Status Pembayaran</td>
                            <td class="fw-bold">
                                <span class="badge bg-{{ $pesanan->status_pembayaran_color }} fs-6">{{ $pesanan->status_pembayaran_label }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted" style="width: 200px;">Status Pesanan</td>
                            <td class="fw-bold">
                                @if(!is_null($pesanan->status_pesanan))
                                    <span class="badge bg-{{ $pesanan->status_pesanan_color }} fs-6">{{ $pesanan->status_pesanan_label }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Dibuat</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($pesanan->created_at)->translatedFormat('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Layanan Katering</td>
                            <td class="fw-semibold">{{ $pesanan->layanan->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode Pengiriman</td>
                            <td class="fw-semibold">
                                @if($pesanan->metode_pengambilan == 'diantar_ke_tempat')
                                    Di Antar ke Lokasi
                                @else
                                    Ambil Sendiri
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($pesanan->metode_pengambilan == 'diantar_ke_tempat' && $pesanan->latitude && $pesanan->longitude)
                        <hr class="my-4">
                        <h6 class="fw-bold text-dark mb-3">Lokasi Pengantaran</h6>
                        <div id="map" class="w-100 rounded border shadow-sm mb-3" style="height: 250px; z-index: 1;"></div>
                        <!-- <a href="https://www.google.com/maps?q={{ $pesanan->latitude }},{{ $pesanan->longitude }}" target="_blank" class="btn btn-outline-primary btn-sm fw-bold">
                            <i class="bi bi-geo-alt-fill me-1"></i> Buka Rute di Google Maps
                        </a> -->
                    @endif
                </div>
            </div>

            @if($pesanan->detailPesanans && $pesanan->detailPesanans->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold fs-5">
                    Detail Menu
                </div>
                <div class="card-body">
                    @foreach($pesanan->detailPesanans as $detail)
                        <h6 class="fw-bold mb-3">{{ $detail->menu->nama_menu }}</h6>
                        
                        @if($detail->menuItems->count() > 0)
                            <ul class="list-group list-group-flush mb-3">
                                @foreach($detail->menuItems as $item)
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span>&bull; {{ $item->nama }}</span>
                                        @if($item->harga > 0)
                                            <span class="text-muted">+ Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="d-flex justify-content-between text-dark mb-2">
                            <span class="text-muted">Jumlah Porsi</span>
                            <span class="fw-bold">{{ $detail->porsi }} porsi</span>
                        </div>
                        <div class="d-flex justify-content-between text-dark">
                            <span class="text-muted">Subtotal Menu</span>
                            <span class="fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if($pesanan->layanan->isHarian() && in_array($pesanan->status_pembayaran, [\App\Models\Pesanan::PEMBAYARAN_DP, \App\Models\Pesanan::PEMBAYARAN_LUNAS]))
                            <div class="mt-3">
                                @php
                                    $tanggalPengiriman = \Carbon\Carbon::parse($detail->tanggal_pengiriman);
                                    $isHMinus1 = $tanggalPengiriman->copy()->subDay()->endOfDay()->isFuture();
                                @endphp
                                <p class="mb-1 text-muted small">Jadwal Pengiriman: <strong>{{ $tanggalPengiriman->translatedFormat('d F Y') }}</strong></p>
                                
                                @if($detail->is_rescheduled)
                                    <span class="badge bg-secondary"><i class="bi bi-info-circle"></i> Sudah Diganti Tanggal</span>
                                @elseif(!$isHMinus1)
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Lewat Batas H-1</span>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $detail->id }}">
                                        <i class="bi bi-calendar-event"></i> Ganti Tanggal
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="rescheduleModal{{ $detail->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ganti Tanggal Pengiriman</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('pelanggan.harian.reschedule') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <input type="hidden" name="detail_id" value="{{ $detail->id }}">
                                                        <p class="small text-muted mb-3">Ganti tanggal hanya bisa dilakukan 1x dan harus pada hari kerja (Senin - Jumat).</p>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Pilih Tanggal Baru</label>
                                                            <input type="date" name="tanggal_baru" class="form-control" required min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(!$loop->last)
                            <hr class="my-4 border-secondary opacity-25">
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if($pesanan->detailPesanans && $pesanan->detailPesanans->whereNotNull('minuman_id')->count() > 0)
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-header bg-white fw-bold fs-5 text-success">
                        <i class="bi bi-cup-straw me-2"></i> Pilihan Minuman
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-0">
                            @foreach($pesanan->detailPesanans->whereNotNull('minuman_id') as $detailMinuman)
                                <li class="list-group-item bg-transparent px-0">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold">{{ $detailMinuman->minuman->nama_minuman }}</span>
                                        <span class="fw-bold text-success">Rp {{ number_format($detailMinuman->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="text-muted small">
                                        {{ $detailMinuman->porsi }} cup &times; Rp {{ number_format($detailMinuman->minuman->harga, 0, ',', '.') }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

           <div class="card shadow-sm border-0 mb-5 bg-light">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Keseluruhan</span>
                        <span class="fw-bold fs-5">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0">Total Pembayaran (Lunas)</p>
                            <h3 class="fw-bold text-success mb-0">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</h3>
                        </div>
                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                            <form id="form-bayar" action="{{ route('pelanggan.acara.bayar_dp', $pesanan->id) }}" method="POST">
                                @csrf
                                <button id="btn-bayar" type="submit" class="btn btn-primary btn-lg px-5 shadow-sm fw-bold">
                                    Bayar Sekarang
                                </button>
                            </form>
                        @elseif($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                            <div class="text-end">
                                <button disabled class="btn btn-secondary btn-lg px-5 shadow-sm fw-bold">Lunas</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-start mb-5 gap-3">
                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                    <a href="{{ route('pelanggan.harian.edit_pesanan', $pesanan->id) }}" class="btn btn-secondary px-5 py-2 fw-bold shadow-sm">
                        Kembali
                    </a>
                @else
                    <a href="{{ route('pelanggan.riwayat') }}" class="btn btn-secondary px-5 py-2 fw-bold shadow-sm">
                        Kembali ke Riwayat
                    </a>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- Script Snap Midtrans -->
@if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('form-bayar').addEventListener('submit', function(e){
        e.preventDefault();

        const form = this;
        const btnBayar = document.getElementById('btn-bayar');
        const url = form.action;
        const csrfToken = form.querySelector('input[name="_token"]').value;

        btnBayar.innerHTML = 'Memproses...';
        btnBayar.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({}) 
        })
        .then(response => response.json())
        .then(data => {
            btnBayar.innerHTML = 'Bayar DP Sekarang';
            btnBayar.disabled = false;

            if(data.status === 'success'){
                snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        alert("Pembayaran berhasil!");
                        window.location.href = "{{ route('pelanggan.riwayat') }}"; 
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran Anda!");
                        window.location.href = "{{ route('pelanggan.riwayat') }}";
                    },
                    onError: function(result){
                        alert("Pembayaran gagal!");
                    },
                    onClose: function(){
                        alert('Anda menutup jendela pembayaran tanpa menyelesaikan pembayaran.');
                    }
                });
            } else {
                alert('Terjadi kesalahan sistem.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnBayar.innerHTML = 'Bayar DP Sekarang';
            btnBayar.disabled = false;
            alert('Gagal menghubungi server.');
        });
    });
</script>
@endif

@if($pesanan->metode_pengambilan == 'diantar_ke_tempat' && $pesanan->latitude && $pesanan->longitude)
<!-- Memanggil javascript leaflet dari CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var lat = {{ $pesanan->latitude }};
        var lng = {{ $pesanan->longitude }};
        var map = L.map('map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        L.marker([lat, lng])
          .addTo(map)
          .bindPopup("<b>Lokasi Pengantaran</b><br>Pesanan akan dikirim ke titik ini.")
          .openPopup();
    });
</script>
@endif
@endsection
