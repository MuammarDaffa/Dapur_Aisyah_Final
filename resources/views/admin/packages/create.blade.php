@extends('layouts.admin')
@section('title', 'Tambah Paket')
@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.packages.store') }}" method="POST" class="bg-white rounded-xl p-6 shadow-sm border space-y-5" id="packageForm">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Layanan Event *</label>
                <select name="catering_service_id" id="serviceSelect" required class="w-full px-4 py-2 rounded-lg border" onchange="loadServiceOptions()">
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($services as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nama Paket *</label>
                <input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border">
            </div>
        </div>

        <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="description" rows="2" class="w-full px-4 py-2 rounded-lg border">{{ old('description') }}</textarea></div>

        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium mb-1">Harga Paket (Rp) *</label><input type="number" name="price" required value="{{ old('price') }}" class="w-full px-4 py-2 rounded-lg border"></div>
            <div><label class="block text-sm font-medium mb-1">Total Porsi *</label><input type="number" name="total_portions" required value="{{ old('total_portions') }}" min="1" class="w-full px-4 py-2 rounded-lg border"></div>
            <div><label class="block text-sm font-medium mb-1">Min. Tambahan</label><input type="number" name="min_addition_qty" value="{{ old('min_addition_qty', 0) }}" min="0" class="w-full px-4 py-2 rounded-lg border"><p class="text-xs text-gray-500 mt-1">0 = tanpa minimum</p></div>
        </div>

        {{-- Custom Options Selector --}}
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-4 border-b pb-2">Isi Paket</label>
            <div id="loadingOptionsMsg" class="text-sm text-gray-400 py-4 text-center border-2 border-dashed rounded-lg bg-gray-50">Pilih layanan terlebih dahulu untuk memuat opsi paket.</div>
            
            <div id="structuredOptionsContainer" class="hidden space-y-5 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <!-- Menu -->
                <div class="relative" id="menuMultiselect">
                    <label class="block text-sm font-medium mb-1">Menu *</label>
                    <div class="min-h-[42px] p-2 border border-gray-300 rounded-lg bg-white cursor-pointer flex flex-wrap gap-2 items-center" onclick="toggleDropdown('menuDropdown')">
                        <div id="menuChips" class="flex flex-wrap gap-2 empty:hidden"></div>
                        <span id="menuPlaceholder" class="text-gray-400 text-sm">Pilih Menu...</span>
                        <div class="ml-auto text-gray-400 text-xs">▼</div>
                    </div>
                    <div id="menuDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto p-2"></div>
                    <select name="menu_ids[]" id="menuHiddenSelect" multiple class="hidden"></select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Dekorasi -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Dekorasi *</label>
                        <select name="decoration_id" id="decorationSelect" required class="w-full px-4 py-2 rounded-lg border focus:ring-orange-500 focus:border-orange-500">
                            <option value="">-- Pilih Dekorasi --</option>
                        </select>
                    </div>
                    <!-- Penyajian -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Penyajian *</label>
                        <select name="serving_type_id" id="servingSelect" required class="w-full px-4 py-2 rounded-lg border focus:ring-orange-500 focus:border-orange-500">
                            <option value="">-- Pilih Penyajian --</option>
                        </select>
                    </div>
                </div>

                <!-- Extra -->
                <div class="relative" id="extraMultiselect">
                    <label class="block text-sm font-medium mb-1">Extra <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                    <div class="min-h-[42px] p-2 border border-gray-300 rounded-lg bg-white cursor-pointer flex flex-wrap gap-2 items-center" onclick="toggleDropdown('extraDropdown')">
                        <div id="extraChips" class="flex flex-wrap gap-2 empty:hidden"></div>
                        <span id="extraPlaceholder" class="text-gray-400 text-sm">Pilih Extra...</span>
                        <div class="ml-auto text-gray-400 text-xs">▼</div>
                    </div>
                    <div id="extraDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto p-2"></div>
                    <select name="extra_ids[]" id="extraHiddenSelect" multiple class="hidden"></select>
                </div>
            </div>
        </div>

        <div class="flex gap-4 pt-4 border-t">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_custom" value="1" class="rounded border-gray-300 text-orange-500"><span class="text-sm">Paket Custom (placeholder Full Custom)</span></label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-orange-500"><span class="text-sm">Aktif</span></label>
        </div>

        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600">Simpan Paket</button>
    </form>
</div>

@push('scripts')
<script>
let multiselectData = { menu: [], extra: [] };
let currentSelections = { menu: [], extra: [] };
const existingOptionIds = window.existingOptionIds || [];

function toggleDropdown(id) {
    document.getElementById(id).classList.toggle('hidden');
}

document.addEventListener('click', (e) => {
    const menuEl = document.getElementById('menuMultiselect');
    const extraEl = document.getElementById('extraMultiselect');
    if (menuEl && !menuEl.contains(e.target)) document.getElementById('menuDropdown').classList.add('hidden');
    if (extraEl && !extraEl.contains(e.target)) document.getElementById('extraDropdown').classList.add('hidden');
});

function updateMultiselectUI(type, hiddenSelectId, chipsId, placeholderId, dropdownId) {
    const hiddenSelect = document.getElementById(hiddenSelectId);
    const chipsContainer = document.getElementById(chipsId);
    const placeholder = document.getElementById(placeholderId);
    const dropdown = document.getElementById(dropdownId);
    const dataOptions = multiselectData[type];
    const selectedIds = currentSelections[type];
    
    hiddenSelect.innerHTML = '';
    chipsContainer.innerHTML = '';
    dropdown.innerHTML = '';
    
    let hasSelected = false;

    dataOptions.forEach(opt => {
        const isSelected = selectedIds.includes(opt.id);
        if (isSelected) hasSelected = true;

        hiddenSelect.innerHTML += `<option value="${opt.id}" ${isSelected ? 'selected' : ''}>${opt.name}</option>`;

        if (isSelected) {
            chipsContainer.innerHTML += `
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 text-sm rounded-md" onclick="event.stopPropagation()">
                    ${opt.name}
                    <button type="button" onclick="toggleOption('${type}', ${opt.id}, event)" class="hover:text-orange-900">&times;</button>
                </span>
            `;
        }

        dropdown.innerHTML += `
            <label class="flex items-center gap-2 p-2 hover:bg-gray-50 cursor-pointer rounded" onclick="event.stopPropagation()">
                <input type="checkbox" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" 
                       ${isSelected ? 'checked' : ''} 
                       onchange="toggleOption('${type}', ${opt.id}, event)">
                <span class="text-sm text-gray-700">${opt.name}</span>
            </label>
        `;
    });

    if (hasSelected) {
        placeholder.classList.add('hidden');
    } else {
        placeholder.classList.remove('hidden');
    }
}

function toggleOption(type, id, event) {
    if (event) event.stopPropagation();
    const index = currentSelections[type].indexOf(id);
    if (index === -1) {
        currentSelections[type].push(id);
    } else {
        currentSelections[type].splice(index, 1);
    }
    
    if (type === 'menu') {
        updateMultiselectUI('menu', 'menuHiddenSelect', 'menuChips', 'menuPlaceholder', 'menuDropdown');
    } else {
        updateMultiselectUI('extra', 'extraHiddenSelect', 'extraChips', 'extraPlaceholder', 'extraDropdown');
    }
}

function loadServiceOptions(callback) {
    const serviceId = document.getElementById('serviceSelect').value;
    const msg = document.getElementById('loadingOptionsMsg');
    const container = document.getElementById('structuredOptionsContainer');
    
    if (!serviceId) {
        msg.classList.remove('hidden');
        if (window.existingOptionIds) msg.textContent = 'Pilih layanan terlebih dahulu untuk memuat opsi paket.';
        container.classList.add('hidden');
        return;
    }

    if (window.existingOptionIds) {
        msg.classList.remove('hidden');
        msg.textContent = 'Memuat opsi...';
        container.classList.add('hidden');
    }

    fetch(`/api/service/${serviceId}/custom-options`)
        .then(r => r.json())
        .then(data => {
            msg.classList.add('hidden');
            container.classList.remove('hidden');
            
            const decSelect = document.getElementById('decorationSelect');
            const srvSelect = document.getElementById('servingSelect');
            
            // Reset
            decSelect.innerHTML = '<option value="">-- Pilih Dekorasi --</option>';
            srvSelect.innerHTML = '<option value="">-- Pilih Penyajian --</option>';
            multiselectData.menu = [];
            multiselectData.extra = [];
            
            // Do not clear currentSelections if we are just switching back to a previously loaded state, 
            // but for simplicity, we clear it unless it's the initial load for edit
            if (!window._initialLoadDone && window.existingOptionIds) {
                // Keep selections from existingOptionIds
                window._initialLoadDone = true;
                currentSelections.menu = existingOptionIds;
                currentSelections.extra = existingOptionIds;
            } else {
                currentSelections.menu = [];
                currentSelections.extra = [];
            }
            
            data.forEach(opt => {
                const isSelected = window.existingOptionIds ? existingOptionIds.includes(opt.id) : false;
                
                if (opt.type === 'menu') {
                    multiselectData.menu.push(opt);
                } else if (opt.type === 'extra') {
                    multiselectData.extra.push(opt);
                } else {
                    const selectedAttr = isSelected ? 'selected' : '';
                    const optElement = `<option value="${opt.id}" ${selectedAttr}>${opt.name}</option>`;
                    if (opt.type === 'decoration') decSelect.innerHTML += optElement;
                    if (opt.type === 'serving_type') srvSelect.innerHTML += optElement;
                }
            });

            updateMultiselectUI('menu', 'menuHiddenSelect', 'menuChips', 'menuPlaceholder', 'menuDropdown');
            updateMultiselectUI('extra', 'extraHiddenSelect', 'extraChips', 'extraPlaceholder', 'extraDropdown');

            if (callback) callback();
        });
}

// For edit.blade.php
if (window.existingOptionIds && window.existingOptionIds.length > 0) {
    document.addEventListener('DOMContentLoaded', () => {
        loadServiceOptions();
    });
}
</script>
@endpush
@endsection
