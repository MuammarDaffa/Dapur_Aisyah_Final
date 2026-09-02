@extends('layouts.app')
@section('title', 'Detail Pesanan Acara')

@push('styles')
<style>
    .page-header {
        position: relative;
        padding: 80px 0 40px;
        background-color: var(--bg-cream);
        overflow: hidden;
    }
    
    .blob-header {
        position: absolute;
        top: -50px; right: -10%;
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
        padding: 30px;
        position: relative;
        overflow: hidden;
        height: 100%;
        border: 2px solid rgba(0,0,0,0.03);
    }
    
    .bento-highlight {
        background: var(--bg-cream);
        border: 2px solid var(--accent-yellow);
    }

    .status-badge {
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table-modern thead th {
        background: rgba(0,0,0,0.02);
        color: var(--text-dark);
        border: none;
        padding: 15px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
    }

    .table-modern tbody tr {
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }

    .table-modern tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .table-modern tbody td {
        border: none;
        padding: 20px 15px;
        vertical-align: middle;
    }

    .table-modern tbody td:first-child {
        border-radius: 15px 0 0 15px;
    }

    .table-modern tbody td:last-child {
        border-radius: 0 15px 15px 0;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="blob-header"></div>
    <div class="container position-relative z-1">
        <div class="text-center mb-4">
            <span class="d-inline-flex align-items-center gap-2 bg-white rounded-pill px-4 py-2 shadow-sm mb-3 border border-light">
                <span class="bg-primary-mc rounded-circle" style="width: 8px; height: 8px;"></span>
                <span class="fw-bold text-secondary small text-uppercase tracking-wider">Katering Acara Kantoran</span>
            </span>
            <h1 class="display-4 fw-bold text-dark mb-2">
                Detail <span style="color: var(--primary-terracotta); font-style: italic;">Pesanan</span>
            </h1>
            <div class="d-flex justify-content-center align-items-center gap-3">
                <span class="fs-5 text-secondary">#{{ $pesanan->nomor_pesanan }}</span>
                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                    <span class="status-badge bg-success text-white"><i class="fa-solid fa-check-circle me-1"></i> Lunas</span>
                @elseif($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DP)
                    <span class="status-badge bg-info text-dark"><i class="fa-solid fa-circle-half-stroke me-1"></i> DP Dibayar</span>
                @else
                    <span class="status-badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Belum Dibayar</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-4 pb-5 mt-n4">
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-xl-10">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="bento-box mb-4">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-list-ul text-primary-mc me-2"></i> Detail Menu</h4>
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead class="text-center">
                            <tr>
                                <th>Menu / Item</th>
                                <th style="width: 25%;">Harga Satuan</th>
                                <th style="width: 15%;">Jumlah</th>
                                <th style="width: 25%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanan->detailPesanans as $detail)
                                <tr>
                                    <td class="bg-light" style="border-radius: 15px 0 0 15px;">
                                        @if($detail->menu)
                                            <div class="fw-bold fs-6 text-dark">{{ $detail->menu->nama_menu }}</div>
                                            @if($detail->tambahanLaukPauk->count() > 0)
                                                <ul class="list-unstyled ms-3 mt-2 mb-0 small text-muted border-start border-2 ps-2">
                                                    @foreach($detail->tambahanLaukPauk as $item)
                                                        <li>{{ $item->nama }} @if($item->harga > 0)<span class="text-primary-mc fw-semibold">(+ Rp {{ number_format($item->harga, 0, ',', '.') }})</span>@endif</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @elseif($detail->minuman)
                                            <div class="fw-bold fs-6 text-dark">{{ $detail->minuman->nama_minuman }}</div>
                                        @else
                                            <div class="fw-bold fs-6 text-dark">{{ $detail->item_name ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">
                                        @if($detail->menu)
                                            Rp {{ number_format($detail->menu->harga, 0, ',', '.') }}
                                        @elseif($detail->minuman)
                                            Rp {{ number_format($detail->minuman->harga, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6">{{ $detail->porsi }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-primary-mc" style="border-radius: 0 15px 15px 0;">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bento-box mb-4">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-regular fa-calendar-check text-primary-mc me-2"></i> Informasi Acara</h4>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fa-regular fa-calendar-days text-primary-mc fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small">Tanggal Acara</span>
                                <span class="fw-bold text-dark fs-5">
                                    {{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                @if($pesanan->metode_pengambilan == 'diantar_ke_tempat')
                                    <i class="fa-solid fa-motorcycle text-primary-mc fs-4"></i>
                                @else
                                    <i class="fa-solid fa-store text-secondary fs-4"></i>
                                @endif
                            </div>
                            <div>
                                <span class="text-muted d-block small">Metode</span>
                                <span class="fw-bold text-dark fs-5">
                                    @if($pesanan->metode_pengambilan == 'diantar_ke_tempat')
                                        Diantar ke Lokasi
                                    @else
                                        Ambil Sendiri
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark mb-2">Lokasi:</h6>
                        <div class="p-3 bg-cream rounded-4 border mb-3" style="background-color: var(--bg-cream); border-color: rgba(224, 93, 54, 0.2) !important;">
                            @if($pesanan->alamat_lengkap)
                                <p class="mb-0 text-dark" style="line-height: 1.6;">{{ $pesanan->alamat_lengkap }}</p>
                            @elseif($pesanan->metode_pengambilan == 'ambil_sendiri')
                                <p class="mb-0 text-dark fw-bold fst-italic">Diambil di Dapur Aisyah</p>
                            @else
                                <p class="mb-0 text-muted fst-italic">-</p>
                            @endif
                        </div>

                        @if($pesanan->catatan)
                            <h6 class="fw-bold text-dark mb-2">Catatan Pesanan:</h6>
                            <div class="p-3 bg-light rounded-4 border border-light">
                                <p class="mb-0 text-secondary fst-italic">"{{ $pesanan->catatan }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bento-box bento-highlight mb-5 p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                            <span class="text-secondary fw-semibold fs-5">Total Keseluruhan</span>
                            <span class="fw-bold fs-4 text-dark">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                        </div>
                        @if($pesanan->sisa_pembayaran > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary fw-semibold">Sisa Pelunasan</span>
                            <span class="fw-bold fs-5 text-danger">Rp {{ number_format($pesanan->sisa_pembayaran, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="mt-4 pt-3">
                            @if($pesanan->jumlah_dp == $pesanan->total)
                                <p class="text-secondary fw-bold small text-uppercase tracking-wider mb-1"><i class="fa-solid fa-wallet text-primary-mc me-2"></i> Total Pembayaran (Lunas)</p>
                            @else
                                <p class="text-secondary fw-bold small text-uppercase tracking-wider mb-1"><i class="fa-solid fa-wallet text-primary-mc me-2"></i> Tagihan Saat Ini (DP 50%)</p>
                            @endif
                            <h1 class="fw-bold text-dark mb-0 display-6">Rp {{ number_format($pesanan->jumlah_dp, 0, ',', '.') }}</h1>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 text-center text-lg-end">
                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                            <form id="form-bayar" action="{{ route('pelanggan.acara.bayar_dp', $pesanan->id) }}" method="POST">
                                @csrf
                                <button id="btn-bayar" type="submit" class="btn btn-primary-mc btn-lg rounded-pill px-5 py-4 shadow-lg fw-bold w-100 w-md-auto d-inline-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-solid fa-money-bill-wave fs-5"></i> 
                                    <span class="fs-5">{{ $pesanan->jumlah_dp == $pesanan->total ? 'Bayar Sekarang' : 'Bayar DP Sekarang' }}</span>
                                </button>
                            </form>
                        @elseif($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DP)
                            <div class="d-inline-flex flex-column align-items-center align-items-lg-end">
                                <div class="d-inline-flex align-items-center gap-2 bg-info text-dark rounded-pill px-5 py-3 shadow-sm fw-bold mb-2">
                                    <i class="fa-solid fa-circle-half-stroke fs-5"></i>
                                    <span class="fs-5">DP Lunas</span>
                                </div>
                                <span class="text-muted small">Sisa pembayaran dilunasi H-1 acara.</span>
                            </div>
                        @elseif($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                            <div class="d-inline-flex align-items-center gap-2 bg-success text-white rounded-pill px-5 py-3 shadow-sm fw-bold">
                                <i class="fa-solid fa-check-circle fs-5"></i>
                                <span class="fs-5">Pembayaran Lunas</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center justify-content-md-start gap-3 mb-5">
                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                    <a href="{{ route('pelanggan.acara.edit_pesanan', $pesanan->id) }}" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold border-2 hover-bg-dark">
                        <i class="fa-solid fa-pen me-2"></i> Edit Pesanan
                    </a>
                @endif
                <a href="{{ route('pelanggan.riwayat') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark border-2 shadow-sm">
                    <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Riwayat
                </a>
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

        const originalBtnText = btnBayar.innerHTML;
        btnBayar.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i> Memproses...';
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
            btnBayar.innerHTML = originalBtnText;
            btnBayar.disabled = false;

            if(data.status === 'success'){
                snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pembayaran berhasil dikonfirmasi.',
                            confirmButtonColor: '#2C4A3B'
                        }).then(() => {
                            window.location.href = "{{ route('pelanggan.riwayat') }}"; 
                        });
                    },
                    onPending: function(result){
                        Swal.fire({
                            icon: 'info',
                            title: 'Menunggu',
                            text: 'Menunggu pembayaran Anda!',
                            confirmButtonColor: '#f97316'
                        }).then(() => {
                            window.location.href = "{{ route('pelanggan.riwayat') }}";
                        });
                    },
                    onError: function(result){
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Pembayaran gagal!',
                            confirmButtonColor: '#dc3545'
                        });
                    },
                    onClose: function(){
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Anda menutup jendela pembayaran tanpa menyelesaikan pembayaran.',
                            confirmButtonColor: '#f97316'
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem.',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnBayar.innerHTML = originalBtnText;
            btnBayar.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal menghubungi server.',
                confirmButtonColor: '#dc3545'
            });
        });
    });
</script>
@endif
@endsection
