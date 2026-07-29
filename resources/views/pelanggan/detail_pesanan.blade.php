@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="fs-3 fw-bold text-dark mt-2 mb-4">Detail Pesanan Anda</h2>

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

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold fs-5">
                    Ringkasan Acara
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 200px;">Layanan Katering</td>
                            <td class="fw-semibold">{{ $layanan->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Acara</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($draft['tanggal_acara'])->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode Pengambilan</td>
                            <td class="fw-semibold">
                                @if($draft['metode_pengambilan'] == 'diantar_ke_tempat')
                                    Di Antar ke Lokasi
                                @else
                                    Ambil Sendiri
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe Penyajian</td>
                            <td class="fw-semibold">{{ $draft['tipe_penyajian'] ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @foreach($menusDetail as $detail)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold fs-5">
                        Detail Menu
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">{{ $detail['menu']->nama_menu }}</h6>
                        
                        @if($detail['selectedItems']->count() > 0)
                            <ul class="list-group list-group-flush mb-3">
                                @foreach($detail['selectedItems'] as $item)
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span>&bull; {{ $item->nama }}</span>
                                        @if($item->harga > 0)
                                            <span class="text-muted">+ Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="d-flex justify-content-between text-dark mb-2">
                            <span class="text-muted">Jumlah Porsi</span>
                            <span class="fw-bold">{{ $detail['porsi'] }} porsi</span>
                        </div>
                        <div class="d-flex justify-content-between text-dark">
                            <span class="text-muted">Subtotal Menu</span>
                            <span class="fw-bold">Rp {{ number_format($detail['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

           <div class="card shadow-sm border-0 mb-5 bg-light">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Total Keseluruhan</span>
            <span class="fw-bold fs-5">Rp {{ number_format($draft['total'], 0, ',', '.') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Sisa Pelunasan (Dibayar Nanti)</span>
            <span class="fw-bold fs-5 text-danger">Rp {{ number_format($draft['total'] * 0.5, 0, ',', '.') }}</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="text-muted mb-0">DP yang Harus Dibayar (50%)</p>
                <h3 class="fw-bold text-success mb-0">Rp {{ number_format($draft['total'] * 0.5, 0, ',', '.') }}</h3>
            </div>
                   <form id="form-bayar" action="{{ route('pelanggan.acara.bayar') }}" method="POST">
             @csrf
             <button id="btn-bayar" type="submit" class="btn btn-primary btn-lg px-5 shadow-sm fw-bold">Bayar DP Sekarang</button>
         </form>

        </div>
    </div>
</div>


            <div class="d-flex justify-content-start mb-5">
                <a href="{{ route('pelanggan.acara.pilih_menu') }}" class="btn btn-secondary px-5 py-2 fw-bold shadow-sm">
                    Kembali
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Script Snap Midtrans -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('form-bayar').addEventListener('submit', function(e){
        e.preventDefault(); // Mencegah browser pindah halaman

        const form = this;
        const btnBayar = document.getElementById('btn-bayar');
        const url = form.action;
        const csrfToken = form.querySelector('input[name="_token"]').value;

        // Ubah tombol jadi loading
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
            // Kembalikan tombol seperti semula
            btnBayar.innerHTML = 'Bayar DP Sekarang';
            btnBayar.disabled = false;

            if(data.status === 'success'){
                // PANGGIL MIDTRANS SNAP
                snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        alert("Pembayaran berhasil!");
                        window.location.href = "{{ route('landing') }}"; 
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran Anda!");
                        window.location.href = "{{ route('landing') }}";
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
            btnBayar.innerHTML = 'Bayar DP Sekarang';
            btnBayar.disabled = false;
            alert('Gagal menghubungi server.');
        });
    });
</script>


@endsection
