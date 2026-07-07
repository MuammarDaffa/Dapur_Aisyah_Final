@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('content')


<div class="max-w-2xl">
    <div class="mb-6">
        @php
            $backService = request('catering_service_id') ? \App\Models\CateringService::find(request('catering_service_id')) : null;
        @endphp
        @if($backService)
            <a href="{{ route('admin.catering.show', $backService->id) }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors font-medium">← Kembali ke detail katering: {{ $backService->name }}</a>
        @else
            <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors font-medium">← Kembali</a>
        @endif
    </div>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-4">
        @csrf
        <input type="hidden" name="catering_service_id" value="{{ request('catering_service_id') }}">
        <div><label class="block text-sm font-medium mb-1">Nama Produk *</label><input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border">@error('name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror</div>
        <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border">{{ old('description') }}</textarea></div>
        <div><label class="block text-sm font-medium mb-1">Harga (Rp) *</label><input type="text" name="price" required value="{{ old('price') }}" class="w-full px-4 py-2 rounded-lg border rupiah-input"></div>
        <div>
            <label class="block text-sm font-medium mb-1">Gambar</label>
            <div class="flex flex-col sm:flex-row items-start gap-4 mt-1">
                <div class="w-36 h-36 shrink-0 rounded-xl border border-gray-200 bg-gray-50 flex flex-col items-center justify-center overflow-hidden relative shadow-sm">
                    <img src="" id="image-preview" alt="Preview Gambar" class="hidden w-full h-full object-cover">
                    <div id="image-placeholder" class="flex flex-col items-center justify-center text-center p-2 text-gray-400">
                        <svg class="w-8 h-8 mb-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-xs font-medium">Belum ada gambar</span>
                    </div>
                </div>
                <div class="flex-1 w-full">
                    <input type="file" name="image" id="image-input" accept="image/*" class="w-full px-4 py-2 rounded-lg border">
                </div>
            </div>
        </div>
        <div class="flex gap-6"><label class="flex items-center gap-2"><input type="checkbox" name="is_best_seller" value="1" class="rounded border-gray-300 text-orange-500"><span class="text-sm">Best Seller</span></label><label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-orange-500"><span class="text-sm">Aktif</span></label></div>
        @if(isset($extras) && $extras->count() > 0)
        @php $selectedExtras = old('extras', []); @endphp
        <div class="space-y-3" id="extra-selector-container">
            <label class="block text-sm font-medium">Extra Tambahan (Opsional)</label>
            
            <!-- Tags Container -->
            <div id="selected-extras-tags" class="flex flex-wrap gap-2 empty:hidden">
                <!-- Tags will be rendered here by JS -->
            </div>

            <!-- Dropdown Toggle -->
            <div class="relative">
                <button type="button" onclick="toggleExtraDropdown()" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    Tambah Extra
                </button>

                <!-- Dropdown Menu -->
                <div id="extra-dropdown" class="hidden absolute z-10 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <div class="p-2 space-y-1">
                        @foreach($extras as $extra)
                            @php $isChecked = in_array($extra->id, $selectedExtras); @endphp
                            <label class="flex items-center gap-3 px-3 py-2 hover:bg-orange-50 rounded-md cursor-pointer transition-colors">
                                <input type="checkbox" name="extras[]" value="{{ $extra->id }}" data-name="{{ $extra->name }}" onchange="updateExtraTags()" class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500 extra-checkbox-input" {{ $isChecked ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 font-medium">{{ $extra->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @error('extras')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        @endif
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ isset($backService) ? route('admin.catering.show', $backService->id) : url()->previous() }}" class="px-6 py-2.5 text-gray-700 bg-gray-100 font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">Simpan Produk</button>
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
