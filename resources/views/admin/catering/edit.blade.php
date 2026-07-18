@extends('layouts.admin')
@section('title', 'Edit Katering')
@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.catering.index') }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors">← Kembali ke Daftar Katering</a>
    </div>

    <form action="{{ route('admin.catering.update', $catering) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-5">
        @csrf @method('PUT')

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Katering *</label>
            <input type="text" name="name" required value="{{ old('name', $catering->name) }}"
                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
            <textarea name="description" rows="3" required
                      class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">{{ old('description', $catering->description) }}</textarea>
        </div>

        {{-- Tipe Katering (Read-only setelah dibuat) --}}
        @php
            $currentType = $catering->isDaily() ? 'daily' : ($catering->isEvent() ? 'event' : 'daily');
        @endphp
        <input type="hidden" name="catering_type" value="{{ $currentType }}">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Katering</label>
            <div class="p-4 border-2 rounded-xl {{ $currentType === 'daily' ? 'border-blue-300 bg-blue-50' : 'border-purple-300 bg-purple-50' }}">
                <div class="flex items-center gap-3">
                    @if($currentType === 'daily')
                    <svg class="w-6 h-6 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    @else
                    <svg class="w-6 h-6 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-800">{{ $currentType === 'daily' ? 'Daily' : 'Event' }}</p>
                        <p class="text-xs text-gray-500">{{ $currentType === 'daily' ? 'Menu harian dengan produk' : 'Acara dengan paket catering' }}</p>
                    </div>
                    <!-- <span class="ml-auto inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Tidak dapat diubah</span>
                    </span> -->
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-1">Tipe katering tidak dapat diubah setelah dibuat.</p>
        </div>

        {{-- Harga & Porsi --}}
        <div class="grid grid-cols-1 {{ !$catering->isDaily() ? 'sm:grid-cols-3' : '' }} gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Dasar (Rp) *</label>
                <input type="text" name="base_price" required value="{{ old('base_price', $catering->base_price) }}"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500 rupiah-input">
            </div>
            @if(!$catering->isDaily())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Min. Porsi *</label>
                <input type="number" name="min_portion" required value="{{ old('min_portion', $catering->min_portion) }}" min="1"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max. Porsi</label>
                <input type="number" name="max_portion" value="{{ old('max_portion', $catering->max_portion) }}" placeholder="Tidak dibatasi"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
            @endif
        </div>

        @if(!$catering->isDaily())
        {{-- Ketentuan & Jadwal (Hanya Event) --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ketentuan Pemesanan</label>
                <textarea name="order_terms" rows="2"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">{{ old('order_terms', $catering->order_terms) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Jadwal</label>
                <textarea name="schedule_notes" rows="2"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">{{ old('schedule_notes', $catering->schedule_notes) }}</textarea>
            </div>
        </div>

        {{-- Minimal Hari Pemesanan (Hanya Event) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Minimal Hari Pemesanan</label>
            <select name="minimal_order_days" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                @for($i = 1; $i <= 14; $i++)
                    <option value="{{ $i }}" {{ old('minimal_order_days', $catering->minimal_order_days) == $i ? 'selected' : '' }}>{{ $i }} Hari</option>
                @endfor
            </select>
        </div>
        @endif

        {{-- Gambar --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-50 file:text-orange-700 file:font-medium hover:file:bg-orange-100">
            @if($catering->image)
            <p class="text-xs text-gray-500 mt-1 inline-flex items-center gap-1">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Gambar saat ini tersimpan. Upload baru untuk mengganti.</span>
            </p>
            @endif
            <div id="imagePreviewContainer" class="{{ $catering->image ? '' : 'hidden' }} mt-3">
                <p class="text-xs text-gray-500 mb-1.5 font-medium">Preview Gambar:</p>
                <div class="inline-block border border-gray-200 rounded-xl overflow-hidden bg-gray-50 shadow-sm">
                    <img id="imagePreview" src="{{ $catering->image ? asset('storage/' . $catering->image) : '' }}" alt="Preview Gambar" class="h-28 w-auto object-cover block">
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="flex items-center gap-3 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $catering->is_active ? 'checked' : '' }}
                       class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="pt-4 border-t">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
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
