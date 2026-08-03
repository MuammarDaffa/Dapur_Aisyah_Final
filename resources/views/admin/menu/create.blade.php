{{-- 
=======================================
File : resources/views/admin/menu/create.blade.php
Fungsi : Halaman untuk menambahkan menu baru (harian/acara).
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Tambah Menu')

@section('content')
<div class="row">
    <div class="col-md-8">
        {{-- Tombol Batal/Kembali --}}
        <div class="mb-3">
            <a href="{{ $tipe_layanan === 'harian' ? route('admin.catering.harian', 'harian') : route('admin.catering.acara', 'acara') }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Batal & Kembali
            </a>
        </div>

        <form action="{{ route('admin.menu.store', $tipe_layanan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Menu {{ $tipe_layanan === 'harian' ? 'Harian' : 'Acara' }}</h3>
                </div>
                <div class="card-body">
                    {{-- Nama Menu --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu') }}" required>
                        @error('nama_menu')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Gambar Menu --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Gambar Menu <span class="text-danger">*</span></label>
                        <input type="file" name="gambar" id="gambar" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
                        @error('gambar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <img id="preview" src="#" alt="Preview Gambar" class="img-thumbnail mt-2" style="max-height: 200px; display: none;">
                    </div>

                    {{-- Harga --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga {{ $tipe_layanan === 'harian' ? 'per Porsi' : 'Dasar' }} (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" required>
                        @error('harga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    @if($tipe_layanan !== 'harian')
                    {{-- Kategori Penyajian --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Menu Acara <span class="text-danger">*</span></label>
                        <select name="kategori_penyajian" class="form-select" required>
                            <option value="bisa_pilih" {{ old('kategori_penyajian') == 'bisa_pilih' ? 'selected' : '' }}>Menu Utama (Bisa Pilih Nasi Kotak / Prasmanan)</option>
                            <option value="prasmanan_saja" {{ old('kategori_penyajian') == 'prasmanan_saja' ? 'selected' : '' }}>Menu Pondokan / Gubukan (Otomatis Prasmanan)</option>
                        </select>
                        @error('kategori_penyajian')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <div class="form-text">Pilih kategori ini untuk menentukan bagaimana pelanggan memilih menu ini di halaman pemesanan.</div>
                    </div>
                    @endif

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
                    <a href="{{ $tipe_layanan === 'harian' ? route('admin.catering.harian', 'harian') : route('admin.catering.acara', 'acara') }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('gambar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    });
</script>
@endpush
