{{-- 
=======================================
File : resources/views/admin/catering/harian.blade.php
Fungsi : Sebagai halaman pusat/dashboard pengelolaan Katering Harian (Pengaturan Jadwal & Daftar Menu).
Dijalankan Kapan : Saat admin mengklik tombol detail/ikon mata pada katering tipe harian.
Data berasal dari mana : CateringHarianController (variabel $layanan, $daftarMenu, $jadwalTersimpan, $daftarHari).
=======================================
--}}

@extends('layouts.admin')

@section('title', 'Manajemen Katering Harian')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        {{-- Tombol Kembali --}}
        <a href="{{ route('admin.catering.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Judul Halaman Sesuai Nama Katering --}}
<div class="row mb-4">
    <div class="col-12">
        <h2>{{ $layanan->nama }}</h2>
    </div>
</div>

<div class="row">
    {{-- =======================================
         CARD 1: PENGATURAN JADWAL MENU
         ======================================= --}}
    <div class="col-12 mb-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title fw-bold">Pengaturan Jadwal Menu</h3>
            </div>
            
            <form action="{{ route('admin.catering.harian.jadwal', $layanan->id) }}" method="POST" id="formJadwal">
                @csrf
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Hari</th>
                                <th>Aktif</th>
                                <th>Tanggal</th>
                                <th>Menu</th>
                                <th>Stok Awal</th>
                                <th>Sisa Stok</th>
                                <th>Extra</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Melakukan perulangan untuk mencetak baris dari Senin sampai Jumat --}}
                            @foreach($daftarHari as $hari)
                                @php
                                    // Mengecek apakah ada data jadwal yang tersimpan untuk hari ini di database
                                    $jadwal = $jadwalTersimpan->get($hari);
                                    $isAktif = $jadwal ? true : false;
                                @endphp
                                
                                <tr class="jadwal-row">
                                    {{-- 1. Kolom Hari (Tetap/Readonly) --}}
                                    <td class="fw-bold">{{ $hari }}</td>
                                    
                                    {{-- 2. Kolom Aktif (Checkbox) --}}
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input checkbox-aktif" type="checkbox" name="jadwal[{{ $hari }}][aktif]" value="1" {{ $isAktif ? 'checked' : '' }} onchange="toggleInputs(this)">
                                        </div>
                                    </td>
                                    
                                    {{-- 3. Kolom Tanggal (Date Picker) --}}
                                    <td>
                                        <input type="date" class="form-control input-tanggal" name="jadwal[{{ $hari }}][tanggal]" value="{{ $isAktif ? $jadwal->tanggal : '' }}" {{ $isAktif ? '' : 'disabled' }} required>
                                    </td>
                                    
                                    {{-- 4. Kolom Menu (Dropdown) --}}
                                    <td>
                                        <select class="form-select input-menu" name="jadwal[{{ $hari }}][menu_harian_id]" {{ $isAktif ? '' : 'disabled' }} required>
                                            <option value="">-- Pilih Menu --</option>
                                            {{-- Menampilkan daftar menu milik katering ini --}}
                                            @foreach($daftarMenu as $menu)
                                                <option value="{{ $menu->id }}" {{ $isAktif && $jadwal->menu_harian_id == $menu->id ? 'selected' : '' }}>
                                                    {{ $menu->nama_menu }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                    {{-- 5. Kolom Stok Awal --}}
                                    <td>
                                        <input type="number" class="form-control input-stok" name="jadwal[{{ $hari }}][stok_awal]" value="{{ $isAktif ? $jadwal->stok_awal : 0 }}" min="0" {{ $isAktif ? '' : 'disabled' }} style="width: 80px;">
                                    </td>
                                    
                                    {{-- 6. Kolom Sisa Stok (Readonly) --}}
                                    <td>
                                        <input type="text" class="form-control bg-light" value="{{ $isAktif ? $jadwal->stok_tersisa : '-' }}" readonly style="width: 80px;">
                                    </td>
                                    
                                    {{-- 7. Kolom Extra (Tombol Kelola) --}}
                                    <td>
                                        @if($isAktif)
                                            <a href="{{ route('admin.extra-harian.index', $jadwal->id) }}" class="btn btn-sm btn-outline-primary button-kelola">
                                                Kelola
                                            </a>
                                        @else
                                            {{-- Jika baris belum aktif di database, ganti tombol menjadi peringatan --}}
                                            <button type="button" class="btn btn-sm btn-outline-secondary button-kelola" onclick="Swal.fire('Oops!', 'Silakan isi form lalu klik tombol <b>Simpan Jadwal</b> (di bawah tabel) terlebih dahulu untuk mengaktifkan hari ini. Setelah tersimpan, tombol Kelola Extra akan terbuka!', 'info')">
                                                Kelola
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    {{-- =======================================
         CARD 2: DAFTAR MENU HARIAN
         ======================================= --}}
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold m-0">Daftar Menu Harian</h3>
                <a href="{{ route('admin.menu-harian.create', $layanan->id) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> Tambah Menu
                </a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Gambar</th>
                            <th>Nama Menu</th>
                            <th>Harga</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarMenu as $index => $menu)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($menu->gambar)
                                        <img src="{{ asset('storage/' . $menu->gambar) }}" alt="Gambar Menu" width="50" height="50" style="object-fit: cover; border-radius: 5px;">
                                    @else
                                        <span class="text-muted"><i class="bi bi-image"></i></span>
                                    @endif
                                </td>
                                <td>{{ $menu->nama_menu }}</td>
                                <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td>
                                    <div class="btn-group">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('admin.menu-harian.edit', $menu->id) }}" class="btn btn-sm btn-warning" title="Edit Menu">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.menu-harian.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Apakah Anda yakin ingin menghapus menu ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Menu">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada menu yang didaftarkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// =======================================
// Fungsi JavaScript: Mengaktifkan/Menonaktifkan input pada baris tabel Jadwal
// Dijalankan Kapan : Saat admin meng-klik checkbox "Aktif" di suatu baris.
// =======================================
function toggleInputs(checkbox) {
    // Mencari elemen induk "tr" (baris tabel) tempat checkbox ini berada
    const row = checkbox.closest('.jadwal-row');
    
    // Mencari elemen-elemen input di dalam baris tersebut
    const inputTanggal = row.querySelector('.input-tanggal');
    const inputMenu = row.querySelector('.input-menu');
    const inputStok = row.querySelector('.input-stok');
    
    // Mengecek apakah checkbox dicentang atau tidak
    const isChecked = checkbox.checked;
    
    // Menghidupkan (false) atau mematikan (true) atribut "disabled"
    inputTanggal.disabled = !isChecked;
    inputMenu.disabled = !isChecked;
    inputStok.disabled = !isChecked;
    
    // Mengosongkan data jika baris dimatikan (opsional)
    if (!isChecked) {
        // Kita tidak otomatis mengosongkan nilainya agar jika tidak sengaja ter-klik, data tidak hilang.
        // Data tidak akan terkirim ke server karena berstatus 'disabled'.
    }
}
</script>
@endpush
@endsection
