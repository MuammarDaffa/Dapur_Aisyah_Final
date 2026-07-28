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
@endsection
