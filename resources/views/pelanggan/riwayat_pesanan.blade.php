@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <h2 class="fw-bold mb-4 text-black">Riwayat Pesanan Katering</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($riwayatPesanan->isEmpty())
                <div class="alert alert-info text-center shadow-sm rounded-4 py-4">
                    Belum ada pesanan.
                </div>
            @else
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive p-3">
                            <table class="table table-hover align-middle mb-0 w-100" id="tabelRiwayat">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 px-4">No. Pesanan</th>
                                        <th class="py-3">Tanggal Pesanan</th>
                                        <th class="py-3">Tipe Katering</th>
                                        <th class="py-3 text-end">Total</th>
                                        <th class="py-3 text-center">Status Pembayaran</th>
                                        <th class="py-3 text-center">Status Pesanan</th>
                                        <th class="py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatPesanan as $pesanan)
                                    @php
                                        // Deteksi tipe katering
                                        $tipeLayanan = ucfirst($pesanan->tipe_layanan);
                                    @endphp
                                    <tr>
                                        <td class="py-3 px-4 fw-bold ">{{ $pesanan->nomor_pesanan }}</td>
                                        <td class="py-3 text-start" data-sort="{{ $pesanan->created_at->format('YmdHis') }}">{{ $pesanan->created_at->format('d M Y') }}</td>
                                        <td class="py-3">{{ $tipeLayanan }}</td>
                                        <td class="py-3 text-end">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                        <td class="py-3 text-center">
                                            <span class="badge bg-{{ $pesanan->status_pembayaran_color }}">{{ $pesanan->status_pembayaran_label }}</span>
                                        </td>
                                        <td class="py-3 text-center">
                                            @if(!is_null($pesanan->status_pesanan))
                                                <span class="badge bg-{{ $pesanan->status_pesanan_color }}">{{ $pesanan->status_pesanan_label }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                                                    <a href="{{ route(strtolower($tipeLayanan) === 'acara' ? 'pelanggan.acara.detail_pesanan' : 'pelanggan.harian.detail_pesanan', $pesanan->id) }}" class="btn btn-primary btn-sm">Lihat</a>
                                                    <form action="{{ route('pelanggan.pesanan.hapus', $pesanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                    </form>
                                                @else
                                                    @if(strtolower($tipeLayanan) === 'acara')
                                                        <!-- Aksi Acara -->
                                                        <a href="{{ route('pelanggan.acara.detail_pesanan', $pesanan->id) }}" class="btn btn-primary btn-sm">Lihat</a>
                                                        
                                                        @if($pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_DIPROSES)
                                                            @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DP)
                                                                <form class="form-pelunasan" action="{{ route('pelanggan.pelunasan', $pesanan->id) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm btn-pelunasan">Pelunasan</button>
                                                                </form>
                                                            @endif
                                                            
                                                            <form action="{{ route('pelanggan.acara.batalkan', $pesanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                                                            </form>
                                                        @endif
                                                    @else
                                                        <!-- Aksi Harian -->
                                                        <a href="{{ route('pelanggan.harian.detail_pesanan', $pesanan->id) }}" class="btn btn-primary btn-sm">Lihat</a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
         
        </div>
    </div>
</div>

<!-- Script Snap Midtrans -->
@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.querySelectorAll('.form-pelunasan').forEach(function(form) {
        form.addEventListener('submit', function(e){
            e.preventDefault();

            const btnBayar = form.querySelector('.btn-pelunasan');
            const originalText = btnBayar.innerHTML;
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
                btnBayar.innerHTML = originalText;
                btnBayar.disabled = false;

                if(data.status === 'success'){
                    snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            alert("Pelunasan berhasil!");
                            window.location.reload(); 
                        },
                        onPending: function(result){
                            alert("Menunggu pembayaran Anda!");
                            window.location.reload();
                        },
                        onError: function(result){
                            alert("Pembayaran gagal!");
                        },
                        onClose: function(){
                            alert('Anda menutup jendela tanpa menyelesaikan pembayaran.');
                        }
                    });
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btnBayar.innerHTML = originalText;
                btnBayar.disabled = false;
                alert('Gagal menghubungi server.');
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelRiwayat').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            },
            responsive: true,
            order: [[1, 'desc']], // Urutkan berdasarkan tanggal dibuat (terbaru)
            columnDefs: [
                { orderable: false, targets: 6 } // Disable sorting on Action column
            ]
        });
    });
</script>
@endpush
@endsection
