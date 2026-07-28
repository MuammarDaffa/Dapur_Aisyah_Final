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
        @if($catering->isAcara())
        <div class="card card-outline card-info mb-4">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Kapasitas Minggu Ini</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0 text-center">
                        <thead>
                            <tr>
                                <th>Kapasitas Porsi per Minggu</th>
                                <th>Jumlah Porsi Terjual Minggu Ini</th>
                                <th>Sisa Porsi Minggu Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($kapasitas_porsi_per_minggu, 0, ',', '.') }} Porsi</td>
                                <td>{{ number_format($jumlah_porsi_terjual_minggu_ini, 0, ',', '.') }} Porsi</td>
                                <td>{{ number_format($sisa_porsi_minggu_ini, 0, ',', '.') }} Porsi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <form id="form-update-katering" action="{{ route('admin.catering.update', $catering->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card card-outline card-warning">
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
                            <label class="form-label fw-bold">Kapasitas Porsi per Minggu <span class="text-danger">*</span></label>
                            <input type="number" name="kapasitas_porsi_per_minggu" value="{{ old('kapasitas_porsi_per_minggu', $catering->kapasitas_porsi_per_minggu) }}" min="1" step="1" class="form-control">
                            @error('kapasitas_porsi_per_minggu')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                    <a href="{{ route('admin.menu.create', $catering->id) }}" class="btn btn-primary btn-sm">
                        Tambah Menu
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
                                    <a href="{{ route('admin.menu.items.index', $menu->id) }}" class="btn btn-info btn-sm text-white">
                                        Kelola Isi Menu
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>
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
@endif

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Deskripsi</h3>
            </div>
            <div class="card-body">
                <textarea id="summernote" name="deskripsi" form="form-update-katering">{{ old('deskripsi', $catering->deskripsi) }}</textarea>
                @error('deskripsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
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
});
</script>
@endpush
