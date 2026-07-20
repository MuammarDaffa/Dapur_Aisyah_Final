@extends('layouts.admin')
@section('title', 'Tambah Katering')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('admin.catering.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Katering</a>
        </div>

        <form action="{{ route('admin.catering.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi Katering</h3>
                </div>
                <div class="card-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Katering <span class="text-danger">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" class="form-control" placeholder="Nama Katering">
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tipe Katering --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Tipe Katering <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="catering_type" id="typeHarian" value="harian" {{ old('catering_type', 'harian') === 'harian' ? 'checked' : '' }} onchange="toggleCateringTypeFields()">
                            <label class="form-check-label" for="typeHarian">Harian</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="catering_type" id="typeAcara" value="acara" {{ old('catering_type') === 'acara' ? 'checked' : '' }} onchange="toggleCateringTypeFields()">
                            <label class="form-check-label" for="typeAcara">Acara</label>
                        </div>
                        @error('catering_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Minimal Hari Pemesanan (Hanya Acara) --}}
                    <div class="mb-3" id="cutoff_wrapper">
                        <label class="form-label fw-bold">Minimal Hari Pemesanan <span class="text-danger">*</span></label>
                        <input type="number" name="minimal_order_days" value="{{ old('minimal_order_days') }}" min="0" step="1" class="form-control" placeholder="Minimal Hari Pemesanan (contoh: 3)">
                        @error('minimal_order_days')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Gambar --}}
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold">Gambar</label>
                        <input type="file" name="image" accept="image/*" class="form-control">
                        <div id="imagePreviewContainer" class="d-none mt-3">
                            <p class="small text-muted fw-medium mb-1">Preview Gambar:</p>
                            <div class="border rounded bg-light p-1" style="display: inline-block;">
                                <img id="imagePreview" src="" alt="Preview Gambar" style="height: 112px; object-fit: cover;">
                            </div>
                        </div>
                        @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="isActiveCheck">
                        <label class="form-check-label fw-bold" for="isActiveCheck">
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
    const typeRadio = document.querySelector('input[name="catering_type"]:checked');
    const isAcara = typeRadio && typeRadio.value === 'acara';

    const cutoffWrapper = document.getElementById('cutoff_wrapper');

    if (cutoffWrapper) {
        cutoffWrapper.style.display = isAcara ? 'block' : 'none';
        const input = cutoffWrapper.querySelector('input');
        if (input) {
            input.disabled = !isAcara;
            if (!isAcara) input.value = '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleCateringTypeFields();

    const imgInput = document.querySelector('input[name="image"]');
    const imgContainer = document.getElementById('imagePreviewContainer');
    const imgPreview = document.getElementById('imagePreview');
    if (imgInput && imgContainer && imgPreview) {
        imgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    imgPreview.src = evt.target.result;
                    imgContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                imgPreview.src = '';
                imgContainer.classList.add('d-none');
            }
        });
    }
});
</script>
@endpush
@endsection
