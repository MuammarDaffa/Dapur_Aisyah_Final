{{-- 
=======================================
File : resources/views/admin/extra-harian/index.blade.php
Fungsi : Halaman untuk mengelola (melihat daftar) Extra Menu pada suatu jadwal hari.
Dijalankan Kapan : Saat admin menekan tombol "Kelola" di kolom Extra.
Data berasal dari mana : ExtraHarianController (variabel $jadwal dan $extras).
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Kelola Extra Menu')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        {{-- Tombol Kembali ke Manajemen Harian --}}
        <a href="{{ route('admin.catering.harian', $jadwal->menuHarian->layanan_id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Pengaturan Jadwal
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h2>Kelola Extra Menu: Hari {{ $jadwal->hari }}</h2>
        <p class="text-muted">Menu Utama: <strong>{{ $jadwal->menuHarian->nama_menu }}</strong> (Tanggal: {{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }})</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold m-0">Daftar Extra</h3>
                {{-- Tombol Tambah Extra --}}
                <a href="{{ route('admin.extra-harian.create', $jadwal->id) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Extra
                </a>
            </div>
            
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Extra</th>
                            <th>Harga</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extras as $index => $extra)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $extra->nama }}</td>
                                <td>Rp {{ number_format($extra->harga, 0, ',', '.') }}</td>
                                <td>
                                    <div class="btn-group">
                                        {{-- Tombol Edit Extra --}}
                                        <a href="{{ route('admin.extra-harian.edit', $extra->id) }}" class="btn btn-sm btn-warning" title="Edit Extra">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        {{-- Tombol Hapus Extra --}}
                                        <form action="{{ route('admin.extra-harian.destroy', $extra->id) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Apakah Anda yakin ingin menghapus extra ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Extra">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada extra yang didaftarkan untuk jadwal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
