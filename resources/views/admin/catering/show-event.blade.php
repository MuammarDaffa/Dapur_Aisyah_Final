@extends('layouts.admin')
@section('title', 'Detail Katering: ' . $catering->name)
@section('content')
<div class="space-y-6">
    {{-- Back Link --}}
    <a href="{{ route('admin.catering.index') }}" class="d-inline-d-flex align-items-center fs-6 text-secondary hover:text-primary">
        ← Kembali ke Daftar Katering
    </a>

    {{-- Header Card --}}
    <div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden">
        <div class="p-6">
            <div class="d-flex d-flex-column sm:d-flex-row justify-content-between items-start g-3">
                <div class="d-flex items-start g-3">
                    @if($catering->image)
                    <img src="{{ asset('storage/' . $catering->image) }}" alt="{{ $catering->name }}" class="w-20 h-20 rounded object-cover border">
                    @else
                    <div class="w-20 h-20 rounded d-flex align-items-center justify-content-center text-purple-600">
                        <svg style="height: 40px;" class="w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    @endif
                    <div>
                        <h2 class="fs-4 fw-bold text-secondary">{{ $catering->name }}</h2>
                        <div class="d-flex align-items-center g-3 mt-1">
                            <span class="d-inline-d-flex align-items-center g-3 px-2.5 py-1 rounded-pill small fw-medium bg-purple-100 text-purple-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                <span>Event</span>
                            </span>
                            <span class="px-2.5 py-1 rounded-pill small fw-medium {{ $catering->is_active ? 'bg-success text-white text-success' : 'bg-light text-secondary' }}">
                                {{ $catering->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        @if($catering->description)
                        <p class="fs-6 text-secondary mt-2 max-w-lg">{{ $catering->description }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.catering.edit', $catering) }}" class="d-inline-d-flex align-items-center g-3.5 px-4 py-2 fs-6 fw-medium text-info bg-info text-white rounded hover:bg-info text-white">
                    <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    <span>Edit Katering</span>
                </a>
            </div>

            {{-- Info Grid --}}
            <div class="row row-cols-2 sm:row-cols-4 g-3 mt-6 pt-4 border-t">
                <div class="text-center p-3 bg-light rounded">
                    <p class="small text-secondary uppercase tracking-wider">Harga Mulai</p>
                    <p class="fs-5 fw-bold text-primary mt-1">Rp {{ number_format($catering->base_price, 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-3 bg-light rounded">
                    <p class="small text-secondary uppercase tracking-wider">Min. Porsi</p>
                    <p class="fs-5 fw-bold text-secondary mt-1">{{ $catering->min_portion }}</p>
                </div>
                <div class="text-center p-3 bg-light rounded">
                    <p class="small text-secondary uppercase tracking-wider">Max. Porsi</p>
                    <p class="fs-5 fw-bold text-secondary mt-1">{{ $catering->max_portion ?? '∞' }}</p>
                </div>
                <div class="text-center p-3 bg-light rounded">
                    <p class="small text-secondary uppercase tracking-wider">Total Paket</p>
                    <p class="fs-5 fw-bold text-secondary mt-1">{{ $catering->packages()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- Task 13: Section Menu --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden">
        <div class="px-6 py-4 border-b bg-light d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-secondary d-flex align-items-center g-3">
                    <svg style="width: 20px; height: 20px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Menu</span>
                </h3>
                <p class="small text-secondary mt-0.5">Menu yang tersedia untuk paket katering ini</p>
            </div>
            <button type="button" onclick="openOptionModal('menu')" class="btn btn-primary">
                + Tambah Menu
            </button>
        </div>
        <table class="w-100 fs-6">
            <thead class="bg-light/50">
                <tr class="small uppercase text-secondary tracking-wider">
                    <th class="px-6 py-3 text-start fw-bold">Nama</th>
                    <th class="px-6 py-3 text-center fw-bold">Harga</th>
                    <th class="px-6 py-3 text-center fw-bold">Status</th>
                    <th class="px-6 py-3 text-center fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($menus as $menu)
                <tr class="hover:bg-primary text-white/30">
                    <td class="px-6 py-4 fw-medium text-secondary">{{ $menu->name }}</td>
                    <td class="px-6 py-4 text-center fw-bold text-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-pill small fw-medium {{ $menu->is_active ? 'bg-success text-white text-success' : 'bg-light text-secondary' }}">
                            {{ $menu->is_active ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="d-flex align-items-center justify-content-center g-3">
                            <button type="button" data-items="{{ json_encode($menu->items ?? []) }}" onclick="openEditModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, {{ $menu->is_active ? 'true' : 'false' }}, 'menu', this)" class="px-3 py-1.5 small fw-medium text-info bg-info text-white rounded hover:bg-info text-white">Edit</button>
                            <form id="form-delete-menu-{{ $menu->id }}" action="{{ route('admin.catering.options.destroy', [$catering, $menu]) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('form-delete-menu-{{ $menu->id }}', 'Hapus menu ini?')" class="px-3 py-1.5 small fw-medium text-danger bg-danger text-white rounded hover:bg-danger text-white">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-secondary">
                        <div style="width: 48px; height: 48px;" class="mx-auto mb-2 bg-light rounded-pill d-flex align-items-center justify-content-center">
                            <svg style="width: 24px; height: 24px;" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <p class="fs-6">Belum ada menu. Tambahkan menu untuk digunakan dalam paket.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    {{-- ============================================ --}}
    {{-- Task 15: Section Extra --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden">
        <div class="px-6 py-4 border-b bg-light d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-secondary d-flex align-items-center g-3">
                    <svg style="width: 20px; height: 20px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    <span>Extra</span>
                </h3>
                <p class="small text-secondary mt-0.5">Extra tambahan (Sambal, Kerupuk, Air Mineral, dll)</p>
            </div>
            <button type="button" onclick="openOptionModal('extra')" class="btn btn-primary">
                + Tambah Extra
            </button>
        </div>
        <table class="w-100 fs-6">
            <thead class="bg-light/50">
                <tr class="small uppercase text-secondary tracking-wider">
                    <th class="px-6 py-3 text-start fw-bold">Nama</th>
                    <th class="px-6 py-3 text-center fw-bold">Harga</th>
                    <th class="px-6 py-3 text-center fw-bold">Status</th>
                    <th class="px-6 py-3 text-center fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($extras as $extra)
                <tr class="hover:bg-primary text-white/30">
                    <td class="px-6 py-4 fw-medium text-secondary">{{ $extra->name }}</td>
                    <td class="px-6 py-4 text-center fw-bold text-primary">Rp {{ number_format($extra->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-pill small fw-medium {{ $extra->is_active ? 'bg-success text-white text-success' : 'bg-light text-secondary' }}">
                            {{ $extra->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="d-flex align-items-center justify-content-center g-3">
                            <button type="button" data-items="{{ json_encode($extra->items ?? []) }}" onclick="openEditModal({{ $extra->id }}, '{{ addslashes($extra->name) }}', {{ $extra->price }}, {{ $extra->is_active ? 'true' : 'false' }}, 'extra', this)" class="px-3 py-1.5 small fw-medium text-info bg-info text-white rounded hover:bg-info text-white">Edit</button>
                            <form id="form-delete-extra-{{ $extra->id }}" action="{{ route('admin.catering.options.destroy', [$catering, $extra]) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('form-delete-extra-{{ $extra->id }}', 'Hapus extra ini?')" class="px-3 py-1.5 small fw-medium text-danger bg-danger text-white rounded hover:bg-danger text-white">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-secondary">
                        <div style="width: 48px; height: 48px;" class="mx-auto mb-2 bg-light rounded-pill d-flex align-items-center justify-content-center">
                            <svg style="width: 24px; height: 24px;" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        <p class="fs-6">Belum ada extra. Contoh: Sambal Tambahan, Kerupuk, Air Mineral.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- Paket Section --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden">
        <div class="px-6 py-4 border-b bg-light d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-secondary">Daftar Paket</h3>
                <p class="small text-secondary mt-0.5">Paket catering tetap (fixed package) untuk layanan event ini</p>
            </div>
            <a href="{{ route('admin.packages.create') }}?catering_service_id={{ $catering->id }}"
               class="px-4 py-2 bg-primary text-white text-white small fw-medium rounded hover:bg-primary text-white shadow-sm">
                + Tambah Paket
            </a>
        </div>
        <table class="w-100 fs-6">
            <thead class="bg-light/50">
                <tr class="small uppercase text-secondary tracking-wider">
                    <th class="px-6 py-3 text-start fw-bold">Nama Paket</th>
                    <th class="px-6 py-3 text-center fw-bold">Porsi</th>
                    <th class="px-6 py-3 text-center fw-bold">Harga</th>
                    <th class="px-6 py-3 text-center fw-bold">Isi Menu & Penyajian</th>
                    <th class="px-6 py-3 text-center fw-bold">Status</th>
                    <th class="px-6 py-3 text-center fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($packages as $pkg)
                <tr class="hover:bg-primary text-white/30">
                    <td class="px-6 py-4">
                        <div class="d-flex align-items-center g-3">
                            @if($pkg->image)
                            <img src="{{ Storage::url($pkg->image) }}" alt="{{ $pkg->name }}" class="w-12 h-12 rounded object-cover border d-flex-shrink-0">
                            @endif
                            <div>
                                <p class="fw-bold text-secondary">{{ $pkg->name }}</p>
                                @if($pkg->description)
                                <p class="small text-secondary mt-0.5 line-clamp-1">{{ $pkg->description }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="fw-medium">{{ $pkg->total_portions }}</span>
                        <span class="small text-secondary">porsi</span>
                    </td>
                    <td class="px-6 py-4 text-center fw-bold text-primary">
                        Rp {{ number_format($pkg->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $menus = $pkg->getIncludedMenus();
                            $serving = $pkg->getIncludedServingTypes()->first();
                        @endphp
                        @if($menus->count() > 0 || $serving)
                        <div class="d-flex d-flex-wrap g-3 justify-content-center align-items-center">
                            @if($serving)
                            <span class="px-2 py-0.5 text-[10px] rounded fw-bold bg-info text-white text-info">{{ $serving->name }}</span>
                            @endif
                            @foreach($menus->take(3) as $opt)
                            <span class="px-1.5 py-0.5 text-[10px] rounded bg-light text-secondary">{{ $opt->name }}</span>
                            @endforeach
                            @if($menus->count() > 3)
                            <span class="px-1.5 py-0.5 text-[10px] rounded bg-light text-secondary">+{{ $menus->count() - 3 }} menu</span>
                            @endif
                        </div>
                        @else
                        <span class="small text-secondary">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-pill small fw-medium {{ $pkg->is_active ? 'bg-success text-white text-success' : 'bg-light text-secondary' }}">
                            {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="d-flex align-items-center justify-content-center g-3">
                            <a href="{{ route('admin.packages.edit', $pkg) }}" class="px-3 py-1.5 small fw-medium text-info bg-info text-white rounded hover:bg-info text-white">Edit</a>
                            <form id="form-delete-package-{{ $pkg->id }}" action="{{ route('admin.packages.destroy', $pkg) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('form-delete-package-{{ $pkg->id }}', 'Hapus paket ini?')" class="px-3 py-1.5 small fw-medium text-danger bg-danger text-white rounded hover:bg-danger text-white">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-secondary">
                        <p class="fs-6">Belum ada paket untuk katering ini.</p>
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
<div id="addOptionModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-md mx-4">
        <div class="d-flex align-items-center justify-content-between p-6 border-b border border-secondary">
            <h3 id="addModalTitle" class="fs-5 fw-bold text-secondary">Tambah Item</h3>
            <button onclick="closeOptionModal()" class="p-1 text-secondary hover:text-secondary rounded hover:bg-light">
                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="addOptionForm" action="{{ route('admin.catering.options.store', $catering) }}" method="POST" enctype="multipart/form-data" class="p-6 d-flex flex-column gap-3">
            @csrf
            <input type="hidden" name="type" id="addOptionType">
            <div class="mb-3">
            <label class="form-label fw-bold">Nama *</label>
                <input type="text" name="name" required class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary" placeholder="Nama item...">
            </div>
            <div id="addItemsField" style="display:none;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="form-label fw-bold">Item Menu *</label>
                    <button type="button" onclick="addAddMenuItem()" class="small text-primary fw-bold hover:text-primary">+ Tambah Item</button>
                </div>
                <div id="addItemsContainer" class="d-flex flex-column gap-2">
                    <!-- Dynamic item inputs will go here -->
                </div>
            </div>
            <div id="addImageField" style="display:none;">
                <label class="form-label fw-bold">Gambar</label>
                <input type="file" name="image" accept="image/*" class="w-100 px-4 py-2.5 rounded border border border-secondary">
            </div>
            <div id="addPriceField">
                <label class="form-label fw-bold">Harga (Rp) *</label>
                <input type="text" name="price" id="addPrice" value="0" class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary rupiah-input">
            </div>
            <div class="d-flex align-items-center g-3">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border border-secondary text-primary">
                <span class="fs-6 fw-medium text-secondary">Aktif</span>
            </div>
            <div class="d-flex g-3">
                <button type="button" onclick="document.getElementById('addOptionModal').style.display='none'" class="w-100 px-6 py-2.5 text-secondary bg-light fw-medium rounded hover:bg-light">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Option --}}
<div id="editOptionModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-md mx-4">
        <div class="d-flex align-items-center justify-content-between p-6 border-b border border-secondary">
            <h3 class="fs-5 fw-bold text-secondary">Edit Item</h3>
            <button onclick="closeEditModal()" class="p-1 text-secondary hover:text-secondary rounded hover:bg-light">
                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="editOptionForm" action="" method="POST" enctype="multipart/form-data" class="p-6 d-flex flex-column gap-3">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" id="editOptionType">
            <div class="mb-3">
            <label class="form-label fw-bold">Nama *</label>
                <input type="text" name="name" id="editName" required class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
            </div>
            <div id="editItemsField" style="display:none;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="form-label fw-bold">Item Menu *</label>
                    <button type="button" onclick="addEditMenuItem()" class="small text-primary fw-bold hover:text-primary">+ Tambah Item</button>
                </div>
                <div id="editItemsContainer" class="d-flex flex-column gap-2">
                    <!-- Dynamic item inputs will go here -->
                </div>
            </div>
            <div id="editImageField" style="display:none;">
                <label class="form-label fw-bold">Gambar (Kosongkan jika tidak diubah)</label>
                <input type="file" name="image" accept="image/*" class="w-100 px-4 py-2.5 rounded border border border-secondary">
            </div>
            <div id="editPriceField">
                <label class="form-label fw-bold">Harga (Rp) *</label>
                <input type="text" name="price" id="editPrice" value="0" class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary rupiah-input">
            </div>
            <div class="d-flex align-items-center g-3">
                <input type="checkbox" name="is_active" value="1" id="editActive" class="rounded border border-secondary text-primary">
                <span class="fs-6 fw-medium text-secondary">Aktif</span>
            </div>
            <div class="d-flex g-3">
                <button type="button" onclick="document.getElementById('editOptionModal').style.display='none'" class="w-100 px-6 py-2.5 text-secondary bg-light fw-medium rounded hover:bg-light">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const cateringId = {{ $catering->id }};
const typeLabels = { menu: 'Menu', extra: 'Extra' };

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
    
    document.getElementById('addPriceField').style.display = 'block';
    document.getElementById('addPrice').value = '0';
    
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
    
    document.getElementById('editPriceField').style.display = 'block';
    document.getElementById('editPrice').value = formatRupiah(price);
    
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
        <div class="d-flex align-items-center g-3">
            <input type="text" name="items[]" value="${value}" required class="form-control w-100 px-3 py-2 fs-6 rounded border border border-secondary focus:border border-primary" placeholder="Misal: Nasi Putih">
            <button type="button" onclick="this.parentElement.remove()" class="btn btn-outline-danger btn btn-danger p-2 text-danger hover:bg-danger text-white rounded">
                <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    `);
}

function addEditMenuItem(value = '') {
    const container = document.getElementById('editItemsContainer');
    container.insertAdjacentHTML('beforeend', `
        <div class="d-flex align-items-center g-3">
            <input type="text" name="items[]" value="${value}" required class="form-control w-100 px-3 py-2 fs-6 rounded border border border-secondary focus:border border-primary" placeholder="Misal: Nasi Putih">
            <button type="button" onclick="this.parentElement.remove()" class="btn btn-outline-danger btn btn-danger p-2 text-danger hover:bg-danger text-white rounded">
                <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    `);
}
</script>
@endpush
@endsection
