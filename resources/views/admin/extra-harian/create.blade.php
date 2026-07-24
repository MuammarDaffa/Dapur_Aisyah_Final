{{-- 
=======================================
File : resources/views/admin/extra-harian/create.blade.php
Fungsi : Halaman form untuk menambahkan extra baru.
Dijalankan Kapan : Saat admin menekan tombol "Tambah Extra".
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Tambah Extra Menu')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <a href="{{ route('admin.extra-harian.index', $jadwal->id) }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Batal & Kembali
            </a>
        </div>

        <form action="{{ route('admin.extra-harian.store', $jadwal->id) }}" method="POST">
            @csrf
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Extra</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Extra <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: Nasi Putih Tambahan">
                        @error('nama')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga Extra (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" required placeholder="Contoh: 5000">
                        @error('harga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Extra
                    </button>
                    <a href="{{ route('admin.extra-harian.index', $jadwal->id) }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
