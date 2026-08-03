@extends('layouts.admin')
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
                            @if($pesanan->tipe_layanan !== 'harian')
                                Tgl Kirim: {{ $pesanan->tanggal_pesanan->format('d M Y') }}<br>
                            @endif
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                @if($pesanan->tipe_layanan === 'harian')
                                <th>Tanggal Pengiriman</th>
                                @endif
                                <th>Menu</th>
                                <th>Tambahan</th>
                                <th class="text-center">Porsi</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan->detailPesanans as $detail)
                            <tr>
                                @if($pesanan->tipe_layanan === 'harian')
                                <td class="align-middle text-muted">
                                    {{ \Carbon\Carbon::parse($detail->tanggal_pengiriman)->translatedFormat('d F Y') }}
                                </td>
                                @endif
                                <td class="align-middle fw-medium">
                                    {{ $detail->menu->nama_menu ?? '-' }}
                                    @if($detail->tipe_penyajian)
                                        <br><small class="text-muted">Kemasan: {{ $detail->tipe_penyajian }}</small>
                                    @endif
                                </td>
                                <td class="align-middle text-muted small">
                                    @if($detail->menuItems && $detail->menuItems->count() > 0)
                                        @php
                                            $groupedTambahan = $detail->menuItems->groupBy('id')->map(function ($items) {
                                                return $items->first()->nama . ' (' . $items->count() . ')';
                                            })->implode(', ');
                                        @endphp
                                        {{ $groupedTambahan }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    {{ $detail->porsi }}
                                </td>
                                <td class="align-middle text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $pesanan->tipe_layanan === 'harian' ? '5' : '4' }}" class="text-center text-muted">Data menu tidak ditemukan.</td>
                            </tr>
                            @endforelse

                            @if($pesanan->detailPesanans && $pesanan->detailPesanans->whereNotNull('minuman_id')->count() > 0)
                                <tr>
                                    <td colspan="{{ $pesanan->tipe_layanan === 'harian' ? '5' : '4' }}" class="bg-light fw-bold text-success">
                                        <i class="fa-solid fa-mug-hot"></i> Minuman
                                    </td>
                                </tr>
                                @foreach($pesanan->detailPesanans->whereNotNull('minuman_id') as $minumanDetail)
                                <tr>
                                    @if($pesanan->tipe_layanan === 'harian')
                                    <td class="align-middle text-muted">
                                        {{ \Carbon\Carbon::parse($minumanDetail->tanggal_pengiriman)->translatedFormat('d F Y') }}
                                    </td>
                                    @endif
                                    <td class="align-middle fw-medium text-success">
                                        {{ $minumanDetail->minuman->nama_minuman }}
                                    </td>
                                    <td class="align-middle text-muted text-center">-</td>
                                    <td class="align-middle text-center text-success">
                                        {{ $minumanDetail->porsi }} Porsi
                                    </td>
                                    <td class="align-middle text-end fw-bold text-success">Rp {{ number_format($minumanDetail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @endif
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

        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Aksi Pesanan</h3>
            </div>
            <div class="card-body">
                {{-- Form Update Status --}}
                <form id="statusForm" action="{{ route('admin.pesanan.status', $pesanan) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group mb-3">
                        <label>Status Pesanan</label>
                        <select name="status_pesanan" class="form-select">
                            @foreach(['diproses'=>'Diproses','dibatalkan'=>'Dibatalkan','selesai'=>'Selesai'] as $k=>$v)
                            <option value="{{ $k }}" {{ $pesanan->status_pesanan==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3"><i class="fa-solid fa-sync"></i> Update Status</button>
                </form>


            </div>
        </div>
    </div>
</div>

@endsection
