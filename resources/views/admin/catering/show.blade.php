{{-- 
=======================================
File : resources/views/admin/catering/show.blade.php
Fungsi : Menampilkan form edit katering dan manajemen menu (jika katering acara) dalam satu halaman.
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Lihat Katering')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <a href="{{ route('admin.catering.index') }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Katering
            </a>
        </div>
        
        <form action="{{ route('admin.catering.update', $catering->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Edit Informasi Katering</h3>
                </div>
                <div class="card-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Katering <span class="text-danger">*</span></label>
                        <input type="text" name="nama" required value="{{ old('nama', $catering->nama) }}" class="form-control">
                        @error('nama')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

            
                {{-- Tipe Katering (Dikunci) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Tipe Katering</label>
                        <input type="text" class="form-control bg-light" value="{{ ucfirst($catering->tipe) }}" readonly>
                        {{-- Hidden input agar data tipe tetap terkirim ke controller --}}
                        <input type="hidden" name="tipe" value="{{ $catering->tipe }}">
                       
                    </div>


                     @if($catering->isAcara())
                        {{-- Kapasitas (Hanya Acara) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kapasitas Total <span class="text-danger">*</span></label>
                            <input type="number" name="kapasitas_total" value="{{ old('kapasitas_total', $catering->kapasitas_total) }}" min="1" step="1" class="form-control">
                            @error('kapasitas_total')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        
                        {{-- Minimal Porsi (Hanya Acara) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Minimal Porsi Pemesanan <span class="text-danger">*</span></label>
                            <input type="number" name="minimal_porsi" value="{{ old('minimal_porsi', $catering->minimal_porsi) }}" min="1" step="1" class="form-control">
                            @error('minimal_porsi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    {{-- Status --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1" {{ old('status', $catering->status) ? 'checked' : '' }} id="statusCheck">
                        <label class="form-check-label fw-bold" for="statusCheck">
                            Aktif
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Perbarui Katering</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($catering->isAcara())
<div class="row mt-4">
    <!-- Card 1: Menu Makanan -->
    <div class="col-md-12 mb-4">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">Menu Makanan</h3>
                <div class="ms-auto">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMenu">
                        Tambah Menu
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
                                <th width="20%" class="text-center">Isi Menu</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus ?? [] as $index => $menu)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $menu->nama_menu }}</strong>
                                    <div class="text-muted small">{{ $menu->deskripsi }}</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.isi-menu.index', $menu->id) }}" class="btn btn-info btn-sm text-white">
                                        Kelola Isi Menu
                                    </a>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditMenu{{ $menu->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.menu-acara.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus menu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit Menu -->
                            <div class="modal fade" id="modalEditMenu{{ $menu->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.menu-acara.update', $menu->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Menu Makanan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Menu</label>
                                                    <input type="text" name="nama_menu" class="form-control" value="{{ $menu->nama_menu }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea name="deskripsi" class="form-control" rows="3">{{ $menu->deskripsi }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
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

    <!-- Card 2: Minuman -->
    <div class="col-md-12">
        <div class="card card-outline card-success">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">Minuman</h3>
                <div class="ms-auto">
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMinuman">
                        Tambah Minuman
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
                                <td>{{ $minuman->nama }}</td>
                                <td>Rp{{ number_format($minuman->harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditMinuman{{ $minuman->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.minuman-acara.destroy', $minuman->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus minuman ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="layanan_id" value="{{ $catering->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit Minuman -->
                            <div class="modal fade" id="modalEditMinuman{{ $minuman->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.minuman-acara.update', $minuman->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="layanan_id" value="{{ $catering->id }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Minuman</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Minuman</label>
                                                    <input type="text" name="nama" class="form-control" value="{{ $minuman->nama }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Harga</label>
                                                    <input type="number" name="harga" class="form-control" value="{{ $minuman->harga }}" required min="0">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
<div class="modal fade" id="modalTambahMenu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.menu-acara.store', $catering->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Menu Makanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Menu</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Minuman -->
<div class="modal fade" id="modalTambahMinuman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.minuman-acara.store', $catering->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Minuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Minuman</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Minuman</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
