@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('content')


<div class="max-w-2xl">
    <div class="mb-6">
        @php
            $backService = request('catering_service_id') ? \App\Models\CateringService::find(request('catering_service_id')) : null;
        @endphp
        @if($backService)
            <a href="{{ route('admin.catering.show', $backService->id) }}" class="fs-6 text-secondary hover:text-primary fw-medium">← Kembali ke detail katering: {{ $backService->name }}</a>
        @else
            <a href="{{ url()->previous() }}" class="fs-6 text-secondary hover:text-primary fw-medium">← Kembali</a>
        @endif
    </div>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="card shadow-sm mb-4 p-4">
        @csrf
        <input type="hidden" name="catering_service_id" value="{{ request('catering_service_id') }}">
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Produk *</label><input type="text" name="name" required value="{{ old('name') }}" class="form-control w-100 px-4 py-2 rounded border">@error('name')<p class="text-danger fs-6">{{ $message }}</p>@enderror</div>
        <div class="mb-3">
            <label class="form-label fw-bold">Deskripsi</label><textarea name="description" rows="3" class="form-control w-100 px-4 py-2 rounded border">{{ old('description') }}</textarea></div>
        <div class="mb-3">
            <label class="form-label fw-bold">Harga (Rp) *</label><input type="text" name="price" required value="{{ old('price') }}" class="form-control w-100 px-4 py-2 rounded border rupiah-input"></div>
        <div class="mb-3">
            <label class="form-label fw-bold">Gambar</label>
            <div class="d-flex d-flex-column sm:d-flex-row items-start g-3 mt-1">
                <div class="w-36 h-36 flex-shrink-0 rounded border border border-secondary bg-light d-flex d-flex-column align-items-center justify-content-center overflow-hidden position-relative shadow-sm">
                    <img src="" id="image-preview" alt="Preview Gambar" class="d-none w-100 h-100 object-fit-cover">
                    <div id="image-placeholder" class="d-flex d-flex-column align-items-center justify-content-center text-center p-2 text-secondary">
                        <svg style="width: 32px; height: 32px;" class="mb-1 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="small fw-medium">Belum ada gambar</span>
                    </div>
                </div>
                <div class="d-flex-1 w-100">
                    <input type="file" name="image" id="image-input" accept="image/*" class="w-100 px-4 py-2 rounded border">
                </div>
            </div>
        <div class="d-flex g-3"><label class="d-flex align-items-center g-3"><input type="checkbox" name="is_active" value="1" checked class="rounded border border-secondary text-primary"><span class="fs-6">Aktif</span></label></div>
        @if(isset($extras) && $extras->count() > 0)
        @php $selectedExtras = old('extras', []); @endphp
        <div class="d-flex flex-column gap-2" id="extra-selector-container">
            <label class="form-label fw-bold">Extra Tambahan (Opsional)</label>
            
            <!-- Tags Container -->
            <div id="selected-extras-tags" class="d-flex d-flex-wrap g-3 empty:d-none">
                <!-- Tags will be rendered here by JS -->
            </div>

            <!-- Dropdown Toggle -->
            <div class="position-relative">
                <button type="button" onclick="toggleExtraDropdown()" class="d-flex align-items-center g-3 px-4 py-2 bg-white border border border-secondary rounded fs-6 fw-medium text-secondary hover:bg-light focus: -2">
                    <svg style="width: 16px; height: 16px;" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    Tambah Extra
                </button>

                <!-- Dropdown Menu -->
                <div id="extra-dropdown" class="d-none position-absolute mt-2 w-64 bg-white border border border-secondary rounded shadow max-h-60 overflow-y-auto">
                    <div class="p-2 space-y-1">
                        @foreach($extras as $extra)
                            @php $isChecked = in_array($extra->id, $selectedExtras); @endphp
                            <label class="d-flex align-items-center g-3 px-3 py-2 hover:bg-primary text-white rounded cursor-pointer">
                                <input type="checkbox" name="extras[]" value="{{ $extra->id }}" data-name="{{ $extra->name }}" onchange="updateExtraTags()" class="w-4 h-4 text-primary border border-secondary rounded extra-checkbox-input" {{ $isChecked ? 'checked' : '' }}>
                                <span class="fs-6 text-secondary fw-medium">{{ $extra->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @error('extras')<p class="text-danger fs-6 mt-1">{{ $message }}</p>@enderror
        </div>
        @endif
        <div class="d-flex justify-content-end g-3 pt-4 border-t">
            <a href="{{ isset($backService) ? route('admin.catering.show', $backService->id) : url()->previous() }}" class="px-6 py-2.5 text-secondary bg-light fw-medium rounded hover:bg-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleExtraDropdown() {
        const dropdown = document.getElementById('extra-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('extra-selector-container');
        const dropdown = document.getElementById('extra-dropdown');
        if (container && dropdown && !container.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    function updateExtraTags() {
        const checkboxes = document.querySelectorAll('.extra-checkbox-input');
        const tagsContainer = document.getElementById('selected-extras-tags');
        if (!tagsContainer) return;
        
        tagsContainer.innerHTML = ''; // Clear current tags

        checkboxes.forEach(cb => {
            if (cb.checked) {
                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center gap-1.5 px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full border border-orange-200 shadow-sm';
                
                const label = document.createElement('span');
                label.textContent = cb.getAttribute('data-name');
                
                const closeBtn = document.createElement('button');
                closeBtn.type = 'button';
                closeBtn.className = 'flex items-center justify-center w-4 h-4 rounded-full text-orange-600 hover:bg-orange-200 hover:text-orange-900 transition-colors focus:outline-none';
                closeBtn.innerHTML = '×';
                closeBtn.onclick = function() {
                    cb.checked = false;
                    updateExtraTags();
                };

                tag.appendChild(label);
                tag.appendChild(closeBtn);
                tagsContainer.appendChild(tag);
            }
        });
    }

    // Initialize tags on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateExtraTags();

        // === Image Preview Script ===
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        const imagePlaceholder = document.getElementById('image-placeholder');
        const originalSrc = imagePreview && !imagePreview.classList.contains('hidden') ? imagePreview.src : null;

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files && e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        if (imagePreview) {
                            imagePreview.src = event.target.result;
                            imagePreview.classList.remove('hidden');
                        }
                        if (imagePlaceholder) {
                            imagePlaceholder.classList.add('hidden');
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    if (originalSrc) {
                        if (imagePreview) {
                            imagePreview.src = originalSrc;
                            imagePreview.classList.remove('hidden');
                        }
                        if (imagePlaceholder) {
                            imagePlaceholder.classList.add('hidden');
                        }
                    } else {
                        if (imagePreview) {
                            imagePreview.src = '';
                            imagePreview.classList.add('hidden');
                        }
                        if (imagePlaceholder) {
                            imagePlaceholder.classList.remove('hidden');
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
