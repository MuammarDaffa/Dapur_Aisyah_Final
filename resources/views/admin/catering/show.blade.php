{{-- 
=======================================
File : resources/views/admin/catering/show.blade.php
Fungsi : Menampilkan rincian data (detail) dari suatu katering secara lengkap.
Dijalankan Kapan : Setelah admin menekan tombol Detail dan Controller selesai memproses data.
Data berasal dari mana : Dikirim oleh CateringController (berupa variabel bernama $catering).
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Detail Katering')

@section('content')
<div class="row">
    <div class="col-md-8">
        {{-- Tombol untuk kembali ke halaman daftar katering utama (index) --}}
        <div class="mb-3">
            <a href="{{ route('admin.catering.index') }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Katering
            </a>
        </div>

        {{-- Kartu (Card) untuk menampilkan informasi detail --}}
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Informasi Detail Katering</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 200px;">Nama Katering</th>
                            {{-- Menampilkan isi data nama dari database --}}
                            <td>{{ $catering->nama }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Layanan</th>
                            <td>
                                {{-- Logika percabangan (if): Jika katering ini tipe harian, tampilkan badge biru, jika tidak (acara) tampilkan badge ungu --}}
                                @if($catering->isHarian())
                                    <span class="badge text-bg-info">Harian</span>
                                @else
                                    <span class="badge text-bg-purple" style="background-color: #6f42c1; color: white;">Acara</span>
                                @endif
                            </td>
                        </tr>
                        
                        {{-- Data kapasitas HANYA dimunculkan jika katering tersebut bertipe Acara --}}
                        @if($catering->isAcara())
                        <tr>
                            <th>Kapasitas Total Porsi</th>
                            <td>{{ $catering->kapasitas_total }} porsi</td>
                        </tr>
                        <tr>
                            <th>Minimal Porsi Pemesanan</th>
                            <td>{{ $catering->minimal_porsi }} porsi</td>
                        </tr>
                        @endif

                        <tr>
                            <th>Status Aktif</th>
                            <td>
                                {{-- Mengecek status katering: angka 1 berarti aktif, 0 berarti nonaktif --}}
                                @if($catering->status == 1)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            {{-- Memformat tanggal agar mudah dibaca manusia (Contoh: 24 Jul 2026 14:00) --}}
                            <td>{{ $catering->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
