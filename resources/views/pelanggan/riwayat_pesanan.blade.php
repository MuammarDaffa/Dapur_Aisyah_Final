@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* Yummy Clean Section Title */
    .section-title {
        text-align: center;
        padding-bottom: 30px;
    }
    .section-title h2 {
        font-size: 13px;
        letter-spacing: 1px;
        font-weight: 400;
        margin: 0;
        padding: 0;
        color: #7f7f90;
        text-transform: uppercase;
        font-family: "Inter", sans-serif;
    }
    .section-title p {
        margin: 0;
        font-size: 48px;
        font-weight: 700;
        font-family: "Amatic SC", sans-serif;
        color: #37373f;
    }
    .section-title p span {
        color: #ce1212;
    }

    /* Minimalist Card */
    .yummy-card {
        background: #fff;
        border: none;
        border-radius: 8px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
        padding: 40px;
    }

    /* Minimalist Table */
    .table-modern {
        border-collapse: collapse;
    }
    .table-modern thead th {
        background: transparent;
        color: #8a8a8a;
        border-bottom: 2px solid #f2f2f2;
        padding: 15px 10px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }
    .table-modern tbody td {
        border-bottom: 1px solid #f2f2f2;
        padding: 15px 10px;
        vertical-align: middle;
        color: #37373f;
    }
    .table-modern tbody tr:hover td {
        background-color: #fafafa;
    }
    
    /* Datatable Pagination & Filter Yummy Style */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #ce1212 !important;
        color: white !important;
        border: none;
        border-radius: 4px;
    }
    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 5px 10px;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #ce1212;
        outline: none;
    }

    /* Badges */
    .badge-yummy {
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
    }
    .badge-status {
        background-color: #f8d7da;
        color: #ce1212;
    }
    
    /* Buttons */
    .btn-yummy-outline {
        border: 1px solid #ce1212;
        color: #ce1212;
        background: transparent;
        border-radius: 50px;
        padding: 5px 15px;
        font-size: 13px;
        transition: 0.3s;
    }
    .btn-yummy-outline:hover {
        background: #ce1212;
        color: white;
    }
    .btn-yummy {
        background: #ce1212;
        color: white;
        border: 1px solid #ce1212;
        border-radius: 50px;
        padding: 5px 15px;
        font-size: 13px;
        transition: 0.3s;
    }
    .btn-yummy:hover {
        background: transparent;
        color: #ce1212;
    }

    body {
        background-color: #f2f2f2;
    }
</style>
@endpush

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    
    <div class="section-title">
        <h2>Aktivitas Anda</h2>
        <p>Riwayat <span>Pesanan</span></p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            @if($riwayatPesanan->isEmpty())
                <div class="yummy-card text-center py-5">
                    <div class="mb-4">
                        <i class="fa-solid fa-clipboard-list" style="font-size: 60px; color: #ce1212;"></i>
                    </div>
                    <h3 class="fw-bold mb-3" style="color: #37373f;">Belum Ada Pesanan</h3>
                    <p class="text-muted mb-4">Anda belum memiliki riwayat pesanan. Yuk, mulai pesan makanan lezat dari Dapur Aisyah!</p>
                    <a href="{{ route('landing') }}#menu" class="btn-yummy text-decoration-none px-4 py-2" style="font-size: 15px;">
                        Lihat Menu
                    </a>
                </div>
            @else
                <div class="yummy-card">
                    <div class="table-responsive">
                        <table class="table table-modern w-100" id="tabelRiwayat">
                            <thead>
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Pembayaran</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatPesanan as $pesanan)
                                @php
                                    $tipeLayanan = ucfirst($pesanan->tipe_layanan);
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $pesanan->nomor_pesanan }}</td>
                                    <td data-sort="{{ $pesanan->created_at->format('YmdHis') }}">{{ $pesanan->created_at->format('d M Y') }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $tipeLayanan }}</span></td>
                                    <td class="text-end fw-bold" style="color: #ce1212;">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-yummy bg-{{ $pesanan->status_pembayaran_color }}">{{ $pesanan->status_pembayaran_label }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(!is_null($pesanan->status_pesanan))
                                            <span class="badge badge-yummy bg-{{ $pesanan->status_pesanan_color }}">{{ $pesanan->status_pesanan_label }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)
                                                <a href="{{ route(strtolower($tipeLayanan) === 'acara' ? 'pelanggan.acara.detail_pesanan' : 'pelanggan.harian.detail_pesanan', $pesanan->id) }}" class="btn-yummy-outline">Lihat</a>
                                                <form action="{{ route('pelanggan.pesanan.hapus', $pesanan->id) }}" method="POST" class="d-inline form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn-hapus">Hapus</button>
                                                </form>
                                            @else
                                                <a href="{{ route(strtolower($tipeLayanan) === 'acara' ? 'pelanggan.acara.detail_pesanan' : 'pelanggan.harian.detail_pesanan', $pesanan->id) }}" class="btn-yummy-outline">Lihat</a>
                                                
                                                @if(strtolower($tipeLayanan) === 'acara')
                                                    @if($pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_DIPROSES)
                                                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DP)
                                                            <form class="form-pelunasan d-inline" action="{{ route('pelanggan.pelunasan', $pesanan->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm rounded-pill btn-pelunasan">Pelunasan</button>
                                                            </form>
                                                        @endif
                                                        @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                                                            <form action="{{ route('pelanggan.acara.batalkan', $pesanan->id) }}" method="POST" class="d-inline form-cancel">
                                                                @csrf
                                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn-batalkan">Batal</button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                @endif

                                                @if($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS && $pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_SELESAI && !$pesanan->ulasan)
                                                    <button type="button" class="btn btn-warning btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalPenilaian{{ $pesanan->id }}">
                                                        Nilai
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
                        <div class="modal-content rounded-3 border-0 shadow">
                            <form action="{{ route('pelanggan.ulasan.store') }}" method="POST" class="form-penilaian">
                                @csrf
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold" style="color: #37373f;">Beri Penilaian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">
                                    <p class="text-muted small mb-3">Bagaimana pengalaman Anda dengan pesanan #{{ $pesanan->nomor_pesanan }}?</p>
                                    <div class="mb-3">
                                        <textarea class="form-control" name="komentar" rows="4" placeholder="Tuliskan ulasan Anda di sini..." required style="border-radius: 8px;"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn-yummy btn-kirim-ulasan">Kirim Ulasan</button>
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
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', confirmButtonColor: '#ce1212' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', confirmButtonColor: '#dc3545' });
        @endif
        @if(session('info'))
            Swal.fire({ icon: 'info', title: 'Informasi', text: '{{ session('info') }}', confirmButtonColor: '#ce1212' });
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
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Pelunasan berhasil!', confirmButtonColor: '#ce1212' })
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
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, confirmButtonColor: '#ce1212' })
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
