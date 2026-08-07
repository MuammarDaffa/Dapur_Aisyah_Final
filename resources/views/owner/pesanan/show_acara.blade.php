@extends('layouts.owner')
@section('title', 'Detail Pesanan')
@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.pesanan') }}" class="btn btn-default"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Detail Pesanan Layout -->
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Detail Pesanan: {{ $pesanan->nomor_pesanan }}</h3>
                <div class="ms-auto">
                    <span class="badge text-bg-{{ $pesanan->status_pembayaran_color }} mb-1">
                        {{ $pesanan->status_pembayaran_label }}
                    </span>
                    <span class="badge text-bg-{{ $pesanan->status_pesanan_color }}">
                        {{ $pesanan->status_pesanan_label }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row detail-info mb-4">
                    <div class="col-sm-4 detail-col">
                        Pelanggan
                        <address>
                            <strong>{{ $pesanan->user->name }}</strong><br>
                            Telepon: {{ $pesanan->user->phone }}<br>
                            Email: {{ $pesanan->user->email }}
                        </address>
                    </div>
                    <div class="col-sm-4 detail-col">
                        Detail Pesanan
                        <address>
                            <strong>Katering {{ ucfirst($pesanan->tipe_layanan) }}</strong><br>
                                Tgl Kirim: {{ $pesanan->tanggal_pesanan->format('d M Y') }}<br>
                            <strong>Metode:</strong>{{ ucfirst($pesanan->metode_pengambilan) }}<br>
                            @if($pesanan->metode_pengambilan === 'diantar_ke_tempat')
                                <strong>Lokasi:</strong> {{ $pesanan->alamat_lengkap ?? '-' }}
                            @endif
                        </address>
                    </div>

                </div>

                @if($pesanan->catatan)
                <div class="callout callout-info mb-4">
                    <h5><i class="fa-solid fa-note-sticky text-info"></i> Catatan Pesanan:</h5>
                    <p>{{ $pesanan->catatan }}</p>
                </div>
                @endif

                @if($pesanan->alasan_pembatalan)
                <div class="callout callout-danger mb-4">
                    <h5><i class="fa-solid fa-ban text-danger"></i> Alasan Pembatalan:</h5>
                    <p>{{ $pesanan->alasan_pembatalan }}</p>
                </div>
                @endif

                <h4 class="mb-3">Detail Pesanan</h4>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Menu</th>
                                <th style="width: 15%;">Porsi / Cup</th>
                                <th style="width: 25%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan->detailPesanans as $detail)
                                <tr>
                                    <td>
                                        @if($detail->menu)
                                            <div class="fw-medium">{{ $detail->menu->nama_menu }}</div>
                                            @if($detail->tipe_penyajian)
                                                <small class="text-muted">Kemasan: {{ $detail->tipe_penyajian }}</small>
                                            @endif
                                            @if($detail->menuItems->count() > 0)
                                                <ul class="list-unstyled ms-3 mb-0 small text-muted">
                                                    @foreach($detail->menuItems as $item)
                                                        <li>&bull; {{ $item->nama }} @if($item->harga > 0)(+ Rp {{ number_format($item->harga, 0, ',', '.') }})@endif</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @elseif($detail->minuman)
                                            <div class="fw-medium">{{ $detail->minuman->nama_minuman }}</div>
                                        @else
                                            <span class="text-warning"><i class="fa-solid fa-clock"></i> Menunggu Jadwal Admin</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $detail->porsi }}
                                    </td>
                                    <td class="text-end fw-bold align-middle">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Data pesanan tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- <div class="card card-outline card-success mb-4">
            <div class="card-header">
                <h3 class="card-title">Pembayaran</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>Rp {{ number_format($pesanan->subtotal,0,',','.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <strong class="fs-5">Total</strong>
                    <strong class="fs-5 text-success">Rp {{ number_format($pesanan->total,0,',','.') }}</strong>
                </div>
                
                @if($pesanan->refund_status && $pesanan->refund_status !== 'none')
                    <div class="alert alert-{{ $pesanan->refund_status === 'pending' ? 'warning' : 'success' }} py-2 mt-3 mb-0">
                        <i class="fa-solid fa-{{ $pesanan->refund_status === 'pending' ? 'clock' : 'check' }}"></i>
                        Refund: {{ $pesanan->refund_status === 'pending' ? 'Menunggu Refund' : 'Sudah Direfund' }}
                    </div>
                @endif
            </div>
        </div> -->

    </div>
</div>

@endsection
