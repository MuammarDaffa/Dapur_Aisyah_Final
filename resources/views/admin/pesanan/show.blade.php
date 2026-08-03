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
                            <strong>{{ $pesanan->layanan->nama ?? '-' }}</strong> <small class="text-muted">({{ ucfirst($pesanan->layanan->tipe ?? '') }})</small><br>
                            @if(!$pesanan->layanan->isHarian())
                                Tgl Kirim: {{ $pesanan->tanggal_pesanan->format('d M Y') }}<br>
                            @endif
                            Metode: {{ ucfirst($pesanan->metode_pengambilan) }}
                        </address>
                    </div>
                    @if($pesanan->metode_pengambilan === 'delivery')
                    <div class="col-sm-4 detail-col">
                        Alamat Pengiriman
                        <address>
                            {{ $pesanan->detail_alamat ?: '-' }}<br>
                            @if($pesanan->latitude && $pesanan->longitude)
                                Koordinat: {{ $pesanan->latitude }}, {{ $pesanan->longitude }}
                            @endif
                        </address>
                    </div>
                    @endif
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

                <h4 class="mb-3">Item Pesanan</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                @if($pesanan->layanan->isHarian())
                                <th>Tanggal Pengiriman</th>
                                @endif
                                <th>Item</th>
                                <th class="text-center">Porsi x Subtotal</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan->detailPesanans as $detail)
                            <tr>
                                @if($pesanan->layanan->isHarian())
                                <td class="align-middle text-muted">
                                    {{ \Carbon\Carbon::parse($detail->tanggal_pengiriman)->translatedFormat('d F Y') }}
                                </td>
                                @endif
                                <td class="align-middle fw-medium">
                                    {{ $detail->menu->nama_menu ?? '-' }}
                                    @if($detail->tipe_penyajian)
                                        <br><small class="text-muted">Kemasan: {{ $detail->tipe_penyajian }}</small>
                                    @endif
                                    @if($detail->menuItems && $detail->menuItems->count() > 0)
                                        <ul class="mb-0 mt-1 ps-3 text-muted small">
                                            @foreach($detail->menuItems as $item)
                                                <li>{{ $item->nama }} (+Rp {{ number_format($item->harga, 0, ',', '.') }})</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    {{ $detail->porsi }} x Rp {{ number_format($detail->subtotal / $detail->porsi, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $pesanan->layanan->isHarian() ? '4' : '3' }}" class="text-center text-muted">Data menu tidak ditemukan.</td>
                            </tr>
                            @endforelse

                            @if($pesanan->detailPesanans && $pesanan->detailPesanans->whereNotNull('minuman_id')->count() > 0)
                                <tr>
                                    <td colspan="{{ $pesanan->layanan->isHarian() ? '4' : '3' }}" class="bg-light fw-bold text-success">
                                        <i class="fa-solid fa-mug-hot"></i> Minuman
                                    </td>
                                </tr>
                                @foreach($pesanan->detailPesanans->whereNotNull('minuman_id') as $minumanDetail)
                                <tr>
                                    @if($pesanan->layanan->isHarian())
                                    <td class="align-middle text-muted">
                                        {{ \Carbon\Carbon::parse($minumanDetail->tanggal_pengiriman)->translatedFormat('d F Y') }}
                                    </td>
                                    @endif
                                    <td class="align-middle fw-medium text-success">
                                        {{ $minumanDetail->minuman->nama_minuman }}
                                    </td>
                                    <td class="align-middle text-center text-success">
                                        {{ $minumanDetail->porsi }} x Rp {{ number_format($minumanDetail->minuman->harga, 0, ',', '.') }}
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
