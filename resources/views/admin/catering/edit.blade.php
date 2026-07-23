@extends('layouts.admin')
@section('title', 'Edit Katering')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('admin.catering.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Katering</a>
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

                    {{-- Tipe Katering --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Tipe Katering <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe" id="tipeHarian" value="harian" {{ old('tipe', $catering->tipe) === 'harian' ? 'checked' : '' }} onchange="toggleCateringTypeFields()">
                            <label class="form-check-label" for="tipeHarian">Harian</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe" id="tipeAcara" value="acara" {{ old('tipe', $catering->tipe) === 'acara' ? 'checked' : '' }} onchange="toggleCateringTypeFields()">
                            <label class="form-check-label" for="tipeAcara">Acara</label>
                        </div>
                        @error('tipe')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Kapasitas (Hanya Acara) --}}
                    <div class="mb-3 acara-field">
                        <label class="form-label fw-bold">Kapasitas Total <span class="text-danger">*</span></label>
                        <input type="number" name="kapasitas_total" value="{{ old('kapasitas_total', $catering->kapasitas_total) }}" min="1" step="1" class="form-control">
                        @error('kapasitas_total')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    
                    {{-- Minimal Porsi (Hanya Acara) --}}
                    <div class="mb-3 acara-field">
                        <label class="form-label fw-bold">Minimal Porsi Pemesanan <span class="text-danger">*</span></label>
                        <input type="number" name="minimal_porsi" value="{{ old('minimal_porsi', $catering->minimal_porsi) }}" min="1" step="1" class="form-control">
                        @error('minimal_porsi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1" {{ old('status', $catering->status) ? 'checked' : '' }} id="statusCheck">
                        <label class="form-check-label fw-bold" for="statusCheck">
                            Aktif
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Perbarui Katering</button>
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
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    toggleCateringTypeFields();
});
</script>
@endpush
@endsection
