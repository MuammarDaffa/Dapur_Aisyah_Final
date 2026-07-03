@extends('layouts.admin')
@section('title', 'Tambah Paket')
@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        @php
            $backService = request('catering_service_id') ? collect($services)->firstWhere('id', request('catering_service_id')) : null;
        @endphp
        @if($backService)
            <a href="{{ route('admin.catering.show', $backService->id) }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors font-medium">← Kembali ke detail katering: {{ $backService->name }}</a>
        @else
            <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors font-medium">← Kembali</a>
        @endif
    </div>
    <form action="{{ route('admin.packages.store') }}" method="POST" class="bg-white rounded-xl p-6 shadow-sm border space-y-5" id="packageForm">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Layanan Event *</label>
                @if(request('catering_service_id'))
                    @php
                        $selectedService = collect($services)->firstWhere('id', request('catering_service_id'));
                    @endphp
                    <div class="w-full px-4 py-2 rounded-lg border bg-gray-50 text-gray-700 font-medium">
                        {{ $selectedService ? $selectedService->name : 'Layanan tidak ditemukan' }}
                    </div>
                    <input type="hidden" name="catering_service_id" id="serviceSelect" value="{{ request('catering_service_id') }}">
                @else
                    <select name="catering_service_id" id="serviceSelect" required class="w-full px-4 py-2 rounded-lg border" onchange="loadServiceOptions()">
                        <option value="">-- Pilih Layanan --</option>
                        @foreach($services as $s)
                        <option value="{{ $s->id }}" {{ old('catering_service_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nama Paket *</label>
                <input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border">
            </div>
        </div>

        <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="description" rows="2" class="w-full px-4 py-2 rounded-lg border">{{ old('description') }}</textarea></div>

        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Harga Paket (Rp) *</label><input type="text" name="price" required value="{{ old('price') }}" class="w-full px-4 py-2 rounded-lg border rupiah-input"></div>
            <div><label class="block text-sm font-medium mb-1">Total Porsi *</label><input type="number" name="total_portions" required value="{{ old('total_portions') }}" min="1" class="w-full px-4 py-2 rounded-lg border"></div>
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

                <div>
                    <!-- Penyajian -->
                    <label class="block text-sm font-medium mb-1">Penyajian *</label>
                    <select name="serving_type_id" id="servingSelect" required class="w-full px-4 py-2 rounded-lg border focus:ring-orange-500 focus:border-orange-500">
                        <option value="">-- Pilih Penyajian --</option>
                    </select>
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

        <div class="flex justify-between items-center pt-4 border-t">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-orange-500"><span class="text-sm">Aktif</span></label>
            <div class="flex gap-3">
                <a href="{{ isset($backService) ? route('admin.catering.show', $backService->id) : url()->previous() }}" class="px-6 py-2.5 text-gray-700 bg-gray-100 font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600">Simpan Paket</button>
            </div>
        </div>
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
            
            const srvSelect = document.getElementById('servingSelect');
            
            // Reset
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
                    if (opt.type === 'serving_type') srvSelect.innerHTML += optElement;
                }
            });

            updateMultiselectUI('menu', 'menuHiddenSelect', 'menuChips', 'menuPlaceholder', 'menuDropdown');
            updateMultiselectUI('extra', 'extraHiddenSelect', 'extraChips', 'extraPlaceholder', 'extraDropdown');

            if (callback) callback();
        });
}

// Init load if service is pre-selected (via query param or old value)
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('serviceSelect').value) {
        loadServiceOptions();
    }
});
</script>
@endpush
@endsection
