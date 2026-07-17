@extends('layouts.admin')
@section('title', 'Tambah Katering')
@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.catering.index') }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors">← Kembali ke Daftar Katering</a>
    </div>

    <form action="{{ route('admin.catering.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-5">
        @csrf

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Katering *</label>
            <input type="text" name="name" required value="{{ old('name') }}"
                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500"
                   placeholder="cth: Katering Harian">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
            <textarea name="description" rows="3" required
                      class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500"
                      placeholder="Deskripsi singkat katering...">{{ old('description') }}</textarea>
        </div>

        {{-- Tipe Katering --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Katering *</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" name="catering_type" value="daily" {{ old('catering_type', 'daily') === 'daily' ? 'checked' : '' }}
                           onchange="toggleCateringTypeFields()" class="peer sr-only">
                    <div class="p-4 border-2 rounded-xl transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <div>
                                <p class="font-semibold text-gray-800">Daily</p>
                                <p class="text-xs text-gray-500">Menu harian dengan produk</p>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="catering_type" value="event" {{ old('catering_type') === 'event' ? 'checked' : '' }}
                           onchange="toggleCateringTypeFields()" class="peer sr-only">
                    <div class="p-4 border-2 rounded-xl transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-gray-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            <div>
                                <p class="font-semibold text-gray-800">Event</p>
                                <p class="text-xs text-gray-500">Acara dengan paket catering</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            @error('catering_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Harga & Porsi --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="base_price_grid">
            <div id="base_price_wrapper" class="sm:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Dasar (Rp) *</label>
                <input type="text" name="base_price" required value="{{ old('base_price') }}" 
                       class="w-full pl-4 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all rupiah-input">
            </div>
            <div id="min_portion_wrapper">
                <label class="block text-sm font-medium text-gray-700 mb-1">Min. Porsi *</label>
                <input type="number" name="min_portion" value="{{ old('min_portion', 1) }}" min="1"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div id="max_portion_wrapper">
                <label class="block text-sm font-medium text-gray-700 mb-1">Max. Porsi</label>
                <input type="number" name="max_portion" value="{{ old('max_portion') }}" placeholder="Tidak dibatasi"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
        </div>

        {{-- Ketentuan & Jadwal (Hanya Event) --}}
        <div id="terms_schedule_wrapper" class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ketentuan Pemesanan</label>
                <textarea name="order_terms" rows="2"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500"
                          placeholder="Ketentuan khusus...">{{ old('order_terms') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Jadwal</label>
                <textarea name="schedule_notes" rows="2"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500"
                          placeholder="Info jadwal...">{{ old('schedule_notes') }}</textarea>
            </div>
        </div>

        {{-- Pengaturan Cutoff (Hanya Event) --}}
        <div id="cutoff_wrapper" class="bg-orange-50/50 border border-orange-100 rounded-xl p-4 space-y-4">
            <div>
                <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Pengaturan Cutoff Pemesanan</span>
                </h4>
                <p class="text-xs text-gray-500 mt-0.5">Batas waktu minimal pemesanan untuk layanan ini</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Minimal Hari Pemesanan</label>
                <input type="number" name="minimal_order_days" value="{{ old('minimal_order_days') }}" min="0" placeholder="cth: 3"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                <p class="text-xs text-gray-400 mt-1">Jumlah hari minimal sebelum tanggal acara/pengiriman</p>
            </div>
        </div>

        {{-- Gambar --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-50 file:text-orange-700 file:font-medium hover:file:bg-orange-100">
            <div id="imagePreviewContainer" class="hidden mt-3">
                <p class="text-xs text-gray-500 mb-1.5 font-medium">Preview Gambar:</p>
                <div class="inline-block border border-gray-200 rounded-xl overflow-hidden bg-gray-50 shadow-sm">
                    <img id="imagePreview" src="" alt="Preview Gambar" class="h-28 w-auto object-cover block">
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="flex items-center gap-3 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="pt-4 border-t">
            <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
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
            baseWrapper.classList.remove('sm:col-span-1');
        } else {
            baseGrid.classList.remove('sm:grid-cols-1');
            baseGrid.classList.add('sm:grid-cols-3');
            baseWrapper.classList.add('sm:col-span-1');
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
