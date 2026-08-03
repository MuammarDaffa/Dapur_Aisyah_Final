{{-- 
=======================================
File : resources/views/admin/menu/edit.blade.php
Fungsi : Halaman untuk mengedit menu yang sudah ada.
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')
<div class="row">
    <div class="col-md-8">
        {{-- Tombol Batal/Kembali --}}
        <div class="mb-3">
            <a href="{{ $menu->tipe_layanan === 'harian' ? route('admin.catering.harian', 'harian') : route('admin.catering.acara', 'acara') }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Batal & Kembali
            </a>
        </div>

        <form action="{{ route('admin.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Menu {{ $menu->tipe_layanan === 'harian' ? 'Harian' : 'Acara' }}</h3>
                </div>
                <div class="card-body">
                    {{-- Nama Menu --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu', $menu->nama_menu) }}" required>
                        @error('nama_menu')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Gambar Menu --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Gambar Menu</label>
                        <input type="file" name="gambar" id="gambar" class="form-control" accept="image/jpeg,image/png,image/jpg">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        @error('gambar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        
                        <div class="mt-2">
                            @if($menu->gambar)
                                <img id="preview" src="{{ asset('storage/menu/' . $menu->gambar) }}" alt="Preview Gambar" class="img-thumbnail" style="max-height: 200px;">
                            @else
                                <img id="preview" src="#" alt="Preview Gambar" class="img-thumbnail" style="max-height: 200px; display: none;">
                            @endif
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga {{ $menu->tipe_layanan === 'harian' ? 'per Porsi' : 'Dasar' }} (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control" value="{{ old('harga', $menu->harga) }}" min="0" required>
                        @error('harga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    @if($menu->tipe_layanan !== 'harian')
                    {{-- Kategori Penyajian --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Menu Acara <span class="text-danger">*</span></label>
                        <select name="kategori_penyajian" class="form-select" required>
                            <option value="bisa_pilih" {{ old('kategori_penyajian', $menu->kategori_penyajian) == 'bisa_pilih' ? 'selected' : '' }}>Menu Utama (Bisa Pilih Nasi Kotak / Prasmanan)</option>
                            <option value="prasmanan_saja" {{ old('kategori_penyajian', $menu->kategori_penyajian) == 'prasmanan_saja' ? 'selected' : '' }}>Menu Pondokan / Gubukan (Otomatis Prasmanan)</option>
                        </select>
                        @error('kategori_penyajian')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <div class="form-text">Pilih kategori ini untuk menentukan bagaimana pelanggan memilih menu ini di halaman pemesanan.</div>
                    </div>
                    @endif

                    {{-- Status Aktif --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="statusCheck" {{ old('status', $menu->status) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="statusCheck">
                            Tersedia (Aktif)
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update Menu
                    </button>
                    <a href="{{ $menu->tipe_layanan === 'harian' ? route('admin.catering.harian', 'harian') : route('admin.catering.acara', 'acara') }}" class="btn btn-secondary">Batal</a>
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
            @if($menu->gambar)
                preview.src = "{{ asset('storage/menu/' . $menu->gambar) }}";
            @else
                preview.src = '#';
                preview.style.display = 'none';
            @endif
        }
    });
</script>
@endpush
