@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('content')


<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors">← Kembali</a>
    </div>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-4">
        @csrf
        <input type="hidden" name="catering_service_id" value="{{ request('catering_service_id') }}">
        <div><label class="block text-sm font-medium mb-1">Nama Produk *</label><input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border">@error('name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror</div>
        <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border">{{ old('description') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Harga (Rp) *</label><input type="number" name="price" required value="{{ old('price') }}" class="w-full px-4 py-2 rounded-lg border"></div>
            <div>
                <label class="block text-sm font-medium mb-1">Status Produk *</label>
                <select name="status" class="w-full px-4 py-2 rounded-lg border">
                    <option value="tersedia" {{ old('status', 'tersedia') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="habis" {{ old('status') === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
        </div>
        <div><label class="block text-sm font-medium mb-1">Gambar</label><input type="file" name="image" accept="image/*" class="w-full px-4 py-2 rounded-lg border"></div>
        <div class="flex gap-6"><label class="flex items-center gap-2"><input type="checkbox" name="is_best_seller" value="1" class="rounded border-gray-300 text-orange-500"><span class="text-sm">Best Seller</span></label><label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-orange-500"><span class="text-sm">Aktif</span></label></div>
        <div><label class="block text-sm font-medium mb-1">Hari Tersedia *</label><select name="available_days" required class="w-full px-4 py-2 rounded-lg border"><option value="" disabled selected>Pilih Hari</option>@foreach(['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $d)<option value="{{ $d }}" {{ old('available_days') === $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>@endforeach</select>@error('available_days')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror</div>
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
        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600">Simpan Produk</button>
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
    document.addEventListener('DOMContentLoaded', updateExtraTags);
</script>
@endpush
@endsection
