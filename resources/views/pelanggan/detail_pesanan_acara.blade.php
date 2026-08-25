@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<!-- Leaflet CSS Removed -->

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
                    Detail Menu
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Menu</th>
                                    <th style="width: 20%;">Harga Porsi / Cup</th>
                                    <th style="width: 15%;">Jumlah</th>
                                    <th style="width: 20%;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan->detailPesanans as $detail)
                                    <tr>
                                        <td>
                                            @if($detail->menu)
                                                <div class="fw-bold">{{ $detail->menu->nama_menu }}</div>
                                                @if($detail->tambahanLaukPauk->count() > 0)
                                                    <ul class="list-unstyled ms-3 mb-0 small text-muted">
                                                        @foreach($detail->tambahanLaukPauk as $item)
                                                            <li>&bull; {{ $item->nama }} @if($item->harga > 0)(+ Rp {{ number_format($item->harga, 0, ',', '.') }})@endif</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @elseif($detail->minuman)
                                                <div class="fw-bold">{{ $detail->minuman->nama_minuman }}</div>
                                            @else
                                                <div class="fw-bold">{{ $detail->item_name ?? '-' }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($detail->menu)
                                                Rp {{ number_format($detail->menu->harga, 0, ',', '.') }}
                                            @elseif($detail->minuman)
                                                Rp {{ number_format($detail->minuman->harga, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $detail->porsi }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold fs-5">
                    Informasi Acara
                </div>
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <strong class="text-muted" style="width: 180px; flex-shrink: 0;">Tanggal Acara</strong>
                        <div class="fw-semibold" style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="fw-semibold flex-grow-1">
                            {{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong class="text-muted" style="width: 180px; flex-shrink: 0;">Metode Pengambilan</strong>
                        <div class="fw-semibold" style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="fw-semibold flex-grow-1">
                            @if($pesanan->metode_pengambilan == 'diantar_ke_tempat')
                                Di Antar ke Lokasi
                            @else
                                Ambil Sendiri
                            @endif
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong class="text-muted" style="width: 180px; flex-shrink: 0;">Alamat</strong>
                        <div class="fw-semibold" style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="fw-semibold flex-grow-1">
                            @if($pesanan->alamat_lengkap)
                                {{ $pesanan->alamat_lengkap }}
                            @elseif($pesanan->metode_pengambilan == 'ambil_sendiri')
                                <span class="fst-italic">Diambil di Dapur Aisyah</span>
                            @else
                                <span class="fst-italic">-</span>
                            @endif
                        </div>
                    </div>
                    @if($pesanan->catatan)
                    <div class="d-flex mb-2">
                        <strong class="text-muted" style="width: 180px; flex-shrink: 0;">Catatan</strong>
                        <div class="fw-semibold" style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="fw-semibold flex-grow-1">
                            {{ $pesanan->catatan }}
                        </div>
                    </div>
                    @endif


                </div>
            </div>

           <div class="card shadow-sm border-0 mb-5 bg-light">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total</span>
                        <span class="fw-bold fs-5">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                    </div>
                    @if($pesanan->sisa_pembayaran > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sisa Pelunasan </span>
                        <span class="fw-bold fs-5 ">Rp {{ number_format($pesanan->sisa_pembayaran, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            @if($pesanan->jumlah_dp == $pesanan->total)
                                <p class="text-muted mb-0">Total Pembayaran (Lunas)</p>
                            @else
                                <p class="text-muted mb-0">DP (50%)</p>
                            @endif
                            <h3 class="fw-bold  mb-0">Rp {{ number_format($pesanan->jumlah_dp, 0, ',', '.') }}</h3>
                        </div>
                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                            <form id="form-bayar" action="{{ route('pelanggan.acara.bayar_dp', $pesanan->id) }}" method="POST">
                                @csrf
                                <button id="btn-bayar" type="submit" class="btn btn-warning btn-lg px-5 shadow-sm fw-bold">
                                    {{ $pesanan->jumlah_dp == $pesanan->total ? 'Bayar Sekarang' : 'Bayar DP Sekarang' }}
                                </button>
                            </form>
                        @elseif($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DP)
                            <div class="text-end">
                                <button disabled class="btn btn-secondary btn-lg px-5 text-white">Bayar DP Sekarang</button>
                                <!-- <div class="text-muted small mt-1">DP sudah dibayarkan.</div> -->
                            </div>
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
                    <a href="{{ route('pelanggan.acara.edit_pesanan', $pesanan->id) }}" class="btn btn-secondary px-5 py-2 fw-bold shadow-sm">
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


@endsection
