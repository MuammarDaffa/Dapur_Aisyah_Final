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
                <div class="mb-5">
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Pelanggan</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ $pesanan->user->name }}</div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Telepon</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ $pesanan->user->phone }}</div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Email</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1"><a href="mailto:{{ $pesanan->user->email }}" class="text-primary text-decoration-none">{{ $pesanan->user->email }}</a></div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Tipe Katering</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">Katering {{ ucfirst($pesanan->tipe_layanan) }} Kantoran</div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Tgl Kirim</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ $pesanan->tanggal_pesanan->format('d M Y') }}</div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Metode</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ ucwords(str_replace('_', ' ', $pesanan->metode_pengambilan)) }}</div>
                    </div>
                    @if($pesanan->metode_pengambilan === 'diantar_ke_tempat')
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Lokasi</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ $pesanan->alamat_lengkap ?? '-' }}</div>
                    </div>
                    @endif
                    @if($pesanan->catatan)
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Catatan</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ $pesanan->catatan }}</div>
                    </div>
                    @endif
                </div>

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
                                            @if($detail->tambahanLaukPauk->count() > 0)
                                                <ul class="list-unstyled ms-3 mb-0 small text-muted">
                                                    @foreach($detail->tambahanLaukPauk as $item)
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

        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Aksi Pesanan</h3>
            </div>
            <div class="card-body">
                @php
                    $isFinalStatus = in_array($pesanan->status_pesanan, ['selesai', 'dibatalkan']);
                @endphp

                {{-- Form Update Status --}}
                <form id="statusForm" action="{{ route('admin.pesanan.status', $pesanan) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group mb-3">
                        <label>Status Pesanan</label>
                        <select name="status_pesanan" id="statusPesananSelect" class="form-select" {{ $isFinalStatus ? 'disabled' : '' }}>
                            @foreach(['diproses'=>'Diproses','dibatalkan'=>'Dibatalkan','selesai'=>'Selesai'] as $k=>$v)
                            <option value="{{ $k }}" {{ $pesanan->status_pesanan==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group mb-3" id="kodePengambilanContainer" style="display: none;">
                        <label>Kode Pengambilan</label>
                        <input type="text" name="kode_pengambilan" class="form-control" placeholder="Masukkan kode pengambilan" {{ $isFinalStatus ? 'disabled' : '' }} value="{{ $pesanan->kode_pengambilan }}">
                        <!-- <small class="text-muted">Kode wajib dimasukkan untuk menyelesaikan pesanan.</small> -->
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" {{ $isFinalStatus ? 'disabled' : '' }}>Update Status</button>
                </form>


            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const statusSelect = document.getElementById('statusPesananSelect');
        const kodeContainer = document.getElementById('kodePengambilanContainer');

        function toggleKodeInput() {
            if (statusSelect.value === 'selesai') {
                kodeContainer.style.display = 'block';
            } else {
                kodeContainer.style.display = 'none';
            }
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', toggleKodeInput);
            toggleKodeInput(); // initial check
        }
    });
</script>
@endpush
