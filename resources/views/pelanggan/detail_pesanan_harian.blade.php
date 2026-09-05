@extends('layouts.app')
@section('title', 'Detail Pesanan Harian')

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
        background: var(--primary-terracotta);
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
<div class="page-header">
    <div class="blob-header"></div>
    <div class="container position-relative z-1">
        <div class="text-center mb-4">
            <span class="d-inline-flex align-items-center gap-2 bg-white rounded-pill px-4 py-2 shadow-sm mb-3 border border-light">
                <span class="bg-primary-mc rounded-circle" style="width: 8px; height: 8px;"></span>
                <span class="fw-bold text-secondary small text-uppercase tracking-wider">Katering Harian</span>
            </span>
            <h1 class="display-4 fw-bold text-dark mb-2">
                Detail <span style="color: var(--primary-terracotta); font-style: italic;">Pesanan</span>
            </h1>
            <div class="d-flex justify-content-center align-items-center gap-3">
                <span class="fs-5 text-secondary">#{{ $pesanan->nomor_pesanan }}</span>
                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                    <span class="status-badge bg-success text-white"><i class="fa-solid fa-check-circle me-1"></i> Lunas</span>
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

            <div class="bento-box mb-4">
                @if($pesanan->detailPesanans && $pesanan->detailPesanans->count() > 0)
                <div class="mb-4">
                    <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-calendar-days text-primary-mc me-2"></i> Jadwal Menu</h4>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead class="text-center">
                                <tr>
                                    @php
                                        $isLunas = $pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS;
                                    @endphp
                                    <th class="text-nowrap">Hari/Tanggal</th>
                                    <th class="text-nowrap" style="min-width: 150px;">Menu</th>
                                    <th class="text-nowrap">Harga</th>
                                    <th class="text-nowrap">Porsi</th>
                                    <th class="text-nowrap" style="min-width: 150px;">Tambahan</th>
                                    <th class="text-nowrap">Harga Tbhn</th>
                                    <th class="text-nowrap">Jml</th>
                                    <th class="text-nowrap">Total</th>
                                    @if($isLunas)
                                        <th class="text-nowrap">Opsi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan->detailPesanans as $detail)
                                    @php
                                        $groupedItems = null;
                                        $rowspan = 1;
                                        if ($detail->tambahanLaukPauk && $detail->tambahanLaukPauk->count() > 0) {
                                            $groupedItems = $detail->tambahanLaukPauk->groupBy('id')->map(function ($items) {
                                                return (object) [
                                                    'nama' => $items->first()->nama,
                                                    'harga' => $items->first()->harga,
                                                    'jumlah' => $items->count()
                                                ];
                                            })->values();
                                            $rowspan = $groupedItems->count();
                                        }
                                        
                                        $menuName = $detail->menu ? $detail->menu->nama_menu : ($detail->is_rescheduled ? '-' : '<span class="text-warning"><i class="bi bi-clock-history"></i> Menunggu Jadwal Admin</span>');
                                        $menuPrice = $detail->menu ? $detail->menu->harga : 0;
                                        $formattedDate = $detail->tanggal_pengiriman ? \Carbon\Carbon::parse($detail->tanggal_pengiriman)->translatedFormat('l, d M Y') : '-';
                                    @endphp

                                    <tr>
                                        <td class="text-center bg-light fw-semibold text-nowrap" rowspan="{{ $rowspan }}" style="border-radius: 15px 0 0 15px;">{{ $formattedDate }}</td>
                                        <td rowspan="{{ $rowspan }}"><span class="fw-bold">{!! $menuName !!}</span></td>
                                        <td class="text-end text-nowrap" rowspan="{{ $rowspan }}">
                                            @if($detail->menu)
                                                Rp {{ number_format($menuPrice, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center text-nowrap" rowspan="{{ $rowspan }}">
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $detail->porsi }}</span>
                                        </td>
                                        
                                        @if($groupedItems)
                                            <td>{{ $groupedItems[0]->nama }}</td>
                                            <td class="text-end text-nowrap">Rp {{ number_format($groupedItems[0]->harga, 0, ',', '.') }}</td>
                                            <td class="text-center text-nowrap">{{ $groupedItems[0]->jumlah }}</td>
                                        @else
                                            <td class="text-center text-muted">-</td>
                                            <td class="text-center text-muted">-</td>
                                            <td class="text-center text-muted">-</td>
                                        @endif
                                        
                                        <td class="text-end fw-bold text-primary-mc text-nowrap" rowspan="{{ $rowspan }}">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                                            <td class="text-center text-nowrap" rowspan="{{ $rowspan }}" style="border-radius: 0 15px 15px 0;">
                                                @php
                                                    $today = \Carbon\Carbon::now()->startOfDay();
                                                    $deliveryDate = \Carbon\Carbon::parse($detail->tanggal_pengiriman)->startOfDay();
                                                @endphp
                                                @if($detail->is_rescheduled)
                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill text-nowrap" disabled title="Sudah pernah diubah">
                                                        Ubah Tgl
                                                    </button>
                                                @elseif($deliveryDate->lte($today))
                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill text-nowrap" disabled title="Pesanan pada tanggal tersebut sudah tidak dapat diubah">
                                                        Ubah Tgl
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $detail->id }}">
                                                        <i class="fa-regular fa-calendar-days"></i> Ubah Tgl
                                                    </button>

                                                    <!-- Modal Reschedule -->
                                                    <div class="modal fade text-start" id="rescheduleModal{{ $detail->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content rounded-4 border-0 shadow-lg">
                                                            <form action="{{ route('pelanggan.harian.reschedule', $detail->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header border-0 pb-0">
                                                                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-calendar-day text-primary-mc me-2"></i> Ubah Tanggal Pengiriman</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="text-secondary small mb-4">Pilih tanggal baru untuk pengiriman pesanan ini.</p>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold text-dark">Tanggal Baru</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-calendar text-primary-mc"></i></span>
                                                                            <input type="date" class="form-control border-start-0 py-2 fw-semibold" name="new_date" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-0 pt-0">
                                                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary-mc rounded-pill px-4 fw-bold">Simpan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>

                                    @if($groupedItems && $groupedItems->count() > 1)
                                        @for($i = 1; $i < $rowspan; $i++)
                                            <tr>
                                                <td>{{ $groupedItems[$i]->nama }}</td>
                                                <td class="text-end text-nowrap">Rp {{ number_format($groupedItems[$i]->harga, 0, ',', '.') }}</td>
                                                <td class="text-center text-nowrap">{{ $groupedItems[$i]->jumlah }}</td>
                                            </tr>
                                        @endfor
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <hr class="my-4 text-muted" style="border-style: dashed;">
                @endif

                <div>
                    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-truck-fast text-primary-mc me-2"></i> Informasi Pengiriman</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 h-100">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; min-width: 50px;">
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

                        @if($pesanan->metode_pengambilan == 'diantar_ke_tempat')
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 border h-100" style="background-color: var(--bg-cream); border-color: rgba(224, 93, 54, 0.2) !important;">
                                <span class="text-muted d-block small fw-bold mb-1">Alamat Pengantaran:</span>
                                @if($pesanan->alamat_lengkap)
                                    <p class="mb-0 text-dark small" style="line-height: 1.6;">{{ $pesanan->alamat_lengkap }}</p>
                                @else
                                    <p class="mb-0 text-muted fst-italic small">Alamat belum tersedia.</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($pesanan->detailPesanans && $pesanan->detailPesanans->whereNotNull('minuman_id')->count() > 0)
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="bento-box">
                        <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-glass-water text-primary-mc me-2"></i> Pilihan Minuman</h5>
                        <div class="row g-3">
                            @foreach($pesanan->detailPesanans->whereNotNull('minuman_id') as $detailMinuman)
                                <div class="col-md-6 col-lg-4">
                                    <div class="border-0 bg-light rounded-4 p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-dark fs-6">{{ $detailMinuman->minuman->nama_minuman }}</span>
                                            <span class="fw-bold text-primary-mc">Rp {{ number_format($detailMinuman->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary rounded-pill">{{ $detailMinuman->porsi }} Cup</span>
                                            <span class="text-muted small">&times; Rp {{ number_format($detailMinuman->minuman->harga, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="bento-box bento-highlight mb-5 p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-4 mb-md-0">
                        <p class="text-secondary fw-semibold mb-1"><i class="fa-solid fa-wallet text-primary-mc me-2"></i> Total Pembayaran</p>
                        <h1 class="fw-bold text-dark mb-0 display-5">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</h1>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                            <form id="form-bayar" action="{{ route('pelanggan.harian.bayar', $pesanan->id) }}" method="POST">
                                @csrf
                                <button id="btn-bayar" type="submit" class="btn btn-primary-mc btn-lg rounded-pill px-5 py-3 shadow-lg fw-bold w-100 w-md-auto">
                                    <i class="fa-solid fa-money-bill-wave me-2"></i> Bayar Sekarang
                                </button>
                            </form>
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
                    <a href="{{ route('pelanggan.harian.edit_pesanan', $pesanan->id) }}" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold border-2 hover-bg-dark">
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
            btnBayar.innerHTML = '<i class="fa-solid fa-money-bill-wave me-2"></i> Bayar Sekarang';
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
            btnBayar.innerHTML = '<i class="fa-solid fa-money-bill-wave me-2"></i> Bayar Sekarang';
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
