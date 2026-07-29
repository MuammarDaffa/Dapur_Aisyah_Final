@extends('layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <h2 class="fw-bold mb-4 text-black">Riwayat Pesanan Saya</h2>

            @if($riwayatPesanan->isEmpty())
                <div class="alert alert-info text-center shadow-sm rounded-4 py-4">
                    belum ada pesanan.
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
                                            @if($pesanan->status === 'belum_bayar')
                                                <span class="badge bg-danger rounded-pill px-3 py-2">Belum Bayar</span>
                                            @elseif($pesanan->status === 'dp')
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">DP (Sudah Dibayar)</span>
                                                 <!-- FORM TOMBOL PELUNASAN -->
    <form class="form-pelunasan mt-1" action="{{ route('pelanggan.pelunasan', $pesanan->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 fw-bold btn-pelunasan shadow-sm">
            Bayar Pelunasan <br> (Rp {{ number_format($pesanan->sisa_pembayaran, 0, ',', '.') }})
        </button>
    </form>
                                            @elseif($pesanan->status === 'lunas')
                                                <span class="badge bg-success rounded-pill px-3 py-2">Lunas</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($pesanan->status) }}</span>
                                            @endif
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
            e.preventDefault(); // Mencegah pindah halaman

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
                    // PANGGIL MIDTRANS SNAP
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
