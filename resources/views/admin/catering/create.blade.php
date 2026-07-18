@extends('layouts.admin')
@section('title', 'Tambah Katering')
@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.catering.index') }}" class="fs-6 text-secondary hover:text-primary">← Kembali ke Daftar Katering</a>
    </div>

    <form action="{{ route('admin.catering.store') }}" method="POST" enctype="multipart/form-data" class="card shadow-sm mb-4 p-4">
        @csrf

        {{-- Nama --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Katering *</label>
            <input type="text" name="name" required value="{{ old('name') }}"
                   class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary"
                   placeholder="cth: Katering Harian">
            @error('name')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Deskripsi *</label>
            <textarea name="description" rows="3" required
                      class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary"
                      placeholder="Deskripsi singkat katering...">{{ old('description') }}</textarea>
        </div>

        {{-- Tipe Katering --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tipe Katering *</label>
            <div class="row row-cols-2 g-3">
                <label class="position-relative cursor-pointer">
                    <input type="radio" name="catering_type" value="daily" {{ old('catering_type', 'daily') === 'daily' ? 'checked' : '' }}
                           onchange="toggleCateringTypeFields()" class="peer sr-only">
                    <div class="p-4 border-2 rounded peer-checked:border-blue-500 peer-checked:bg-info text-white hover:border border-secondary">
                        <div class="d-flex align-items-center g-3">
                            <svg style="width: 24px; height: 24px;" class="text-info flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <div>
                                <p class="fw-bold text-secondary">Daily</p>
                                <p class="small text-secondary">Menu harian dengan produk</p>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="position-relative cursor-pointer">
                    <input type="radio" name="catering_type" value="event" {{ old('catering_type') === 'event' ? 'checked' : '' }}
                           onchange="toggleCateringTypeFields()" class="peer sr-only">
                    <div class="p-4 border-2 rounded peer-checked:border-purple-500 peer-checked:bg-light hover:border border-secondary">
                        <div class="d-flex align-items-center g-3">
                            <svg style="width: 24px; height: 24px;" class="text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            <div>
                                <p class="fw-bold text-secondary">Event</p>
                                <p class="small text-secondary">Acara dengan paket catering</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            @error('catering_type')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Harga & Porsi --}}
        <div class="row row-cols-1 row-cols-md-3 g-3" id="base_price_grid">
            <div id="base_price_wrapper" class="col-md-4">
                <label class="form-label fw-bold">Harga Dasar (Rp) *</label>
                <input type="text" name="base_price" required value="{{ old('base_price') }}" 
                       class="form-control w-100 ps-4 pe-4 py-2.5 rounded border border border-secondary focus:border border-primary -2 rupiah-input">
            </div>
            <div id="min_portion_wrapper">
                <label class="form-label fw-bold">Min. Porsi *</label>
                <input type="number" name="min_portion" value="{{ old('min_portion', 1) }}" min="1"
                       class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
            </div>
            <div id="max_portion_wrapper">
                <label class="form-label fw-bold">Max. Porsi</label>
                <input type="number" name="max_portion" value="{{ old('max_portion') }}" placeholder="Tidak dibatasi"
                       class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
            </div>
        </div>

        {{-- Ketentuan & Jadwal (Hanya Event) --}}
        <div id="terms_schedule_wrapper" class="row row-cols-2 g-3">
            <div class="mb-3">
            <label class="form-label fw-bold">Ketentuan Pemesanan</label>
                <textarea name="order_terms" rows="2"
                          class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary"
                          placeholder="Ketentuan khusus...">{{ old('order_terms') }}</textarea>
            </div>
            <div class="mb-3">
            <label class="form-label fw-bold">Catatan Jadwal</label>
                <textarea name="schedule_notes" rows="2"
                          class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary"
                          placeholder="Info jadwal...">{{ old('schedule_notes') }}</textarea>
            </div>
        </div>

        {{-- Pengaturan Cutoff (Hanya Event) --}}
        <div id="cutoff_wrapper" class="bg-primary text-white/50 border border border-primary rounded p-4 d-flex flex-column gap-3">
            <div>
                <h4 class="fs-6 fw-bold text-secondary d-flex align-items-center g-3">
                    <svg style="width: 16px; height: 16px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Pengaturan Cutoff Pemesanan</span>
                </h4>
                <p class="small text-secondary mt-0.5">Batas waktu minimal pemesanan untuk layanan ini</p>
            </div>
            <div class="mb-3">
            <label class="form-label fw-bold">Minimal Hari Pemesanan</label>
                <input type="number" name="minimal_order_days" value="{{ old('minimal_order_days') }}" min="0" placeholder="cth: 3"
                       class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
                <p class="small text-secondary mt-1">Jumlah hari minimal sebelum tanggal acara/pengiriman</p>
            </div>
        </div>

        {{-- Gambar --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Gambar</label>
            <input type="file" name="image" accept="image/*"
                   class="w-100 px-4 py-2.5 rounded border border border-secondary fs-6 file:me-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-primary text-white file:text-primary file:fw-medium hover:file:bg-primary text-white">
            <div id="imagePreviewContainer" class="d-none mt-3">
                <p class="small text-secondary mb-1.5 fw-medium">Preview Gambar:</p>
                <div class="d-inline-block border border border-secondary rounded overflow-hidden bg-light shadow-sm">
                    <img id="imagePreview" src="" alt="Preview Gambar" style="height: 112px;" class="w-auto object-fit-cover d-block">
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="d-flex align-items-center g-3 pt-2">
            <label class="d-flex align-items-center g-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border border-secondary text-primary">
                <span class="fs-6 fw-medium text-secondary">Aktif</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="pt-4 border-t">
            <button type="submit" class="btn btn-primary">
                Simpan Katering
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleCateringTypeFields() {
    const typeRadio = document.querySelector('input[name="catering_type"]:checked');
    const isDaily = typeRadio && typeRadio.value === 'daily';

    const minWrapper = document.getElementById('min_portion_wrapper');
    const maxWrapper = document.getElementById('max_portion_wrapper');
    const termsWrapper = document.getElementById('terms_schedule_wrapper');
    const cutoffWrapper = document.getElementById('cutoff_wrapper');
    const baseGrid = document.getElementById('base_price_grid');
    const baseWrapper = document.getElementById('base_price_wrapper');

    if (minWrapper) {
        minWrapper.classList.toggle('hidden', isDaily);
        const input = minWrapper.querySelector('input');
        if (input) input.disabled = isDaily;
    }
    if (maxWrapper) {
        maxWrapper.classList.toggle('hidden', isDaily);
        const input = maxWrapper.querySelector('input');
        if (input) input.disabled = isDaily;
    }
    if (termsWrapper) {
        termsWrapper.classList.toggle('hidden', isDaily);
        termsWrapper.querySelectorAll('textarea').forEach(el => el.disabled = isDaily);
    }
    if (cutoffWrapper) {
        cutoffWrapper.classList.toggle('hidden', isDaily);
        const input = cutoffWrapper.querySelector('input');
        if (input) input.disabled = isDaily;
    }
    if (baseGrid && baseWrapper) {
        if (isDaily) {
            baseGrid.classList.remove('sm:grid-cols-3');
            baseGrid.classList.add('sm:grid-cols-1');
            baseWrapper.classList.remove('col-md-4');
        } else {
            baseGrid.classList.remove('sm:grid-cols-1');
            baseGrid.classList.add('sm:grid-cols-3');
            baseWrapper.classList.add('col-md-4');
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
                    imgContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                imgPreview.src = '';
                imgContainer.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection
