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
<div class="container mx-auto px-4 py-5" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Header -->
            <div class="mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    Detail Pesanan Anda ({{ $pesanan->nomor_pesanan }})
                </h4>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center mb-0" style="background-color: white;">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-nowrap py-3">Menu / Item</th>
                                <th class="text-nowrap py-3">Harga Satuan</th>
                                <th class="text-nowrap py-3">Porsi</th>
                                <th class="text-nowrap py-3">Tambahan</th>
                                <th class="text-nowrap py-3">Harga Tambahan</th>
                                <th class="text-nowrap py-3">Jumlah</th>
                                <th class="text-nowrap py-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($pesanan->detailPesanans && $pesanan->detailPesanans->count() > 0)
                                @foreach($pesanan->detailPesanans as $detail)
                                    @php
                                        $rowspan = 1;
                                        if ($detail->menu && $detail->tambahanLaukPauk && $detail->tambahanLaukPauk->count() > 0) {
                                            $rowspan = $detail->tambahanLaukPauk->count();
                                        }

                                        $menuName = '-';
                                        $menuPrice = 0;
                                        if($detail->menu){
                                            $menuName = $detail->menu->nama_menu;
                                            $menuPrice = $detail->menu->harga;
                                        } elseif($detail->minuman){
                                            $menuName = $detail->minuman->nama_minuman;
                                            $menuPrice = $detail->minuman->harga;
                                        } else {
                                            $menuName = $detail->item_name ?? '-';
                                        }
                                    @endphp

                                    <tr>
                                        <td rowspan="{{ $rowspan }}" class="text-muted text-nowrap px-3">{{ $menuName }}</td>
                                        <td rowspan="{{ $rowspan }}" class="text-muted text-nowrap">Rp {{ number_format($menuPrice, 0, ',', '.') }}</td>
                                        <td rowspan="{{ $rowspan }}" class="text-muted">{{ $detail->porsi }}</td>
                                        
                                        @if($detail->menu && $detail->tambahanLaukPauk->count() > 0)
                                            <td class="text-start text-muted px-3">{{ $detail->tambahanLaukPauk[0]->nama }}</td>
                                            <td class="text-muted text-nowrap">Rp {{ number_format($detail->tambahanLaukPauk[0]->harga, 0, ',', '.') }}</td>
                                            <td class="text-muted">1</td>
                                        @else
                                            <td class="text-muted">-</td>
                                            <td class="text-muted">-</td>
                                            <td class="text-muted">-</td>
                                        @endif
                                        
                                        <td rowspan="{{ $rowspan }}" class="fw-bold text-dark text-nowrap">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>

                                    @if($detail->menu && $detail->tambahanLaukPauk->count() > 1)
                                        @for($i = 1; $i < $rowspan; $i++)
                                            <tr>
                                                <td class="text-start text-muted px-3">{{ $detail->tambahanLaukPauk[$i]->nama }}</td>
                                                <td class="text-muted text-nowrap">Rp {{ number_format($detail->tambahanLaukPauk[$i]->harga, 0, ',', '.') }}</td>
                                                <td class="text-muted">1</td>
                                            </tr>
                                        @endfor
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada detail menu</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Informasi Acara Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 px-4 py-3">
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
                    <div style="min-width: 180px;" class="text-muted">Tanggal Acara</div>
                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                        <span class="d-none d-md-inline">:</span> 
                        {{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->translatedFormat('l, d F Y') }}
                    </div>
                </div>
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mt-2">
                    <div style="min-width: 180px;" class="text-muted">Metode Pengambilan</div>
                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                        <span class="d-none d-md-inline">:</span> 
                        @if($pesanan->metode_pengambilan == 'diantar_ke_tempat')
                            Diantar ke Lokasi
                        @else
                            Ambil Sendiri
                        @endif
                    </div>
                </div>
                @if($pesanan->metode_pengambilan == 'diantar_ke_tempat' && $pesanan->alamat_lengkap)
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-start mt-2">
                    <div style="min-width: 180px;" class="text-muted">Alamat Pengantaran</div>
                    <div class="text-dark">
                        <span class="d-none d-md-inline fw-bold me-1">:</span> {{ $pesanan->alamat_lengkap }}
                    </div>
                </div>
                @endif
                @if($pesanan->catatan)
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-start mt-2">
                    <div style="min-width: 180px;" class="text-muted">Catatan</div>
                    <div class="text-dark">
                        <span class="d-none d-md-inline fw-bold me-1">:</span> {{ $pesanan->catatan }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Total Pembayaran Card -->
            <div class="card border-0 shadow-sm rounded-3 px-4 py-4 mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                            <span class="text-muted">Total Keseluruhan</span>
                            <span class="fw-bold fs-5 text-dark">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                        </div>
                        @if($pesanan->sisa_pembayaran > 0)
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Sisa Pelunasan</span>
                            <span class="fw-bold fs-5 text-danger">Rp {{ number_format($pesanan->sisa_pembayaran, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="mt-2">
                            @if($pesanan->jumlah_dp == $pesanan->total)
                                <div class="text-muted mb-1">Total Pembayaran (Lunas)</div>
                            @else
                                <div class="text-muted mb-1">Tagihan Saat Ini (DP 50%)</div>
                            @endif
                            <h2 class="fw-bold text-dark mb-0">Rp {{ number_format($pesanan->jumlah_dp, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 text-lg-end">
                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                            <form id="form-bayar" action="{{ route('pelanggan.acara.bayar_dp', $pesanan->id) }}" method="POST">
                                @csrf
                                <button id="btn-bayar" type="submit" class="btn btn-dark fw-bold px-5 py-3 rounded-2 shadow-sm w-100 w-lg-auto">
                                    {{ $pesanan->jumlah_dp == $pesanan->total ? 'Bayar Sekarang' : 'Bayar DP Sekarang' }}
                                </button>
                            </form>
                        @elseif($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DP)
                            <div class="d-inline-flex flex-column align-items-lg-end w-100 w-lg-auto">
                                <div class="btn btn-info text-dark fw-bold px-5 py-3 rounded-2 shadow-sm w-100 mb-2 pointer-events-none">
                                    <i class="fa-solid fa-circle-half-stroke me-1"></i> DP Lunas
                                </div>
                                <span class="text-muted small">Sisa pembayaran dilunasi H-1 acara.</span>
                            </div>
                        @elseif($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                            <div class="btn btn-success fw-bold px-5 py-3 rounded-2 shadow-sm w-100 w-lg-auto pointer-events-none">
                                <i class="fa-solid fa-check-circle me-1"></i> Pembayaran Lunas
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-5">
                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                    <a href="{{ route('pelanggan.acara.edit_pesanan', $pesanan->id) }}" class="btn btn-outline-dark fw-bold px-4 py-2 rounded-2">
                        <i class="fa-solid fa-pen me-2"></i> Edit Pesanan
                    </a>
                @endif
                <a href="{{ route('pelanggan.riwayat') }}" class="btn btn-light border fw-bold text-dark px-4 py-2 rounded-2 shadow-sm">
                    Kembali ke Riwayat
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
