{{-- 
=======================================
File : resources/views/admin/catering/harian.blade.php
Fungsi : Sebagai halaman pusat/dashboard pengelolaan Katering Harian (Pengaturan Jadwal & Daftar Menu).
Dijalankan Kapan : Saat admin mengklik tombol detail/ikon mata pada katering tipe harian.
Data berasal dari mana : CateringHarianController (variabel $layanan, $daftarMenu, $jadwalTersimpan, $daftarHari).
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Katering Harian')

@section('content')


{{-- Judul Halaman Sesuai Nama Katering --}}
<!-- <div class="row mb-4">
    <div class="col-12">
        <h2>Katering Harian</h2>
    </div>
</div> -->



<div class="row">
    {{-- =======================================
         CARD 1: PENGATURAN JADWAL MENU
         ======================================= --}}
    <div class="col-12 mb-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title fw-bold">Pengaturan Jadwal Menu</h3>
            </div>
            
            <div class="card-body pb-0">
                {{-- Form Generate Tanggal --}}
                <form action="{{ route('admin.catering.harian', 'harian') }}" method="GET" class="mb-4">
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Pilih Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                     Buat Jadwal
                                </button>
                                <button type="button" class="btn btn-danger" onclick="resetJadwal(document.getElementById('formResetJadwal'))">
                                     Hapus Jadwal
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <form id="formResetJadwal" action="{{ route('admin.catering.harian.reset') }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

            @if(!empty($daftarTanggal))
            <form action="{{ route('admin.catering.harian.jadwal', 'harian') }}" method="POST" id="formJadwal">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <div class="card-body pt-0 table-responsive">
                    <table class="table table-bordered table-hover align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">Hari</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 30%;">Menu</th>
                                <th style="width: 15%;">Stok Awal</th>
                                <th style="width: 15%;">Terjual</th>
                                <th style="width: 15%;">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daftarTanggal as $item)
                                @php
                                    $tanggalStr = $item['tanggal'];
                                    $hariStr = $item['hari'];
                                    $jadwal = $jadwalTersimpan->get($tanggalStr);
                                    $isAktif = $jadwal ? true : false;
                                @endphp
                                
                                <tr class="jadwal-row">
                                    <td class="fw-bold">{{ $hariStr }}</td>
                                    
                                    <td>
                                        <input type="date" class="form-control form-control-sm shadow-none input-tanggal" name="jadwal[{{ $tanggalStr }}][tanggal]" value="{{ $tanggalStr }}" readonly style="pointer-events: none;">
                                    </td>
                                    
                                    <td>
                                        <select class="form-select form-select-sm shadow-none input-menu" name="jadwal[{{ $tanggalStr }}][menu_id]">
                                            <option value="">-- Pilih Menu --</option>
                                            @foreach($daftarMenu as $menu)
                                                <option value="{{ $menu->id }}" {{ $jadwal && $jadwal->menu_id == $menu->id ? 'selected' : '' }}>
                                                    {{ $menu->nama_menu }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                    <td>
                                        <input type="number" class="form-control form-control-sm shadow-none input-stok" name="jadwal[{{ $tanggalStr }}][stok_awal]" value="{{ $jadwal ? $jadwal->stok_awal : 0 }}" min="0">
                                    </td>
                                    
                                    <td>
                                        <input type="text" class="form-control form-control-sm shadow-none bg-transparent border-0 text-center" value="{{ $jadwal ? ($jadwal->stok_awal - $jadwal->stok_tersisa) : '-' }}" readonly>
                                    </td>
                                    
                                    <td>
                                        <input type="text" class="form-control form-control-sm shadow-none bg-transparent border-0 text-center" value="{{ $jadwal ? $jadwal->stok_tersisa : '-' }}" readonly>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="card-footer d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('formJadwal').submit()">
                    Simpan
                </button>
            </div>
            @else
                <div class="card-body pt-0">
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    {{-- =======================================
         CARD 2: DAFTAR MENU HARIAN
         ======================================= --}}
    <div class="col-12">
        <div class="card card-outline card-success">
           <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar Menu Harian</h3>

    <a href="{{ route('admin.menu.create', 'harian') }}"
       class="btn btn-sm btn-success ms-auto">
        Tambah Menu
    </a>
</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th class="text-start" style="width: 20%;">Nama Menu</th>
                            <th class="text-center" style="width: 15%;">Gambar</th>
                            <th class="text-start" style="width: 25%;">Deskripsi</th>
                            <th class="text-start" style="width: 15%;">Harga</th>
                            <th class="text-center" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarMenu as $index => $menu)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-start">{{ $menu->nama_menu }}</td>
                                <td class="text-center">
                                    @if($menu->gambar)
                                        <img src="{{ asset('storage/menu/' . $menu->gambar) }}" style="width:80px;height:80px;object-fit:cover;" alt="Gambar Menu">
                                    @else
                                        <span class="text-muted small">Belum ada gambar</span>
                                    @endif
                                </td>
                                <td class="text-start">{{ $menu->deskripsi }}</td>
                                <td class="text-start">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <a href="{{ route('admin.menu.tambahan.index', $menu->id) }}" class="btn btn-sm btn-info text-white">
                                            Tambahan Menu
                                        </a>
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="hapusData(this.form)">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada menu yang didaftarkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>

    function hapusData(form) {
        Swal.fire({
            title: 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    function resetJadwal(form) {
        Swal.fire({
            title: 'Apakah Anda ingin reset jadwal ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>

@if(session('swal_success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: '{{ session('swal_success') }}',
            icon: 'success',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    });
</script>
@endif
@endpush
