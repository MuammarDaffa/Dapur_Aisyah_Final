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
        <!-- Tagihan Layout -->
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Tagihan: {{ $pesanan->nomor_pesanan }}</h3>
                <div class="ms-auto">
                    <span class="badge {{ match($pesanan->status) { 'diproses'=>'text-bg-info','dikirim'=>'text-bg-primary','selesai'=>'text-bg-success','dibatalkan'=>'text-bg-danger',default=>'text-bg-secondary' } }}">
                        {{ $pesanan->status_label }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row tagihan-info mb-4">
                    <div class="col-sm-4 tagihan-col">
                        Pelanggan
                        <address>
                            <strong>{{ $pesanan->user->name }}</strong><br>
                            Telepon: {{ $pesanan->user->phone }}<br>
                            Email: {{ $pesanan->user->email }}
                        </address>
                    </div>
                    <div class="col-sm-4 tagihan-col">
                        Detail Pesanan
                        <address>
                            <strong>{{ $pesanan->layanan->nama ?? '-' }}</strong> <small class="text-muted">({{ ucfirst($pesanan->layanan->tipe ?? '') }})</small><br>
                            Tgl Kirim: {{ $pesanan->tanggal_pesanan->format('d M Y') }}<br>
                            Metode: {{ ucfirst($pesanan->metode_pengambilan) }}
                            @if($pesanan->tipe_penyajian)
                                <br>Penyajian: {{ $pesanan->tipe_penyajian }}
                            @endif
                        </address>
                    </div>
                    @if($pesanan->metode_pengambilan === 'delivery')
                    <div class="col-sm-4 tagihan-col">
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
                                <th>Item</th>
                                <th class="text-center">Kuantitas x Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanan->items as $item)
                            <tr>
                                <td class="align-middle fw-medium">{{ $item->item_name }}</td>
                                <td class="align-middle text-center">
                                    {{ $item->jumlah }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-outline card-success mb-4">
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
                <p class="small text-muted mb-1">Metode: {{ ucfirst($pesanan->metode_pembayaran ?: 'Belum Dipilih') }}</p>
                <p class="small text-muted mb-1">Status: {{ ucfirst(str_replace('_', ' ', $pesanan->status_pembayaran)) }}</p>
                
                @if($pesanan->refund_status && $pesanan->refund_status !== 'none')
                    <div class="alert alert-{{ $pesanan->refund_status === 'pending' ? 'warning' : 'success' }} py-2 mt-3 mb-0">
                        <i class="fa-solid fa-{{ $pesanan->refund_status === 'pending' ? 'clock' : 'check' }}"></i>
                        Refund: {{ $pesanan->refund_status === 'pending' ? 'Menunggu Refund' : 'Sudah Direfund' }}
                    </div>
                @endif
            </div>
        </div>

        @if(!in_array($pesanan->status, ['selesai','dibatalkan']))
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Aksi Pesanan</h3>
            </div>
            <div class="card-body">
                {{-- Form Update Status --}}
                <form id="statusForm" action="{{ route('admin.pesanan.status', $pesanan) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group mb-3">
                        <label>Update Status</label>
                        <select name="status" class="form-select">
                            @foreach(['menunggu_pembayaran'=>'Menunggu Pembayaran', 'diproses'=>'Diproses','dikirim'=>'Sedang Dikirim','selesai'=>'Selesai'] as $k=>$v)
                            <option value="{{ $k }}" {{ $pesanan->status==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3"><i class="fa-solid fa-sync"></i> Update Status</button>
                </form>

                {{-- Tombol Batalkan --}}
                <form id="cancelForm" action="{{ route('admin.pesanan.cancel', $pesanan) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="alasan_pembatalan" id="cancelReasonInput">
                    <button type="button" onclick="confirmCancel()" class="btn btn-outline-danger w-100">
                        <i class="fa-solid fa-times"></i> Batalkan Pesanan
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
function confirmCancel() {
    Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Masukkan alasan pembatalan pesanan ini:',
        input: 'textarea',
        inputPlaceholder: 'Tulis alasan pembatalan...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value || value.trim().length < 5) {
                return 'Alasan pembatalan harus diisi (minimal 5 karakter).';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelReasonInput').value = result.value;
            document.getElementById('cancelForm').submit();
        }
    });
}
</script>
@endpush
@endsection
