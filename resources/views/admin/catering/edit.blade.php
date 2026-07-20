@extends('layouts.admin')
@section('title', 'Edit Katering')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('admin.catering.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Katering</a>
        </div>

        <form action="{{ route('admin.catering.update', $catering) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Informasi Katering</h3>
                </div>
                <div class="card-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Katering <span class="text-danger">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $catering->name) }}" class="form-control" placeholder="Nama Katering">
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tipe Katering --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Tipe Katering</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="typeHarian" value="harian" {{ $catering->isHarian() ? 'checked' : '' }} disabled>
                            <label class="form-check-label text-muted" for="typeHarian">Harian</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="typeAcara" value="acara" {{ $catering->isAcara() ? 'checked' : '' }} disabled>
                            <label class="form-check-label text-muted" for="typeAcara">Acara</label>
                        </div>
                        <small class="d-block text-muted mt-1">Tipe Katering tidak dapat diubah setelah dibuat.</small>
                    </div>

                    {{-- Minimal Hari Pemesanan (Hanya Acara) --}}
                    @if($catering->isAcara())
                    <div class="mb-3">
                        <label class="form-label fw-bold">Minimal Hari Pemesanan <span class="text-danger">*</span></label>
                        <input type="number" name="minimal_order_days" value="{{ old('minimal_order_days', $catering->minimal_order_days) }}" min="0" step="1" class="form-control" placeholder="Minimal Hari Pemesanan (contoh: 3)">
                        @error('minimal_order_days')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    @endif

                    {{-- Gambar --}}
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold">Gambar</label>
                        <input type="file" name="image" accept="image/*" class="form-control">
                        <div id="imagePreviewContainer" class="mt-3 {{ $catering->image ? '' : 'd-none' }}">
                            <p class="small text-muted fw-medium mb-1">Preview Gambar:</p>
                            <div class="border rounded bg-light p-1" style="display: inline-block;">
                                <img id="imagePreview" src="{{ $catering->image ? Storage::url($catering->image) : '' }}" alt="Preview Gambar" style="height: 112px; object-fit: cover;">
                            </div>
                        </div>
                        @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $catering->is_active) ? 'checked' : '' }} id="isActiveCheck">
                        <label class="form-check-label fw-bold" for="isActiveCheck">
                            Aktif
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning"><i class="fa-solid fa-save"></i> Perbarui Katering</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
