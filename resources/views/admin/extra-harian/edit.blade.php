{{-- 
=======================================
File : resources/views/admin/extra-harian/edit.blade.php
Fungsi : Halaman form untuk mengedit extra.
Dijalankan Kapan : Saat admin menekan tombol "Edit" pada daftar extra.
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Edit Extra Menu')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <a href="{{ route('admin.extra-harian.index', $extra->jadwal_menu_id) }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Batal & Kembali
            </a>
        </div>

        <form action="{{ route('admin.extra-harian.update', $extra->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Extra</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Extra <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $extra->nama) }}" required>
                        @error('nama')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga Extra (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control" value="{{ old('harga', $extra->harga) }}" min="0" required>
                        @error('harga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update Extra
                    </button>
                    <a href="{{ route('admin.extra-harian.index', $extra->jadwal_menu_id) }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
