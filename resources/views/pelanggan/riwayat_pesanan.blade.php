@extends('layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <h2 class="fw-bold mb-4 text-black">Riwayat Pesanan Saya</h2>

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
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 px-4">No. Pesanan</th>
                                        <th class="py-3">Tanggal Dibuat</th>
                                        <th class="py-3 text-end">Total</th>
                                        <th class="py-3 text-end">DP Dibayar</th>
                                        <th class="py-3 text-center">Status</th>
                                        <th class="py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatPesanan as $pesanan)
                                    <tr>
                                        <td class="py-3 px-4 fw-bold text-primary">{{ $pesanan->nomor_pesanan }}</td>
                                        <td class="py-3 text-start">{{ $pesanan->created_at->format('d M Y') }}</td>
                                        <td class="py-3 text-end">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                        <td class="py-3 text-end">Rp {{ number_format($pesanan->jumlah_dp, 0, ',', '.') }}</td>
                                        <td class="py-3 text-center">
                                            <span class="badge bg-{{ $pesanan->status_pembayaran_color }} rounded-pill px-3 py-2 mb-1">{{ $pesanan->status_pembayaran_label }}</span>
                                            <br>
                                            <span class="badge bg-{{ $pesanan->status_pesanan_color }} rounded-pill px-3 py-2">{{ $pesanan->status_pesanan_label }}</span>
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <a href="{{ route('pelanggan.acara.detail_pesanan', $pesanan->id) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-bold">Lihat</a>
                                                
                                                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DP)
                                                    <form class="form-pelunasan w-100" action="{{ route('pelanggan.pelunasan', $pesanan->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill w-100 fw-bold btn-pelunasan shadow-sm">
                                                            Pelunasan
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <form action="{{ route('pelanggan.acara.batalkan', $pesanan->id) }}" method="POST" class="w-100" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100 fw-bold" {{ $pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS || $pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_DIBATALKAN ? 'disabled' : '' }}>
                                                        Batalkan
                                                    </button>
                                                </form>
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
@endsection
