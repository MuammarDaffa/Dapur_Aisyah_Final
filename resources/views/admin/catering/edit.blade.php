@extends('layouts.admin')
@section('title', 'Edit Katering')
@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.catering.index') }}" class="fs-6 text-secondary hover:text-primary">← Kembali ke Daftar Katering</a>
    </div>

    <form action="{{ route('admin.catering.update', $catering) }}" method="POST" enctype="multipart/form-data" class="card shadow-sm mb-4 p-4">
        @csrf @method('PUT')

        {{-- Nama --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Katering *</label>
            <input type="text" name="name" required value="{{ old('name', $catering->name) }}"
                   class="w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
            @error('name')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Deskripsi *</label>
            <textarea name="description" rows="3" required
                      class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">{{ old('description', $catering->description) }}</textarea>
        </div>

        {{-- Tipe Katering (Read-only setelah dibuat) --}}
        @php
            $currentType = $catering->isDaily() ? 'daily' : ($catering->isEvent() ? 'event' : 'daily');
        @endphp
        <input type="hidden" name="catering_type" value="{{ $currentType }}">
        <div class="mb-3">
            <label class="form-label fw-bold">Tipe Katering</label>
            <div class="p-4 border-2 rounded {{ $currentType === 'daily' ? 'border-blue-300 bg-info text-white' : 'border-purple-300 bg-light' }}">
                <div class="d-flex align-items-center g-3">
                    @if($currentType === 'daily')
                    <svg style="width: 24px; height: 24px;" class="text-info flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    @else
                    <svg style="width: 24px; height: 24px;" class="text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    @endif
                    <div>
                        <p class="fw-bold text-secondary">{{ $currentType === 'daily' ? 'Daily' : 'Event' }}</p>
                        <p class="small text-secondary">{{ $currentType === 'daily' ? 'Menu harian dengan produk' : 'Acara dengan paket catering' }}</p>
                    </div>
                    <!-- <span class="ml-auto d-inline-d-flex align-items-center g-3 px-2.5 py-1 rounded-pill small fw-medium bg-light text-secondary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Tidak dapat diubah</span>
                    </span> -->
                </div>
            </div>
            <p class="small text-secondary mt-1">Tipe katering tidak dapat diubah setelah dibuat.</p>
        </div>

        {{-- Harga & Porsi --}}
        <div class="row row-cols-1 {{ !$catering->isDaily() ? 'sm:row-cols-3' : '' }} g-3">
            <div class="mb-3">
            <label class="form-label fw-bold">Harga Dasar (Rp) *</label>
                <input type="text" name="base_price" required value="{{ old('base_price', $catering->base_price) }}"
                       class="w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary rupiah-input">
            </div>
            @if(!$catering->isDaily())
            <div class="mb-3">
            <label class="form-label fw-bold">Min. Porsi *</label>
                <input type="number" name="min_portion" required value="{{ old('min_portion', $catering->min_portion) }}" min="1"
                       class="w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
            </div>
            <div class="mb-3">
            <label class="form-label fw-bold">Max. Porsi</label>
                <input type="number" name="max_portion" value="{{ old('max_portion', $catering->max_portion) }}" placeholder="Tidak dibatasi"
                       class="w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
            </div>
            @endif
        </div>

        @if(!$catering->isDaily())
        {{-- Ketentuan & Jadwal (Hanya Event) --}}
        <div class="row row-cols-2 g-3">
            <div class="mb-3">
            <label class="form-label fw-bold">Ketentuan Pemesanan</label>
                <textarea name="order_terms" rows="2"
                          class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">{{ old('order_terms', $catering->order_terms) }}</textarea>
            </div>
            <div class="mb-3">
            <label class="form-label fw-bold">Catatan Jadwal</label>
                <textarea name="schedule_notes" rows="2"
                          class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">{{ old('schedule_notes', $catering->schedule_notes) }}</textarea>
            </div>
        </div>

        {{-- Minimal Hari Pemesanan (Hanya Event) --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Minimal Hari Pemesanan</label>
            <select name="minimal_order_days" class="form-select w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
                @for($i = 1; $i <= 14; $i++)
                    <option value="{{ $i }}" {{ old('minimal_order_days', $catering->minimal_order_days) == $i ? 'selected' : '' }}>{{ $i }} Hari</option>
                @endfor
            </select>
        </div>
        @endif

        {{-- Gambar --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Gambar</label>
            <input type="file" name="image" accept="image/*"
                   class="w-100 px-4 py-2.5 rounded border border border-secondary fs-6 file:me-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-primary text-white file:text-primary file:fw-medium hover:file:bg-primary text-white">
            @if($catering->image)
            <p class="small text-secondary mt-1 d-inline-d-flex align-items-center g-3">
                <svg style="width: 16px; height: 16px;" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Gambar saat ini tersimpan. Upload baru untuk mengganti.</span>
            </p>
            @endif
            <div id="imagePreviewContainer" class="{{ $catering->image ? '' : 'd-none' }} mt-3">
                <p class="small text-secondary mb-1.5 fw-medium">Preview Gambar:</p>
                <div class="d-inline-block border border border-secondary rounded overflow-hidden bg-light shadow-sm">
                    <img id="imagePreview" src="{{ $catering->image ? asset('storage/' . $catering->image) : '' }}" alt="Preview Gambar" class="h-28 w-auto object-cover d-block">
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="d-flex align-items-center g-3 pt-2">
            <label class="d-flex align-items-center g-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $catering->is_active ? 'checked' : '' }}
                       class="rounded border border-secondary text-primary">
                <span class="fs-6 fw-medium text-secondary">Aktif</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="pt-4 border-t">
            <button type="submit" class="px-6 py-2.5 bg-info text-white text-white fw-medium rounded hover:bg-info text-white shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imgInput = document.querySelector('input[name="image"]');
    const imgContainer = document.getElementById('imagePreviewContainer');
    const imgPreview = document.getElementById('imagePreview');
    const hasExisting = {{ $catering->image ? 'true' : 'false' }};
    const existingSrc = "{{ $catering->image ? asset('storage/' . $catering->image) : '' }}";

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
            } else if (hasExisting && existingSrc) {
                imgPreview.src = existingSrc;
                imgContainer.classList.remove('hidden');
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
