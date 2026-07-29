@extends('layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-3">Selesaikan Pembayaran DP Anda</h2>
                    <p class="text-muted mb-4">
                        Nomor Pesanan: <strong>{{ $pesanan->nomor_pesanan }}</strong><br>
                        Silakan selesaikan pembayaran DP Anda (50%) sebesar:
                    </p>
                    <h1 class="text-success fw-bold mb-4">Rp {{ number_format($pesanan->jumlah_dp, 0, ',', '.') }}</h1>
                    
                    <button id="pay-button" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow-sm rounded-pill">
                        Pilih Metode Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Snap Midtrans -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        // SnapToken didapat dari controller
        snap.pay('{{ $snapToken }}', {
            // Optional: Jika user sukses bayar
            onSuccess: function(result){
                alert("Pembayaran berhasil!");
                // Nanti kita akan ubah ini untuk memproses status di backend
                window.location.href = "{{ route('landing') }}"; 
            },
            // Optional: Jika pembayaran di-pending (misal Transfer Bank)
            onPending: function(result){
                alert("Menunggu pembayaran Anda!");
                window.location.href = "{{ route('landing') }}";
            },
            // Optional: Jika pembayaran gagal
            onError: function(result){
                alert("Pembayaran gagal!");
            }
        });
    };
</script>
@endsection
