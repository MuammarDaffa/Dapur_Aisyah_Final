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
                                        <th class="py-3 text-center">Kode Pengambilan</th>
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
                                        <td class="py-3 text-center fw-bold">
                                            @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                                                {{ $pesanan->kode_pengambilan ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
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
                                                    <form action="{{ route('pelanggan.pesanan.hapus', $pesanan->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Apakah Anda yakin ingin menghapus pesanan ini?');">
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
                                                            @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                                                                <form action="{{ route('pelanggan.acara.batalkan', $pesanan->id) }}" method="POST" onsubmit="event.preventDefault(); confirmCancelForm(this, 'Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                                                                </form>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <!-- Aksi Harian -->
                                                        <a href="{{ route('pelanggan.harian.detail_pesanan', $pesanan->id) }}" class="btn btn-primary btn-sm">Lihat</a>
                                                    @endif

                                                    @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS && $pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_SELESAI && !$pesanan->ulasan)
                                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalPenilaian{{ $pesanan->id }}">
                                                            Penilaian
                                                        </button>
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
            
            <!-- Modals for Penilaian -->
            @foreach($riwayatPesanan as $pesanan)
                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS && $pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_SELESAI && !$pesanan->ulasan)
                <div class="modal fade" id="modalPenilaian{{ $pesanan->id }}" tabindex="-1" aria-labelledby="modalPenilaianLabel{{ $pesanan->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('pelanggan.ulasan.store') }}" method="POST" class="form-penilaian">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalPenilaianLabel{{ $pesanan->id }}">Beri Penilaian untuk Pesanan {{ $pesanan->nomor_pesanan }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">

                                    <div class="mb-3">
                                        <label for="komentar{{ $pesanan->id }}" class="form-label fw-bold">Ulasan</label>
                                        <textarea class="form-control" id="komentar{{ $pesanan->id }}" name="komentar" rows="4" placeholder="Bagaimana pengalaman Anda dengan layanan kami?" required></textarea>
                                        <div class="invalid-feedback">Komentar wajib diisi.</div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-kirim-ulasan">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
         
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

        // AJAX Form Submission for Penilaian
        $('.form-penilaian').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let btnKirim = form.find('.btn-kirim-ulasan');
            let originalText = btnKirim.html();
            btnKirim.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...').prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    btnKirim.html(originalText).prop('disabled', false);
                    
                    if (response.status === 'success') {
                        // Close modal
                        let modalId = form.closest('.modal').attr('id');
                        let modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                        modal.hide();
                        
                        // SweetAlert Success
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#198754'
                        }).then((result) => {
                            // Reload to update the button state to "Lihat Ulasan"
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    btnKirim.html(originalText).prop('disabled', false);
                    let errMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        if(errors.komentar) errMsg = errors.komentar[0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errMsg,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
