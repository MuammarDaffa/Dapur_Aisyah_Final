@extends('layouts.admin')
@section('title', 'Edit Paket')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            @php
                $backServiceId = $package->layanan_katering_id;
                $backService = $backServiceId ? \App\Models\LayananKatering::find($backServiceId) : null;
            @endphp
            @if($backService)
                <a href="{{ route('admin.catering.show', $backService->id) }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali ke detail katering: {{ $backService->name }}</a>
            @else
                <a href="{{ url()->previous() }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            @endif
        </div>

        <form action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="layanan_katering_id" value="{{ $backServiceId }}">
            
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi Paket</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Paket <span class="text-danger">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $package->name) }}" class="form-control" placeholder="Nama Paket">
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="form-control" placeholder="Deskripsi opsional">{{ old('deskripsi', $package->deskripsi) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="text" name="harga" required value="{{ old('harga', number_format($package->harga, 0, '', '')) }}" class="form-control rupiah-input">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Gambar</label>
                        <input type="file" name="image" accept="image/*" class="form-control" id="image-input">
                        <div id="imagePreviewContainer" class="mt-3 {{ $package->image ? '' : 'd-none' }}">
                            <p class="small text-muted fw-medium mb-1">Preview Gambar:</p>
                            <div class="border rounded bg-light p-1" style="display: inline-block;">
                                <img id="imagePreview" src="{{ $package->image ? Storage::url($package->image) : '' }}" alt="Preview Gambar" style="height: 112px; object-fit: cover;">
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} id="isActiveCheck">
                        <label class="form-check-label fw-bold" for="isActiveCheck">Aktif</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Paket</button>
                    <a href="{{ isset($backService) ? route('admin.catering.show', $backService->id) : url()->previous() }}" class="btn btn-default">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image-input');
        const imgContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files && e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        if (imagePreview) {
                            imagePreview.src = event.target.result;
                            imgContainer.classList.remove('d-none');
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    imgContainer.classList.add('d-none');
                }
            });
        }
    });
</script>
@endpush
@endsection
