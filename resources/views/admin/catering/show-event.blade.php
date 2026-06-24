@extends('layouts.admin')
@section('title', 'Detail Katering: ' . $catering->name)
@section('content')
<div class="space-y-6">
    {{-- Back Link --}}
    <a href="{{ route('admin.catering.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-orange-500 transition-colors">
        ← Kembali ke Daftar Katering
    </a>

    {{-- Header Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div class="flex items-start gap-4">
                    @if($catering->image)
                    <img src="{{ asset('storage/' . $catering->image) }}" alt="{{ $catering->name }}" class="w-20 h-20 rounded-xl object-cover border">
                    @else
                    <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center text-3xl">🎉</div>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $catering->name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">🎉 Event</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $catering->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $catering->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        @if($catering->description)
                        <p class="text-sm text-gray-600 mt-2 max-w-lg">{{ $catering->description }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.catering.edit', $catering) }}" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    ✏️ Edit Katering
                </a>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-4 border-t">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Harga Mulai</p>
                    <p class="text-lg font-bold text-orange-600 mt-1">Rp {{ number_format($catering->base_price, 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Min. Porsi</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $catering->min_portion }}</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Max. Porsi</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $catering->max_portion ?? '∞' }}</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Paket</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $catering->packages()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- Task 13: Section Menu --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">🍽️ Menu</h3>
                <p class="text-xs text-gray-500 mt-0.5">Menu yang tersedia untuk paket katering ini</p>
            </div>
            <button type="button" onclick="openOptionModal('menu')" class="px-4 py-2 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                + Tambah Menu
            </button>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Nama</th>
                    <th class="px-6 py-3 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($menus as $menu)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $menu->name }}</td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $menu->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $menu->is_active ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" data-items="{{ json_encode($menu->items ?? []) }}" onclick="openEditModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, {{ $menu->is_active ? 'true' : 'false' }}, 'menu', this)" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</button>
                            <form id="form-delete-menu-{{ $menu->id }}" action="{{ route('admin.catering.options.destroy', [$catering, $menu]) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('form-delete-menu-{{ $menu->id }}', 'Hapus menu ini?')" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                        <p class="text-2xl mb-1">🍽️</p>
                        <p class="text-sm">Belum ada menu. Tambahkan menu untuk digunakan dalam paket.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- Task 14: Section Penyajian --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">🍲 Penyajian</h3>
                <p class="text-xs text-gray-500 mt-0.5">Tipe penyajian yang tersedia (Lunch Box, Prasmanan, dll)</p>
            </div>
            <button type="button" onclick="openOptionModal('serving_type')" class="px-4 py-2 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                + Tambah Penyajian
            </button>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Nama</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($servings as $serving)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $serving->name }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $serving->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $serving->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" data-items="{{ json_encode($serving->items ?? []) }}" onclick="openEditModal({{ $serving->id }}, '{{ addslashes($serving->name) }}', {{ $serving->price }}, {{ $serving->is_active ? 'true' : 'false' }}, 'serving_type', this)" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</button>
                            <form id="form-delete-serving-{{ $serving->id }}" action="{{ route('admin.catering.options.destroy', [$catering, $serving]) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('form-delete-serving-{{ $serving->id }}', 'Hapus penyajian ini?')" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                        <p class="text-2xl mb-1">🍲</p>
                        <p class="text-sm">Belum ada penyajian. Contoh: Lunch Box, Prasmanan, Plated Service.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- Task 15: Section Extra --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">✨ Extra</h3>
                <p class="text-xs text-gray-500 mt-0.5">Extra tambahan (Sambal, Kerupuk, Air Mineral, dll)</p>
            </div>
            <button type="button" onclick="openOptionModal('extra')" class="px-4 py-2 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                + Tambah Extra
            </button>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Nama</th>
                    <th class="px-6 py-3 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($extras as $extra)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $extra->name }}</td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">Rp {{ number_format($extra->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $extra->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $extra->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" data-items="{{ json_encode($extra->items ?? []) }}" onclick="openEditModal({{ $extra->id }}, '{{ addslashes($extra->name) }}', {{ $extra->price }}, {{ $extra->is_active ? 'true' : 'false' }}, 'extra', this)" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</button>
                            <form id="form-delete-extra-{{ $extra->id }}" action="{{ route('admin.catering.options.destroy', [$catering, $extra]) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('form-delete-extra-{{ $extra->id }}', 'Hapus extra ini?')" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                        <p class="text-2xl mb-1">✨</p>
                        <p class="text-sm">Belum ada extra. Contoh: Sambal Tambahan, Kerupuk, Air Mineral.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- Paket Section --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">📋 Paket</h3>
                <p class="text-xs text-gray-500 mt-0.5">Paket catering untuk layanan event ini</p>
            </div>
            <a href="{{ route('admin.packages.create') }}?catering_service_id={{ $catering->id }}"
               class="px-4 py-2 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                + Tambah Paket
            </a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Nama Paket</th>
                    <th class="px-6 py-3 text-center font-semibold">Porsi</th>
                    <th class="px-6 py-3 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3 text-center font-semibold">Isi Paket</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($packages as $pkg)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $pkg->name }}</p>
                        @if($pkg->description)
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $pkg->description }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-medium">{{ $pkg->total_portions }}</span>
                        <span class="text-xs text-gray-400">porsi</span>
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">
                        Rp {{ number_format($pkg->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($pkg->customOptions->count() > 0)
                        <div class="flex flex-wrap gap-1 justify-center">
                            @foreach($pkg->customOptions->take(3) as $opt)
                            <span class="px-1.5 py-0.5 text-[10px] rounded bg-gray-100 text-gray-600">{{ $opt->name }}</span>
                            @endforeach
                            @if($pkg->customOptions->count() > 3)
                            <span class="px-1.5 py-0.5 text-[10px] rounded bg-gray-200 text-gray-500">+{{ $pkg->customOptions->count() - 3 }}</span>
                            @endif
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $pkg->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.packages.edit', $pkg) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                            <form id="form-delete-package-{{ $pkg->id }}" action="{{ route('admin.packages.destroy', $pkg) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('form-delete-package-{{ $pkg->id }}', 'Hapus paket ini?')" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        <p class="text-2xl mb-1">📋</p>
                        <p class="text-sm">Belum ada paket untuk katering ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($packages->hasPages())
        <div class="px-6 py-3 border-t">{{ $packages->links() }}</div>
        @endif
    </div>
</div>

{{-- Modal Tambah Option --}}
<div id="addOptionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 id="addModalTitle" class="text-lg font-bold text-gray-900">Tambah Item</h3>
            <button onclick="closeOptionModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="addOptionForm" action="{{ route('admin.catering.options.store', $catering) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="type" id="addOptionType">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500" placeholder="Nama item...">
            </div>
            <div id="addItemsField" style="display:none;">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Item Menu *</label>
                    <button type="button" onclick="addAddMenuItem()" class="text-xs text-orange-600 font-semibold hover:text-orange-700">+ Tambah Item</button>
                </div>
                <div id="addItemsContainer" class="space-y-2">
                    <!-- Dynamic item inputs will go here -->
                </div>
            </div>
            <div id="addImageField" style="display:none;">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                <input type="file" name="image" accept="image/*" class="w-full px-4 py-2.5 rounded-lg border border-gray-200">
            </div>
            <div id="addPriceField">
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                <input type="text" name="price" id="addPrice" value="0" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500 rupiah-input">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addOptionModal').style.display='none'" class="w-full px-6 py-2.5 text-gray-700 bg-gray-100 font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                <button type="submit" class="w-full px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Option --}}
<div id="editOptionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Edit Item</h3>
            <button onclick="closeEditModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="editOptionForm" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" id="editOptionType">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="name" id="editName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div id="editItemsField" style="display:none;">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Item Menu *</label>
                    <button type="button" onclick="addEditMenuItem()" class="text-xs text-orange-600 font-semibold hover:text-orange-700">+ Tambah Item</button>
                </div>
                <div id="editItemsContainer" class="space-y-2">
                    <!-- Dynamic item inputs will go here -->
                </div>
            </div>
            <div id="editImageField" style="display:none;">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (Kosongkan jika tidak diubah)</label>
                <input type="file" name="image" accept="image/*" class="w-full px-4 py-2.5 rounded-lg border border-gray-200">
            </div>
            <div id="editPriceField">
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                <input type="text" name="price" id="editPrice" value="0" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500 rupiah-input">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="editActive" class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('editOptionModal').style.display='none'" class="w-full px-6 py-2.5 text-gray-700 bg-gray-100 font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                <button type="submit" class="w-full px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const cateringId = {{ $catering->id }};
const typeLabels = { menu: 'Menu', serving_type: 'Penyajian', extra: 'Extra' };

function openOptionModal(type) {
    document.getElementById('addOptionType').value = type;
    document.getElementById('addModalTitle').textContent = 'Tambah ' + typeLabels[type];
    
    // Tampilkan field items & image hanya untuk menu
    if (type === 'menu') {
        document.getElementById('addItemsField').style.display = 'block';
        document.getElementById('addImageField').style.display = 'block';
        
        // Reset items
        const container = document.getElementById('addItemsContainer');
        container.innerHTML = '';
        addAddMenuItem(); // add one empty input by default
    } else {
        document.getElementById('addItemsField').style.display = 'none';
        document.getElementById('addImageField').style.display = 'none';
        document.getElementById('addItemsContainer').innerHTML = '';
    }
    
    // Penyajian: harga tidak diperlukan (0)
    if (type === 'serving_type') {
        document.getElementById('addPriceField').style.display = 'none';
        document.getElementById('addPrice').value = '0';
    } else {
        document.getElementById('addPriceField').style.display = 'block';
        document.getElementById('addPrice').value = '0';
    }
    
    document.getElementById('addOptionModal').style.display = 'flex';
}

function closeOptionModal() {
    document.getElementById('addOptionModal').style.display = 'none';
}

function openEditModal(optionId, name, price, isActive, type, btnElement) {
    document.getElementById('editName').value = name;
    
    // Tampilkan field items & image hanya untuk menu
    if (type === 'menu') {
        document.getElementById('editItemsField').style.display = 'block';
        document.getElementById('editImageField').style.display = 'block';
        
        const container = document.getElementById('editItemsContainer');
        container.innerHTML = '';
        
        // Parse items dari btnElement
        let items = [];
        if(btnElement && btnElement.getAttribute('data-items')) {
            try {
                items = JSON.parse(btnElement.getAttribute('data-items'));
            } catch(e) {}
        }
        
        if (items && items.length > 0) {
            items.forEach(item => addEditMenuItem(item));
        } else {
            addEditMenuItem(); // minimal 1 item
        }
    } else {
        document.getElementById('editItemsField').style.display = 'none';
        document.getElementById('editImageField').style.display = 'none';
        document.getElementById('editItemsContainer').innerHTML = '';
    }
    
    // Penyajian: harga tidak diperlukan (0)
    if (type === 'serving_type') {
        document.getElementById('editPriceField').style.display = 'none';
        document.getElementById('editPrice').value = '0';
    } else {
        document.getElementById('editPriceField').style.display = 'block';
        document.getElementById('editPrice').value = formatRupiah(price);
    }
    
    document.getElementById('editActive').checked = isActive;
    document.getElementById('editOptionForm').action = `/admin/catering/${cateringId}/options/${optionId}`;
    document.getElementById('editOptionModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editOptionModal').style.display = 'none';
}

// Close modals when clicking outside
document.getElementById('addOptionModal')?.addEventListener('click', function(e) { if (e.target === this) closeOptionModal(); });
document.getElementById('editOptionModal')?.addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });

// Dynamic items UI logic
function addAddMenuItem(value = '') {
    const container = document.getElementById('addItemsContainer');
    container.insertAdjacentHTML('beforeend', `
        <div class="flex items-center gap-2">
            <input type="text" name="items[]" value="${value}" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500" placeholder="Misal: Nasi Putih">
            <button type="button" onclick="this.parentElement.remove()" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    `);
}

function addEditMenuItem(value = '') {
    const container = document.getElementById('editItemsContainer');
    container.insertAdjacentHTML('beforeend', `
        <div class="flex items-center gap-2">
            <input type="text" name="items[]" value="${value}" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500" placeholder="Misal: Nasi Putih">
            <button type="button" onclick="this.parentElement.remove()" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    `);
}
</script>
@endpush
@endsection
