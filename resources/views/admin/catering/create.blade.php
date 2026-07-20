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
                        <input type="text" name="name" required value="{{ old('name') }}" class="form-control" placeholder="cth: Katering Harian">
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" rows="3" required class="form-control" placeholder="Deskripsi singkat katering...">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Tipe Katering --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Tipe Katering <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="w-100 cursor-pointer">
                                    <input type="radio" name="catering_type" value="harian" {{ old('catering_type', 'harian') === 'harian' ? 'checked' : '' }} onchange="toggleCateringTypeFields()" class="d-none peer">
                                    <div class="card mb-0 h-100 border type-selector">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fa-solid fa-calendar-day fa-2x text-info me-3"></i>
                                            <div>
                                                <h5 class="mb-1 fw-bold">Harian</h5>
                                                <small class="text-muted">Menu harian dengan produk</small>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="w-100 cursor-pointer">
                                    <input type="radio" name="catering_type" value="acara" {{ old('catering_type') === 'acara' ? 'checked' : '' }} onchange="toggleCateringTypeFields()" class="d-none peer">
                                    <div class="card mb-0 h-100 border type-selector">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fa-solid fa-glass-cheers fa-2x text-purple me-3" style="color: #6f42c1;"></i>
                                            <div>
                                                <h5 class="mb-1 fw-bold">Acara</h5>
                                                <small class="text-muted">Acara dengan paket catering</small>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @error('catering_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Harga & Porsi --}}
                    <div class="row g-3" id="base_price_grid">
                        <div id="base_price_wrapper" class="col-md-4">
                            <label class="form-label fw-bold">Harga Dasar (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="base_price" required value="{{ old('base_price') }}" class="form-control rupiah-input">
                        </div>
                        <div id="min_portion_wrapper" class="col-md-4">
                            <label class="form-label fw-bold">Min. Porsi <span class="text-danger">*</span></label>
                            <input type="number" name="min_portion" value="{{ old('min_portion', 1) }}" min="1" class="form-control">
                        </div>
                        <div id="max_portion_wrapper" class="col-md-4">
                            <label class="form-label fw-bold">Max. Porsi</label>
                            <input type="number" name="maksimal_porsi" value="{{ old('maksimal_porsi') }}" placeholder="Tidak dibatasi" class="form-control">
                        </div>
                    </div>

                    {{-- Ketentuan & Jadwal (Hanya Acara) --}}
                    <div id="terms_schedule_wrapper" class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ketentuan Pemesanan</label>
                            <textarea name="order_terms" rows="2" class="form-control" placeholder="Ketentuan khusus...">{{ old('order_terms') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Catatan Jadwal</label>
                            <textarea name="schedule_notes" rows="2" class="form-control" placeholder="Info jadwal...">{{ old('schedule_notes') }}</textarea>
                        </div>
                    </div>

                    {{-- Pengaturan Cutoff (Hanya Acara) --}}
                    <div id="cutoff_wrapper" class="callout callout-info mt-3 bg-light border-start border-4 border-info">
                        <h5><i class="fa-solid fa-info-circle text-info"></i> Pengaturan Cutoff Pemesanan</h5>
                        <p class="text-muted mb-2">Batas waktu minimal pemesanan untuk layanan ini</p>
                        <label class="form-label fw-bold">Minimal Hari Pemesanan</label>
                        <input type="number" name="minimal_order_days" value="{{ old('minimal_order_days') }}" min="0" placeholder="cth: 3" class="form-control">
                        <small class="text-muted">Jumlah hari minimal sebelum tanggal acara/pengiriman</small>
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

@push('styles')
<style>
    .type-selector.active {
        border-color: var(--bs-primary) !important;
        background-color: rgba(13, 110, 253, 0.1);
    }
    input[type=radio]:checked + .type-selector {
        border-color: var(--bs-primary) !important;
        background-color: rgba(13, 110, 253, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
function toggleCateringTypeFields() {
    const typeRadio = document.querySelector('input[name="catering_type"]:checked');
    const isDaily = typeRadio && typeRadio.value === 'harian';

    const minWrapper = document.getElementById('min_portion_wrapper');
    const maxWrapper = document.getElementById('max_portion_wrapper');
    const termsWrapper = document.getElementById('terms_schedule_wrapper');
    const cutoffWrapper = document.getElementById('cutoff_wrapper');
    const baseGrid = document.getElementById('base_price_grid');
    const baseWrapper = document.getElementById('base_price_wrapper');

    if (minWrapper) {
        minWrapper.style.display = isDaily ? 'none' : 'block';
        const input = minWrapper.querySelector('input');
        if (input) input.disabled = isDaily;
    }
    if (maxWrapper) {
        maxWrapper.style.display = isDaily ? 'none' : 'block';
        const input = maxWrapper.querySelector('input');
        if (input) input.disabled = isDaily;
    }
    if (termsWrapper) {
        termsWrapper.style.display = isDaily ? 'none' : 'flex';
        termsWrapper.querySelectorAll('textarea').forEach(el => el.disabled = isDaily);
    }
    if (cutoffWrapper) {
        cutoffWrapper.style.display = isDaily ? 'none' : 'block';
        const input = cutoffWrapper.querySelector('input');
        if (input) input.disabled = isDaily;
    }
    if (baseGrid && baseWrapper) {
        if (isDaily) {
            baseWrapper.className = 'col-12';
        } else {
            baseWrapper.className = 'col-md-4';
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
