{{-- 
=======================================
File : resources/views/admin/catering/show.blade.php
Fungsi : Menampilkan form edit katering dan manajemen menu (jika katering acara) dalam satu halaman.
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Lihat Katering')

@section('content')



<div class="row mt-4">
    <!-- Card 1: Menu Makanan -->
    <div class="col-md-12 mb-4">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">Menu Makanan</h3>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahMenuModal">
                        <i class="bi bi-plus-lg"></i> Tambah Menu
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Menu</th>
                                <th width="20%" class="text-center">Harga</th>
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
                                    <!-- <a href="{{ route('admin.menu.items.index', $menu->id) }}" class="btn btn-info btn-sm text-white">
                                        Kelola Isi Menu
                                    </a> -->
                                    <span class="text-muted">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit-menu" 
                                            data-id="{{ $menu->id }}" 
                                            data-nama="{{ $menu->nama_menu }}"
                                            data-deskripsi="{{ $menu->deskripsi }}"
                                            data-harga="{{ $menu->harga }}"
                                            data-kategori="{{ $menu->kategori_penyajian }}"
                                            data-status="{{ $menu->status }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editMenuModal">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus menu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
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
                                    <form action="{{ route('admin.minuman.destroy', $minuman->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus minuman ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
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



<!-- Modal Tambah Menu -->
<div class="modal fade" id="tambahMenuModal" tabindex="-1" aria-labelledby="tambahMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.menu.store', 'acara') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahMenuModalLabel">Tambah Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_menu" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_menu" name="nama_menu" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Menu</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga per Porsi (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga" name="harga" required min="0">
                    </div>
                    <div class="mb-3">
                        <label for="kategori_penyajian" class="form-label">Kategori Menu Acara <span class="text-danger">*</span></label>
                        <select name="kategori_penyajian" id="kategori_penyajian" class="form-select" required>
                            <option value="bisa_pilih">Menu Utama (Bisa Pilih Nasi Kotak / Prasmanan)</option>
                            <option value="prasmanan_saja">Menu Pondokan / Gubukan (Otomatis Prasmanan)</option>
                        </select>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="statusCheck" checked>
                        <label class="form-check-label fw-bold" for="statusCheck">Tersedia (Aktif)</label>
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

<!-- Modal Edit Menu -->
<div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditMenu" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMenuModalLabel">Edit Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_menu" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_menu" name="nama_menu" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi Menu</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_harga" class="form-label">Harga per Porsi (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_harga" name="harga" required min="0">
                    </div>
                    <div class="mb-3">
                        <label for="edit_kategori_penyajian" class="form-label">Kategori Menu Acara <span class="text-danger">*</span></label>
                        <select name="kategori_penyajian" id="edit_kategori_penyajian" class="form-select" required>
                            <option value="bisa_pilih">Menu Utama (Bisa Pilih Nasi Kotak / Prasmanan)</option>
                            <option value="prasmanan_saja">Menu Pondokan / Gubukan (Otomatis Prasmanan)</option>
                        </select>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="edit_statusCheck">
                        <label class="form-check-label fw-bold" for="edit_statusCheck">Tersedia (Aktif)</label>
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

    // Handle Edit Menu Modal
    $('.btn-edit-menu').click(function() {
        var menuId = $(this).data('id');
        var menuNama = $(this).data('nama');
        var menuDeskripsi = $(this).data('deskripsi');
        var menuHarga = $(this).data('harga');
        var menuKategori = $(this).data('kategori');
        var menuStatus = $(this).data('status');
        
        // Isi input nama menu
        $('#edit_nama_menu').val(menuNama);
        $('#edit_deskripsi').val(menuDeskripsi);
        $('#edit_harga').val(menuHarga);
        $('#edit_kategori_penyajian').val(menuKategori);
        $('#edit_statusCheck').prop('checked', menuStatus == 1);
        
        // Ubah action form ke route update yang benar
        var formAction = "{{ url('admin/menu') }}/" + menuId;
        $('#formEditMenu').attr('action', formAction);
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
</script>
@endpush
