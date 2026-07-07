@extends('layouts.admin')
@section('title', 'Edit Paket')
@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-6" id="packageForm">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Layanan Event *</label>
                <select name="catering_service_id" id="serviceSelect" required class="w-full px-4 py-2 rounded-lg border focus:ring-orange-500 focus:border-orange-500" onchange="loadServiceOptions()">
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($services as $s)
                    <option value="{{ $s->id }}" {{ old('catering_service_id', $package->catering_service_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nama Paket *</label>
                <input type="text" name="name" required value="{{ old('name', $package->name) }}" class="w-full px-4 py-2 rounded-lg border focus:ring-orange-500 focus:border-orange-500" placeholder="Contoh: Paket Prasmanan Hemat">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Cover Paket (Opsional)</label>
            @if($package->image)
                <div class="mb-3 flex items-center gap-4 p-3 bg-gray-50 border rounded-xl">
                    <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="w-16 h-16 object-cover rounded-lg border">
                    <div>
                        <p class="text-xs font-semibold text-gray-700">Cover saat ini</p>
                        <p class="text-xs text-gray-400">Unggah file baru di bawah jika ingin mengganti cover.</p>
                    </div>
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 rounded-lg border bg-gray-50 focus:ring-orange-500 focus:border-orange-500 text-sm">
            <p class="text-xs text-gray-500 mt-1">Format gambar: JPG, PNG, WEBP (Maksimal 2MB).</p>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="description" rows="2" class="w-full px-4 py-2 rounded-lg border focus:ring-orange-500 focus:border-orange-500" placeholder="Deskripsi singkat mengenai paket ini...">{{ old('description', $package->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Harga Paket (Rp) *</label>
                <input type="text" name="price" required value="{{ old('price', $package->price) }}" class="w-full px-4 py-2 rounded-lg border focus:ring-orange-500 focus:border-orange-500 rupiah-input" placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Total Porsi (Paket Tetap) *</label>
                <input type="number" name="total_portions" required value="{{ old('total_portions', $package->total_portions) }}" min="1" class="w-full px-4 py-2 rounded-lg border focus:ring-orange-500 focus:border-orange-500">
                <p class="text-xs text-gray-500 mt-1">Konsep paket adalah tetap (fixed package).</p>
            </div>
        </div>

        {{-- Isi Paket & Penyajian --}}
        <div class="border-t pt-6">
            <h3 class="text-base font-bold text-gray-900 mb-4">Isi Paket & Penyajian</h3>
            
            <div id="loadingOptionsMsg" class="text-sm text-gray-500 py-6 text-center border-2 border-dashed rounded-xl bg-gray-50 hidden">
                Memuat daftar menu dan penyajian...
            </div>
            
            <div id="structuredOptionsContainer" class="hidden space-y-6">
                <!-- Checklist Menu -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Pilih Menu yang Termasuk dalam Paket *</label>
                    <p class="text-xs text-gray-500 mb-3">Seluruh menu berasal dari Master Data Menu. Admin cukup mencentang menu yang masuk ke dalam paket ini.</p>
                    <div id="menuChecklistContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-80 overflow-y-auto p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <!-- Diisi oleh JavaScript -->
                    </div>
                </div>

                <!-- Penyajian -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Penyajian *</label>
                    <select name="serving_type_id" id="servingSelect" required class="w-full md:w-1/2 px-4 py-2 rounded-lg border bg-white focus:ring-orange-500 focus:border-orange-500">
                        <option value="">-- Pilih Penyajian --</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Benefit Paket --}}
        <div class="border-t pt-6">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <label class="block text-base font-bold text-gray-900">Benefit Paket</label>
                    <p class="text-xs text-gray-500">Benefit tidak berasal dari Master Data, melainkan diisi manual oleh admin (contoh: Gratis Dessert, Gratis Air Mineral).</p>
                </div>
                <button type="button" onclick="addBenefit()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-sm transition-colors">+ Tambah Benefit</button>
            </div>
            <div id="benefitsList" class="space-y-3 mt-3">
                <!-- Diisi oleh JavaScript -->
            </div>
        </div>

        <div class="flex justify-between items-center pt-6 border-t">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                <span class="text-sm font-medium text-gray-700">Aktifkan Paket</span>
            </label>
            <div class="flex gap-3">
                <a href="{{ route('admin.catering.show', $package->catering_service_id) }}" class="px-6 py-2.5 text-gray-700 bg-gray-100 font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">Update Paket</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
const existingOptionIds = window.existingOptionIds || @json(old('menu_ids', $package->customOptions->pluck('id')));
const oldBenefits = @json(old('benefits', $package->benefits ?? []));
const currentServingId = @json(old('serving_type_id', $package->customOptions->where('type', 'serving_type')->first()?->id));

function addBenefit(val = '') {
    const container = document.getElementById('benefitsList');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 benefit-row';
    div.innerHTML = `
        <span class="text-green-600 font-bold px-2">✓</span>
        <input type="text" name="benefits[]" value="${val.replace(/"/g, '&quot;')}" placeholder="Contoh: Gratis Dessert, Gratis Air Mineral..." class="flex-1 px-4 py-2 border rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
        <button type="button" onclick="this.closest('.benefit-row').remove()" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-bold hover:bg-red-100 transition-colors">&times;</button>
    `;
    container.appendChild(div);
}

function loadServiceOptions() {
    const serviceId = document.getElementById('serviceSelect').value;
    const msg = document.getElementById('loadingOptionsMsg');
    const container = document.getElementById('structuredOptionsContainer');
    const menuContainer = document.getElementById('menuChecklistContainer');
    const srvSelect = document.getElementById('servingSelect');
    
    if (!serviceId) {
        msg.classList.remove('hidden');
        container.classList.add('hidden');
        return;
    }

    msg.classList.remove('hidden');
    msg.textContent = 'Memuat daftar menu...';
    container.classList.add('hidden');

    fetch(`/api/service/${serviceId}/custom-options`)
        .then(r => r.json())
        .then(data => {
            msg.classList.add('hidden');
            container.classList.remove('hidden');
            
            menuContainer.innerHTML = '';
            srvSelect.innerHTML = '<option value="">-- Pilih Penyajian --</option>';
            
            let hasMenu = false;

            data.forEach(opt => {
                const isSelected = existingOptionIds.includes(opt.id) || existingOptionIds.includes(String(opt.id));
                
                if (opt.type === 'menu') {
                    hasMenu = true;
                    const itemsDesc = opt.items ? `<p class="text-xs text-gray-500 mt-1 line-clamp-2">${opt.items}</p>` : '';
                    menuContainer.innerHTML += `
                        <label class="flex items-start gap-3 p-3 bg-white border rounded-xl cursor-pointer hover:border-orange-400 transition-all shadow-sm">
                            <input type="checkbox" name="menu_ids[]" value="${opt.id}" ${isSelected ? 'checked' : ''} class="mt-1 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                            <div class="flex-1">
                                <span class="font-bold text-gray-900 text-sm">${opt.name}</span>
                                ${itemsDesc}
                            </div>
                        </label>
                    `;
                } else if (opt.type === 'serving_type') {
                    const selectedAttr = (currentServingId == opt.id) ? 'selected' : '';
                    srvSelect.innerHTML += `<option value="${opt.id}" ${selectedAttr}>${opt.name}</option>`;
                }
            });

            if (!hasMenu) {
                menuContainer.innerHTML = '<div class="col-span-2 text-center py-6 text-sm text-gray-400">Belum ada Master Data Menu untuk layanan ini.</div>';
            }
        })
        .catch(err => {
            console.error('Error loading options:', err);
            msg.textContent = 'Gagal memuat data menu.';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('serviceSelect').value) {
        loadServiceOptions();
    }
    
    // Populate benefits
    if (oldBenefits && oldBenefits.length > 0) {
        oldBenefits.forEach(b => addBenefit(b));
    } else {
        addBenefit('Gratis Air Mineral');
        addBenefit('Gratis Sendok & Tissue');
    }
});
</script>
@endpush
@endsection
