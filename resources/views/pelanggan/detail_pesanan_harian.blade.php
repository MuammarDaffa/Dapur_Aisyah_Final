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
<div class="container mx-auto px-4 py-5" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
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
                                @php
                                    $isLunas = $pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS;
                                @endphp
                                <th class="text-nowrap py-3">Hari/Tanggal</th>
                                <th class="text-nowrap py-3" style="min-width: 150px;">Menu</th>
                                <th class="text-nowrap py-3">Harga Menu</th>
                                <th class="text-nowrap py-3">Porsi</th>
                                <th class="text-nowrap py-3" style="min-width: 150px;">Tambahan</th>
                                <th class="text-nowrap py-3">Harga Tambahan</th>
                                <th class="text-nowrap py-3">Jumlah</th>
                                <th class="text-nowrap py-3">Total</th>
                                @if($isLunas)
                                    <th class="text-nowrap py-3">Opsi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($pesanan->detailPesanans && $pesanan->detailPesanans->count() > 0)
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
                                        <td rowspan="{{ $rowspan }}" class="text-muted text-nowrap px-3">{{ $formattedDate }}</td>
                                        <td rowspan="{{ $rowspan }}" class="text-muted">{!! $menuName !!}</td>
                                        <td rowspan="{{ $rowspan }}" class="text-muted text-nowrap">Rp {{ number_format($menuPrice, 0, ',', '.') }}</td>
                                        <td rowspan="{{ $rowspan }}" class="text-muted">{{ $detail->porsi }}</td>
                                        
                                        @if($groupedItems)
                                            <td class="text-start text-muted px-3">{{ $groupedItems[0]->nama }}</td>
                                            <td class="text-muted text-nowrap">Rp {{ number_format($groupedItems[0]->harga, 0, ',', '.') }}</td>
                                            <td class="text-muted">{{ $groupedItems[0]->jumlah }}</td>
                                        @else
                                            <td class="text-muted">-</td>
                                            <td class="text-muted">-</td>
                                            <td class="text-muted">-</td>
                                        @endif
                                        
                                        <td rowspan="{{ $rowspan }}" class="fw-bold text-dark text-nowrap">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        
                                        @if($isLunas)
                                            <td rowspan="{{ $rowspan }}" class="text-center text-nowrap">
                                                @php
                                                    $today = \Carbon\Carbon::now()->startOfDay();
                                                    $deliveryDate = \Carbon\Carbon::parse($detail->tanggal_pengiriman)->startOfDay();
                                                @endphp
                                                @if($detail->is_rescheduled || $deliveryDate->lte($today))
                                                    <button type="button" class="btn btn-sm btn-light border text-nowrap" disabled>
                                                        Ubah Tgl
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-warning fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $detail->id }}">
                                                        Ubah Tgl
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
                                                <td class="text-start text-muted px-3">{{ $groupedItems[$i]->nama }}</td>
                                                <td class="text-muted text-nowrap">Rp {{ number_format($groupedItems[$i]->harga, 0, ',', '.') }}</td>
                                                <td class="text-muted">{{ $groupedItems[$i]->jumlah }}</td>
                                            </tr>
                                        @endfor
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Tidak ada jadwal menu</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Metode Pengambilan Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 px-4 py-3">
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
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
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                    <div>
                        <div class="text-muted mb-1">Total Pembayaran</div>
                        <h2 class="fw-bold text-dark mb-0">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</h2>
                    </div>
                    <div>
                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                            <form id="form-bayar" action="{{ route('pelanggan.harian.bayar', $pesanan->id) }}" method="POST">
                                @csrf
                                <button id="btn-bayar" type="submit" class="btn btn-dark fw-bold px-5 py-3 rounded-2 shadow-sm w-100">
                                    Bayar Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-5">
                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                    <a href="{{ route('pelanggan.harian.edit_pesanan', $pesanan->id) }}" class="btn btn-outline-dark fw-bold px-4 py-2 rounded-2">
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
