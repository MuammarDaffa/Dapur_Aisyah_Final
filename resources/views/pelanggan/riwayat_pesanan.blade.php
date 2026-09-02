@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .page-header {
        position: relative;
        padding: 80px 0 40px;
        background-color: var(--bg-cream);
        overflow: hidden;
    }
    
    .blob-header {
        position: absolute;
        top: -50px; left: -10%;
        width: 400px; height: 400px;
        background: var(--primary-terracotta);
        opacity: 0.05;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        animation: morph 15s ease-in-out infinite alternate;
        z-index: 0;
    }

    .bento-box {
        background: white;
        border-radius: var(--bento-radius);
        box-shadow: var(--soft-shadow);
        padding: 30px;
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(0,0,0,0.03);
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table-modern thead th {
        background: rgba(0,0,0,0.02);
        color: var(--text-dark);
        border: none;
        padding: 15px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
    }

    .table-modern tbody tr {
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }

    .table-modern tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .table-modern tbody td {
        border: none;
        padding: 15px;
        vertical-align: middle;
    }

    .table-modern tbody td:first-child {
        border-radius: 12px 0 0 12px;
    }

    .table-modern tbody td:last-child {
        border-radius: 0 12px 12px 0;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-terracotta) !important;
        color: white !important;
        border: none;
        border-radius: 50%;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 5px 15px;
        border: 1px solid #dee2e6;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border-radius: 20px;
        padding: 5px 30px 5px 15px;
        border: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="blob-header"></div>
    <div class="container position-relative z-1">
        <div class="text-center mb-4">
            <span class="d-inline-flex align-items-center gap-2 bg-white rounded-pill px-4 py-2 shadow-sm mb-3 border border-light">
                <span class="bg-primary-mc rounded-circle" style="width: 8px; height: 8px;"></span>
                <span class="fw-bold text-secondary small text-uppercase tracking-wider">Aktivitas Anda</span>
            </span>
            <h1 class="display-4 fw-bold text-dark mb-2">
                Riwayat <span style="color: var(--primary-terracotta); font-style: italic;">Pesanan</span>
            </h1>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-4 pb-5 mt-n4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">

            @if($riwayatPesanan->isEmpty())
                <div class="bento-box text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                        <i class="fa-solid fa-clipboard-list fs-1 text-secondary"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-3">Belum Ada Pesanan</h3>
                    <p class="text-secondary mb-4">Anda belum memiliki riwayat pesanan. Yuk, mulai pesan makanan lezat dari Dapur Aisyah!</p>
                    <a href="{{ route('landing') }}#menu" class="btn btn-primary-mc rounded-pill px-5 py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-utensils me-2"></i> Lihat Menu
                    </a>
                </div>
            @else
                <div class="bento-box p-4 p-md-5">
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0 w-100" id="tabelRiwayat">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4">No. Pesanan</th>
                                    <th class="py-3">Tanggal</th>
                                    <th class="py-3">Tipe</th>
                                    <th class="py-3 text-end">Total</th>
                                    <th class="py-3 text-center">Status Pembayaran</th>
                                    <th class="py-3 text-center">Status Pesanan</th>
                                    <th class="py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatPesanan as $pesanan)
                                @php
                                    $tipeLayanan = ucfirst($pesanan->tipe_layanan);
                                @endphp
                                <tr>
                                    <td class="py-3 px-4 fw-bold text-dark">{{ $pesanan->nomor_pesanan }}</td>
                                    <td class="py-3 text-start fw-semibold" data-sort="{{ $pesanan->created_at->format('YmdHis') }}">{{ $pesanan->created_at->format('d M Y') }}</td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ $tipeLayanan }}</span>
                                    </td>
                                    <td class="py-3 text-end fw-bold text-primary-mc">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-{{ $pesanan->status_pembayaran_color }} rounded-pill px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ $pesanan->status_pembayaran_label }}</span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if(!is_null($pesanan->status_pesanan))
                                            <span class="badge bg-{{ $pesanan->status_pesanan_color }} rounded-pill px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ $pesanan->status_pesanan_label }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                                                <a href="{{ route(strtolower($tipeLayanan) === 'acara' ? 'pelanggan.acara.detail_pesanan' : 'pelanggan.harian.detail_pesanan', $pesanan->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-eye me-1"></i> Lihat</a>
                                                <form action="{{ route('pelanggan.pesanan.hapus', $pesanan->id) }}" method="POST" class="d-inline form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1 fw-bold btn-hapus"><i class="fa-solid fa-trash me-1"></i> Hapus</button>
                                                </form>
                                            @else
                                                <a href="{{ route(strtolower($tipeLayanan) === 'acara' ? 'pelanggan.acara.detail_pesanan' : 'pelanggan.harian.detail_pesanan', $pesanan->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-eye me-1"></i> Lihat</a>
                                                
                                                @if(strtolower($tipeLayanan) === 'acara')
                                                    @if($pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_DIPROSES)
                                                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DP)
                                                            <form class="form-pelunasan d-inline" action="{{ route('pelanggan.pelunasan', $pesanan->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-bold btn-pelunasan"><i class="fa-solid fa-money-bill-wave me-1"></i> Pelunasan</button>
                                                            </form>
                                                        @endif
                                                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                                                            <form action="{{ route('pelanggan.acara.batalkan', $pesanan->id) }}" method="POST" class="d-inline form-cancel">
                                                                @csrf
                                                                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1 fw-bold btn-batalkan"><i class="fa-solid fa-ban me-1"></i> Batalkan</button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                @endif

                                                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS && $pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_SELESAI && !$pesanan->ulasan)
                                                    <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 py-1 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalPenilaian{{ $pesanan->id }}">
                                                        <i class="fa-solid fa-star me-1"></i> Nilai
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
            @endif
            
            <!-- Modals for Penilaian -->
            @foreach($riwayatPesanan as $pesanan)
                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS && $pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_SELESAI && !$pesanan->ulasan)
                <div class="modal fade" id="modalPenilaian{{ $pesanan->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <form action="{{ route('pelanggan.ulasan.store') }}" method="POST" class="form-penilaian">
                                @csrf
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-star text-warning me-2"></i> Beri Penilaian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">
                                    <p class="text-secondary small mb-3">Bagaimana pengalaman Anda dengan pesanan #{{ $pesanan->nomor_pesanan }}?</p>
                                    <div class="mb-3">
                                        <textarea class="form-control bg-light border-0 py-3" id="komentar{{ $pesanan->id }}" name="komentar" rows="4" placeholder="Tuliskan ulasan Anda di sini..." required style="border-radius: 12px;"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary-mc rounded-pill px-4 fw-bold btn-kirim-ulasan">Kirim Ulasan</button>
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
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', confirmButtonColor: '#2C4A3B' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', confirmButtonColor: '#dc3545' });
        @endif
        @if(session('info'))
            Swal.fire({ icon: 'info', title: 'Informasi', text: '{{ session('info') }}', confirmButtonColor: '#2C4A3B' });
        @endif
    });

    $(document).ready(function() {
        $('#tabelRiwayat').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
            responsive: true,
            order: [[1, 'desc']], 
            columnDefs: [{ orderable: false, targets: 6 }],
            dom: '<"row align-items-center mb-4"<"col-md-6"l><"col-md-6"f>>rt<"row align-items-center mt-4"<"col-md-6"i><"col-md-6"p>>'
        });

        // Hapus Pesanan
        $('.btn-hapus').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Pesanan?',
                text: "Anda tidak dapat mengembalikan pesanan yang telah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Batalkan Pesanan
        $('.btn-batalkan').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Batalkan Pesanan?',
                text: "Apakah Anda yakin ingin membatalkan pesanan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Pelunasan Midtrans
        $('.form-pelunasan').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let btnBayar = form.find('.btn-pelunasan');
            let originalText = btnBayar.html();
            let url = form.attr('action');
            let csrfToken = form.find('input[name="_token"]').val();

            btnBayar.html('<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Proses...').prop('disabled', true);

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
                btnBayar.html(originalText).prop('disabled', false);
                if(data.status === 'success'){
                    snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Pelunasan berhasil!', confirmButtonColor: '#2C4A3B' })
                            .then(() => window.location.reload());
                        },
                        onPending: function(result){
                            Swal.fire({ icon: 'info', title: 'Menunggu', text: 'Menunggu pembayaran Anda!', confirmButtonColor: '#f97316' })
                            .then(() => window.location.reload());
                        },
                        onError: function(result){
                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Pembayaran gagal!', confirmButtonColor: '#dc3545' });
                        },
                        onClose: function(){
                            Swal.fire({ icon: 'warning', title: 'Batal', text: 'Anda menutup jendela tanpa menyelesaikan pembayaran.', confirmButtonColor: '#f97316' });
                        }
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan: ' + data.message, confirmButtonColor: '#dc3545' });
                }
            })
            .catch(error => {
                btnBayar.html(originalText).prop('disabled', false);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.', confirmButtonColor: '#dc3545' });
            });
        });

        // Ulasan
        $('.form-penilaian').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let btnKirim = form.find('.btn-kirim-ulasan');
            let originalText = btnKirim.html();
            btnKirim.html('<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Mengirim...').prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    btnKirim.html(originalText).prop('disabled', false);
                    if (response.status === 'success') {
                        $('.modal').modal('hide');
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, confirmButtonColor: '#2C4A3B' })
                        .then(() => window.location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: response.message, confirmButtonColor: '#dc3545' });
                    }
                },
                error: function(xhr) {
                    btnKirim.html(originalText).prop('disabled', false);
                    let errMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    if(xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.komentar) {
                        errMsg = xhr.responseJSON.errors.komentar[0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Oops...', text: errMsg, confirmButtonColor: '#dc3545' });
                }
            });
        });
    });
</script>
@endpush
