@extends('layouts.admin')
@section('title', 'Tambah Katering')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('admin.catering.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Katering</a>
        </div>

        <form action="{{ route('admin.catering.store') }}" method="POST">
            @csrf
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi Katering</h3>
                </div>
                <div class="card-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Katering <span class="text-danger">*</span></label>
                        <input type="text" name="nama" required value="{{ old('nama') }}" class="form-control" placeholder="Nama Katering">
                        @error('nama')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tipe Katering --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Tipe Katering <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe" id="tipeHarian" value="harian" {{ old('tipe', 'harian') === 'harian' ? 'checked' : '' }} onchange="toggleCateringTypeFields()">
                            <label class="form-check-label" for="tipeHarian">Harian</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe" id="tipeAcara" value="acara" {{ old('tipe') === 'acara' ? 'checked' : '' }} onchange="toggleCateringTypeFields()">
                            <label class="form-check-label" for="tipeAcara">Acara</label>
                        </div>
                        @error('tipe')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Kapasitas (Hanya Acara) --}}
                    <div class="mb-3 acara-field">
                        <label class="form-label fw-bold">Kapasitas Total <span class="text-danger">*</span></label>
                        <input type="number" name="kapasitas_total" value="{{ old('kapasitas_total') }}" min="1" step="1" class="form-control" placeholder="Kapasitas Total Porsi">
                        @error('kapasitas_total')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    
                    {{-- Minimal Porsi (Hanya Acara) --}}
                    <div class="mb-3 acara-field">
                        <label class="form-label fw-bold">Minimal Porsi Pemesanan <span class="text-danger">*</span></label>
                        <input type="number" name="minimal_porsi" value="{{ old('minimal_porsi') }}" min="1" step="1" class="form-control" placeholder="Minimal Porsi (contoh: 50)">
                        @error('minimal_porsi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1" checked id="statusCheck">
                        <label class="form-check-label fw-bold" for="statusCheck">
                            Aktif
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Katering</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleCateringTypeFields() {
    const typeRadio = document.querySelector('input[name="tipe"]:checked');
    const isAcara = typeRadio && typeRadio.value === 'acara';

    const acaraFields = document.querySelectorAll('.acara-field');

    acaraFields.forEach(field => {
        field.style.display = isAcara ? 'block' : 'none';
        const input = field.querySelector('input');
        if (input) {
            input.disabled = !isAcara;
            if (!isAcara) input.value = '';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    toggleCateringTypeFields();
});
</script>
@endpush
@endsection
