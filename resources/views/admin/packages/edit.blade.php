@extends('layouts.admin')
@section('title', 'Edit Paket')
@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data" class="card shadow-sm mb-4 p-4" id="packageForm">
        @csrf @method('PUT')

        <div class="row row-cols-1 md:row-cols-2 g-3">
            <div class="mb-3">
            <label class="form-label fw-bold">Layanan Event *</label>
                <select name="catering_service_id" id="serviceSelect" required class="form-select w-100 px-4 py-2 rounded border focus:border border-primary" onchange="loadServiceOptions()">
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($services as $s)
                    <option value="{{ $s->id }}" {{ old('catering_service_id', $package->catering_service_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
            <label class="form-label fw-bold">Nama Paket *</label>
                <input type="text" name="name" required value="{{ old('name', $package->name) }}" class="w-100 px-4 py-2 rounded border focus:border border-primary" placeholder="Contoh: Paket Prasmanan Hemat">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Cover Paket (Opsional)</label>
            @if($package->image)
                <div class="mb-3 d-flex align-items-center g-3 p-3 bg-light border rounded">
                    <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="w-16 h-16 object-cover rounded border">
                    <div>
                        <p class="small fw-bold text-secondary">Cover saat ini</p>
                        <p class="small text-secondary">Unggah file baru di bawah jika ingin mengganti cover.</p>
                    </div>
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="w-100 px-4 py-2 rounded border bg-light focus:border border-primary fs-6">
            <p class="small text-secondary mt-1">Format gambar: JPG, PNG, WEBP (Maksimal 2MB).</p>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Deskripsi</label>
            <textarea name="description" rows="2" class="form-control w-100 px-4 py-2 rounded border focus:border border-primary" placeholder="Deskripsi singkat mengenai paket ini...">{{ old('description', $package->description) }}</textarea>
        </div>

        <div class="row row-cols-1 md:row-cols-2 g-3">
            <div class="mb-3">
            <label class="form-label fw-bold">Harga Paket (Rp) *</label>
                <input type="text" name="price" required value="{{ old('price', $package->price) }}" class="w-100 px-4 py-2 rounded border focus:border border-primary rupiah-input" placeholder="0">
            </div>
            <div class="mb-3">
            <label class="form-label fw-bold">Total Porsi (Paket Tetap) *</label>
                <input type="number" name="total_portions" required value="{{ old('total_portions', $package->total_portions) }}" min="1" class="w-100 px-4 py-2 rounded border focus:border border-primary">
                <p class="small text-secondary mt-1">Konsep paket adalah tetap (fixed package).</p>
            </div>
        </div>

        {{-- Isi Paket & Penyajian --}}
        <div class="border-t pt-6">
            <h3 class="text-base fw-bold text-secondary mb-4">Isi Paket & Penyajian</h3>
            
            <div id="loadingOptionsMsg" class="fs-6 text-secondary py-6 text-center border-2 border-dashed rounded bg-light d-none">
                Memuat daftar menu dan penyajian...
            </div>
            
            <div id="structuredOptionsContainer" class="d-none space-y-6">
                <!-- Checklist Menu -->
                <div class="mb-3">
            <label class="form-label fw-bold">Pilih Menu yang Termasuk dalam Paket *</label>
                    <p class="small text-secondary mb-3">Seluruh menu berasal dari Master Data Menu. Admin cukup mencentang menu yang masuk ke dalam paket ini.</p>
                    <div id="menuChecklistContainer" class="row row-cols-1 md:row-cols-2 g-3 max-h-80 overflow-y-auto p-3 bg-light rounded border border border-secondary">
                        <!-- Diisi oleh JavaScript -->
                    </div>
                </div>

                <!-- Penyajian -->
                <div class="mb-3">
            <label class="form-label fw-bold">Penyajian *</label>
                    <select name="serving_type_id" id="servingSelect" required class="form-select w-100 md:w-1/2 px-4 py-2 rounded border bg-white focus:border border-primary">
                        <option value="">-- Pilih Penyajian --</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Benefit Paket --}}
        <div class="border-t pt-6">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="mb-3">
            <label class="form-label fw-bold">Benefit Paket</label>
                    <p class="small text-secondary">Benefit tidak berasal dari Master Data, melainkan diisi manual oleh admin (contoh: Gratis Dessert, Gratis Air Mineral).</p>
                </div>
                <button type="button" onclick="addBenefit()" class="px-4 py-2 bg-light hover:bg-light text-secondary fw-medium rounded fs-6">+ Tambah Benefit</button>
            </div>
            <div id="benefitsList" class="d-flex flex-column gap-2 mt-3">
                <!-- Diisi oleh JavaScript -->
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-6 border-t">
            <label class="d-flex align-items-center g-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="rounded border border-secondary text-primary">
                <span class="fs-6 fw-medium text-secondary">Aktifkan Paket</span>
            </label>
            <div class="d-flex g-3">
                <a href="{{ route('admin.catering.show', $package->catering_service_id) }}" class="px-6 py-2.5 text-secondary bg-light fw-medium rounded hover:bg-light">Batal</a>
                <button type="submit" class="btn btn-primary">Update Paket</button>
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
        <svg style="width: 20px; height: 20px;" class="text-success flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <input type="text" name="benefits[]" value="${val.replace(/"/g, '&quot;')}" placeholder="Contoh: Gratis Dessert, Gratis Air Mineral..." class="form-control d-flex-1 px-4 py-2 border rounded fs-6 focus:border border-primary">
        <button type="button" onclick="this.closest('.benefit-row').remove()" class="btn btn-outline-danger btn btn-danger px-3 py-2 bg-danger text-white text-danger rounded fs-6 fw-bold hover:bg-danger text-white">&times;</button>
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
                    const itemsDesc = opt.items ? `<p class="small text-secondary mt-1 line-clamp-2">${opt.items}</p>` : '';
                    menuContainer.innerHTML += `
                        <label class="d-flex items-start g-3 p-3 bg-white border rounded cursor-pointer hover:border border-primary shadow-sm">
                            <input type="checkbox" name="menu_ids[]" value="${opt.id}" ${isSelected ? 'checked' : ''} class="mt-1 rounded border border-secondary text-primary">
                            <div class="d-flex-1">
                                <span class="fw-bold text-secondary fs-6">${opt.name}</span>
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
                menuContainer.innerHTML = '<div class="col-span-2 text-center py-6 fs-6 text-secondary">Belum ada Master Data Menu untuk layanan ini.</div>';
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
