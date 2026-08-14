{{-- 
=======================================
File : resources/views/admin/catering/show.blade.php
Fungsi : Menampilkan form edit katering dan manajemen menu (jika katering acara) dalam satu halaman.
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Katering Acara Kantor    ')

@section('content')



<div class="row mt-4">
    <!-- Card 1: Menu Makanan -->
    <div class="col-md-12 mb-4">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">Menu Makanan</h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.menu.create', 'acara') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Tambah Menu
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Menu</th>
                                <th width="15%" class="text-center">Gambar</th>
                                <th>Deskripsi</th>
                                <th width="15%" class="text-center">Harga</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus ?? [] as $index => $menu)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $menu->nama_menu }}</strong>
                                </td>
                                <td class="text-center">
                                    @if($menu->gambar)
                                        <img src="{{ asset('storage/menu/' . $menu->gambar) }}" alt="{{ $menu->nama_menu }}" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <span class="text-muted small">Tidak ada gambar</span>
                                    @endif
                                </td>
                                <td>{{ $menu->deskripsi }}</td>
                                <td class="text-center">
                                    <span class="text-muted">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="hapusData(this.form)">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada menu makanan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-success">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">Manajemen Minuman</h3>
                <div class="ms-auto">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#tambahMinumanModal">
                        <i class="bi bi-plus-lg"></i> Tambah Minuman
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Minuman</th>
                                <th>Harga</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($minumans ?? [] as $index => $minuman)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $minuman->nama_minuman }}</strong>
                                </td>
                                <td>
                                    Rp {{ number_format($minuman->harga, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit-minuman" 
                                            data-id="{{ $minuman->id }}" 
                                            data-nama="{{ $minuman->nama_minuman }}"
                                            data-harga="{{ $minuman->harga }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editMinumanModal">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.minuman.destroy', $minuman->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="hapusData(this.form)">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada minuman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>





<!-- Modal Tambah Minuman -->
<div class="modal fade" id="tambahMinumanModal" tabindex="-1" aria-labelledby="tambahMinumanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.minuman.store', 'acara') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahMinumanModalLabel">Tambah Minuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_minuman" class="form-label">Nama Minuman <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_minuman" name="nama_minuman" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga per Cup/Gelas (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga" name="harga" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Minuman -->
<div class="modal fade" id="editMinumanModal" tabindex="-1" aria-labelledby="editMinumanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditMinuman" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMinumanModalLabel">Edit Minuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_minuman" class="form-label">Nama Minuman <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_minuman" name="nama_minuman" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="edit_harga" class="form-label">Harga per Cup/Gelas (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_harga" name="harga" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    $('#summernote').summernote({
        placeholder: 'Tulis deskripsi layanan katering di sini...',
        tabsize: 2,
        height: 250,
        toolbar: [
            ['font', ['bold', 'italic', 'strikethrough']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['misc', ['undo', 'redo', 'codeview']]
        ]
    });

    // Handle Edit Minuman Modal
    $('.btn-edit-minuman').click(function() {
        var minumanId = $(this).data('id');
        var minumanNama = $(this).data('nama');
        var minumanHarga = $(this).data('harga');
        
        // Isi input
        $('#edit_nama_minuman').val(minumanNama);
        $('#edit_harga').val(minumanHarga);
        
        // Ubah action form ke route update yang benar
        var formAction = "{{ url('admin/minuman') }}/" + minumanId;
        $('#formEditMinuman').attr('action', formAction);
    });
});

function hapusData(form) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>

@if(session('swal_success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: '{{ session('swal_success') }}',
            icon: 'success',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    });
</script>
@endif
@endpush
