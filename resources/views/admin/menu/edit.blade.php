

@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')
<!-- <div class="row mb-3">
    <div class="col-12">
        <a href="{{ $menu->tipe_layanan === 'harian' ? route('admin.catering.harian') : route('admin.catering.acara') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali 
        </a>
    </div>
</div> -->

<div class="row">
    <div class="col-md-8">

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
                                <img id="preview" src="{{ asset('storage/menu/' . $menu->gambar) }}" alt="Preview Gambar" style="max-height: 200px;">
                            @else
                                <img id="preview" src="#" alt="Preview Gambar" style="max-height: 200px; display: none;">
                            @endif
                        </div>
                    </div>

                    {{-- Deskripsi Menu --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Menu <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Contoh: Nasi Putih, Semur Daging, Tempe Goreng, Sambal, Kerupuk" required>{{ old('deskripsi', $menu->deskripsi) }}</textarea>
                        @error('deskripsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Harga --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga {{ $menu->tipe_layanan === 'harian' ? 'per Porsi' : 'Dasar' }} (Rp) <span class="text-danger">*</span></label>
                        <input type="text" name="harga" class="form-control format-rupiah" value="{{ old('harga', $menu->harga) }}" required>
                        @error('harga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>


                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i></i> Update Menu
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

    function formatRupiah(angka) {
        let number_string = angka.replace(/[^0-9]/g, '').toString();
        let sisa = number_string.length % 3;
        let rupiah = number_string.substr(0, sisa);
        let ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    document.querySelectorAll('.format-rupiah').forEach(function(input) {
        input.value = formatRupiah(input.value);
        input.addEventListener('input', function(e) {
            this.value = formatRupiah(this.value);
        });
    });
</script>
@endpush
