{{-- 
=======================================
File : resources/views/admin/menu-harian/create.blade.php
Fungsi : Halaman untuk menambahkan menu harian baru.
Dijalankan Kapan : Saat admin menekan tombol "Tambah Menu".
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Tambah Menu Harian')

@section('content')
<div class="row">
    <div class="col-md-8">
        {{-- Tombol Batal/Kembali --}}
        <div class="mb-3">
            <a href="{{ route('admin.catering.harian', $layanan->id) }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Batal & Kembali
            </a>
        </div>

        <form action="{{ route('admin.menu-harian.store', $layanan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Menu Harian</h3>
                </div>
                <div class="card-body">
                    {{-- Nama Menu --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu') }}" required>
                        @error('nama_menu')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Menu</label>
                        <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Harga --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga per Porsi (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" required>
                        @error('harga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Upload Gambar --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload Gambar Menu</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG/PNG, Maksimal: 2MB.</small>
                        @error('gambar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Status Aktif --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="statusCheck" checked>
                        <label class="form-check-label fw-bold" for="statusCheck">
                            Tersedia (Aktif)
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan Menu
                    </button>
                    <a href="{{ route('admin.catering.harian', $layanan->id) }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
