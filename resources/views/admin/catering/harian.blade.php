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
                            <label for="start_date" class="form-label">Pilih Tanggal Mulai (Senin)</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success">
                                 Generate Jadwal 1 Minggu
                            </button>
                            <!-- @if(request('start_date'))
                                <a href="{{ route('admin.catering.harian', 'harian') }}" class="btn btn-secondary">
                                    Reset
                                </a>
                            @endif -->
                        </div>
                    </div>
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
                                <th style="width: 5%;" class="text-center">Aktif</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 30%;">Menu</th>
                                <th style="width: 15%;">Stok Awal</th>
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
                                    
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center m-0">
                                            <input class="form-check-input checkbox-aktif" type="checkbox" name="jadwal[{{ $tanggalStr }}][aktif]" value="1" {{ $isAktif ? 'checked' : '' }} onchange="toggleInputs(this)">
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <input type="date" class="form-control input-tanggal" name="jadwal[{{ $tanggalStr }}][tanggal]" value="{{ $tanggalStr }}" {{ $isAktif ? '' : 'disabled' }} readonly style="pointer-events: none;">
                                    </td>
                                    
                                    <td>
                                        <select class="form-select input-menu" name="jadwal[{{ $tanggalStr }}][menu_id]" {{ $isAktif ? '' : 'disabled' }} required>
                                            <option value="">-- Pilih Menu --</option>
                                            @foreach($daftarMenu as $menu)
                                                <option value="{{ $menu->id }}" {{ $isAktif && $jadwal->menu_id == $menu->id ? 'selected' : '' }}>
                                                    {{ $menu->nama_menu }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                    <td>
                                        <input type="number" class="form-control input-stok" name="jadwal[{{ $tanggalStr }}][stok_awal]" value="{{ $isAktif ? $jadwal->stok_awal : 0 }}" min="0" {{ $isAktif ? '' : 'disabled' }}>
                                    </td>
                                    
                                    <td>
                                        <input type="text" class="form-control bg-light" value="{{ $isAktif ? $jadwal->stok_tersisa : '-' }}" readonly>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Simpan Jadwal
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#resetJadwalModal">
                         Reset Semua Jadwal
                    </button>
                </div>
            </form>

            <!-- Modal Reset Jadwal -->
            <div class="modal fade text-start" id="resetJadwalModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('admin.catering.harian.reset') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Reset Seluruh Jadwal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0">Apakah Anda yakin ingin menghapus seluruh jadwal menu katering harian?</p>
                                <!-- <p class="text-muted small mt-2">Catatan: Tindakan ini tidak bisa dibatalkan, namun pesanan pelanggan lama tidak akan terdampak.</p> -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Ya</button>
                            </div>
                        </form>
                    </div>
                </div>
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
                                        <img src="{{ asset('storage/menu/' . $menu->gambar) }}" class="img-thumbnail" style="width:80px;height:80px;object-fit:cover;" alt="Gambar Menu">
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
                                        <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Apakah Anda yakin ingin menghapus menu ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
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
    function toggleInputs(checkbox) {
        // Cari baris <tr> terdekat
        const row = checkbox.closest('.jadwal-row');
        // Cari input menu (select), stok_awal, dan tanggal di dalam baris tersebut
        const inputMenu = row.querySelector('.input-menu');
        const inputStok = row.querySelector('.input-stok');
        const inputTanggal = row.querySelector('.input-tanggal');
        
        // Jika checkbox dicentang, aktifkan input; jika tidak, nonaktifkan
        if (checkbox.checked) {
            inputMenu.removeAttribute('disabled');
            inputStok.removeAttribute('disabled');
            if (inputTanggal) inputTanggal.removeAttribute('disabled');
        } else {
            inputMenu.setAttribute('disabled', 'disabled');
            inputStok.setAttribute('disabled', 'disabled');
            if (inputTanggal) inputTanggal.setAttribute('disabled', 'disabled');
        }
    }
</script>
@endpush
