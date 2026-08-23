@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<!-- Leaflet CSS Removed -->

<div class="container mx-auto px-4 py-8 mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <h2 class="fs-3 fw-bold text-dark mt-2 mb-4">Detail Pesanan Anda ({{ $pesanan->nomor_pesanan }})</h2>



            @if($pesanan->detailPesanans && $pesanan->detailPesanans->count() > 0)
            <div class="mb-4">
                
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 15%;">Hari/Tanggal</th>
                                <th style="width: 15%;">Menu</th>
                                <th style="width: 12%;">Harga Menu</th>
                                <th style="width: 5%;">Porsi</th>
                                <th style="width: 15%;">Tambahan</th>
                                <th style="width: 12%;">Harga Tambahan</th>
                                <th style="width: 5%;">Jumlah</th>
                                <th style="width: 11%;">Total</th>
                                <th style="width: 10%;">Opsi</th>
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
                                    <td class="text-center" rowspan="{{ $rowspan }}">{{ $formattedDate }}</td>
                                    <td rowspan="{{ $rowspan }}">{!! $menuName !!}</td>
                                    <td class="text-end" rowspan="{{ $rowspan }}">
                                        @if($detail->menu)
                                            Rp {{ number_format($menuPrice, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center" rowspan="{{ $rowspan }}">{{ $detail->porsi }}</td>
                                    
                                    @if($groupedItems)
                                        <td>{{ $groupedItems[0]->nama }}</td>
                                        <td class="text-end">Rp {{ number_format($groupedItems[0]->harga, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $groupedItems[0]->jumlah }}</td>
                                    @else
                                        <td class="text-center text-muted">-</td>
                                        <td class="text-center text-muted">-</td>
                                        <td class="text-center text-muted">-</td>
                                    @endif
                                    
                                    <td class="text-end fw-bold" rowspan="{{ $rowspan }}">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    <td class="text-center" rowspan="{{ $rowspan }}">
                                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                                            @php
                                                $today = \Carbon\Carbon::now()->startOfDay();
                                                $deliveryDate = \Carbon\Carbon::parse($detail->tanggal_pengiriman)->startOfDay();
                                            @endphp
                                            @if($detail->is_rescheduled)
                                                <button type="button" class="btn btn-sm btn-secondary text-nowrap" disabled title="Sudah pernah diubah">
                                                    Ubah Tanggal
                                                </button>
                                            @elseif($deliveryDate->lte($today))
                                                <button type="button" class="btn btn-sm btn-secondary text-nowrap" disabled title="Pesanan pada tanggal tersebut sudah tidak dapat diubah">
                                                    Ubah Tanggal
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-warning text-nowrap" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $detail->id }}">
                                                    Ubah Tanggal
                                                </button>

                                                <!-- Modal Reschedule -->
                                                <div class="modal fade text-start" id="rescheduleModal{{ $detail->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <form action="{{ route('pelanggan.harian.reschedule', $detail->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                            <h5 class="modal-title">Ubah Tanggal Pengiriman</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                            <p class="mb-3 text-muted">Silakan pilih tanggal pengganti untuk pesanan ini.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Tanggal Baru</label>
                                                                <input type="date" class="form-control" name="new_date" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" required>
                                                            </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan Tanggal</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>

                                @if($groupedItems && $groupedItems->count() > 1)
                                    @for($i = 1; $i < $rowspan; $i++)
                                        <tr>
                                            <td>{{ $groupedItems[$i]->nama }}</td>
                                            <td class="text-end">Rp {{ number_format($groupedItems[$i]->harga, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $groupedItems[$i]->jumlah }}</td>
                                        </tr>
                                    @endfor
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="card shadow-sm border-0 mb-4">
             
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <span class="text-muted" style="width: 180px; flex-shrink: 0;">Metode Pengambilan</span>
                        <div class="fw-semibold" style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="fw-semibold flex-grow-1">
                            @if($pesanan->metode_pengambilan == 'diantar_ke_tempat')
                                Di Antar ke Lokasi
                            @else
                                Ambil Sendiri
                            @endif
                        </div>
                    </div>

                    @if($pesanan->metode_pengambilan == 'diantar_ke_tempat')
                        <hr class="my-4">
                        <h6 class="fw-bold text-dark mb-3">Lokasi Pengantaran</h6>
                            @if($pesanan->alamat_lengkap)
                                <p class="mb-0 text-dark">{{ $pesanan->alamat_lengkap }}</p>
                            @else
                                <p class="mb-0 text-muted fst-italic">Alamat belum tersedia.</p>
                            @endif
                    @endif
                </div>
            </div>

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
                    <!-- <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Keseluruhan</span>
                        <span class="fw-bold fs-5">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                    </div>
                    <hr> -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0">Total Pembayaran</p>
                            <h3 class="fw-bold  mb-0">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</h3>
                        </div>
                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                            <form id="form-bayar" action="{{ route('pelanggan.harian.bayar', $pesanan->id) }}" method="POST">
                                @csrf
                                <button id="btn-bayar" type="submit" class="btn btn-warning text-white btn-lg px-5 shadow-sm fw-bold">
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
            btnBayar.innerHTML = 'Bayar Sekarang';
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
            btnBayar.innerHTML = 'Bayar Sekarang';
            btnBayar.disabled = false;
            alert('Gagal menghubungi server.');
        });
    });
</script>
@endif


@endsection
