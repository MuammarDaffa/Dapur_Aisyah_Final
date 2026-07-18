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
                    <div class="w-20 h-20 rounded d-flex align-items-center justify-content-center text-primary">
                        <svg style="height: 40px;" class="w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    @endif
                    <div>
                        <h2 class="fs-4 fw-bold text-secondary">{{ $catering->name }}</h2>
                        <div class="d-flex align-items-center g-3 mt-1">
                            <span class="d-inline-d-flex align-items-center g-3 px-2.5 py-1 rounded-pill small fw-medium bg-info text-white text-info">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <span>Daily</span>
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
                <div class="d-flex align-items-center g-3">
                    <a href="{{ route('admin.catering.edit', $catering) }}" class="d-inline-d-flex align-items-center g-3.5 px-4 py-2 fs-6 fw-medium text-info bg-info text-white rounded hover:bg-info text-white">
                        <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <span>Edit Katering</span>
                    </a>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="row row-cols-2 g-3 mt-6 pt-4 border-t max-w-md">
                <div class="text-center p-3 bg-light rounded">
                    <p class="small text-secondary uppercase tracking-wider">Harga Mulai</p>
                    <p class="fs-5 fw-bold text-primary mt-1">Rp {{ number_format($catering->base_price, 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-3 bg-light rounded">
                    <p class="small text-secondary uppercase tracking-wider">Total Produk</p>
                    <p class="fs-5 fw-bold text-secondary mt-1">{{ $catering->products()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Menu Mingguan Section --}}
    <div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden" id="schedule_section">
        <div class="px-6 py-4 border-b bg-light">
            <h3 class="fw-bold text-secondary">Jadwal Menu Mingguan</h3>
            <!-- <p class="small text-secondary mt-0.5">Atur rentang tanggal dan menu harian yang akan ditampilkan kepada pelanggan</p> -->
        </div>

        {{-- Form Pengaturan Jadwal --}}
        <form action="{{ route('admin.menu-periods.store', $catering) }}" method="POST" id="schedule_form">
            @csrf
            <div class="p-6 bg-white border-b border border-secondary">
                <div class="d-flex d-flex-column md:d-flex-row items-end g-3">
                    <div class="d-flex-1 w-100">
                        <label class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" lang="id-ID" id="input_start_date" name="start_date" 
                            value="{{ old('start_date', $currentSchedule?->start_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                            class="w-100 px-3.5 py-2 rounded border border border-secondary fs-6 focus:border border-primary -2 bg-white" required>
                    </div>
                    <div class="d-flex-1 w-100">
                        <label class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" lang="id-ID" id="input_end_date" name="end_date" 
                            value="{{ old('end_date', $currentSchedule?->end_date?->format('Y-m-d') ?? now()->addDays(6)->format('Y-m-d')) }}"
                            class="w-100 px-3.5 py-2 rounded border border border-secondary fs-6 focus:border border-primary -2 bg-white" required>
                    </div>
                    <div class="w-100 md:w-auto">
                        <button type="button" id="btn_generate" 
                            class="btn btn-primary">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Buat Jadwal</span>
                        </button>
                    </div>
                </div>
                <!-- <p class="text-[11px] text-secondary mt-2">
                    Tekan tombol <strong class="text-secondary">Buat Jadwal</strong> untuk membentuk daftar tanggal di bawah, lalu pilih produk menu untuk setiap tanggal.
                </p> -->
            </div>

            {{-- Tabel Jadwal --}}
            <div class="overflow-x-auto">
                <table class="w-100 fs-6">
                    <thead class="bg-light/50 border-b border border-secondary">
                        <tr class="small uppercase text-secondary tracking-wider">
                            <th class="px-6 py-3 text-start fw-bold w-1/5">Tanggal</th>
                            <th class="px-6 py-3 text-start fw-bold w-1/5">Hari</th>
                            <th class="px-6 py-3 text-start fw-bold w-2/5">Produk Menu</th>
                            <th class="px-6 py-3 text-start fw-bold w-1/5">Status Produk</th>
                        </tr>
                    </thead>
                    <tbody id="schedule_tbody" class="divide-y divide-gray-100 bg-white">
                        {{-- Rows akan di-generate via JavaScript --}}
                    </tbody>
                </table>
            </div>

            {{-- Empty State jika belum generate --}}
            <div id="table_empty_state" class="py-12 text-center bg-white">
                <!-- <div style="width: 48px; height: 48px;" class="mx-auto mb-3 rounded-pill bg-primary text-white d-flex align-items-center justify-content-center text-primary">
                    <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div> -->
                <p class="fs-6 fw-bold text-secondary">Daftar Tanggal Belum Dibuat</p>
                <p class="small text-secondary mt-1 max-w-sm mx-auto">
                    Silakan tentukan Tanggal Mulai dan Tanggal Selesai di atas, kemudian klik tombol <strong class="text-secondary fw-medium">Buat Jadwal</strong>.
                </p>
            </div>

            {{-- Footer Simpan --}}
            <div id="table_footer" class="px-6 py-4 bg-light border-t border border-secondary d-flex align-items-center justify-content-between" style="display: none;">
                <span class="small text-secondary">
                    Total: <strong id="badge_count" class="text-secondary fw-bold">0 hari</strong>
                </span>
                <div>
                    <button type="submit" id="btn_save_schedule"
                        class="btn btn-primary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Jadwal</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Produk Section --}}
    <div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden">
        <div class="px-6 py-4 border-b bg-light d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-secondary">Daftar Produk</h3>
                <p class="small text-secondary mt-0.5">Produk menu harian untuk katering ini</p>
            </div>
            <a href="{{ route('admin.products.create') }}?catering_service_id={{ $catering->id }}"
               class="d-inline-d-flex align-items-center g-3 px-4 py-2 bg-primary text-white text-white small fw-medium rounded hover:bg-primary text-white shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Tambah Produk</span>
            </a>
        </div>
        <table class="w-100 fs-6">
            <thead class="bg-light/50">
                <tr class="small uppercase text-secondary tracking-wider">
                    <th class="px-6 py-3 text-start fw-bold">Produk</th>
                    <th class="px-6 py-3 text-center fw-bold">Harga</th>
                    <th class="px-6 py-3 text-center fw-bold">Status</th>
                    <th class="px-6 py-3 text-center fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $p)
                <tr class="hover:bg-primary text-white/30">
                    <td class="px-6 py-4">
                        <div class="d-flex align-items-center g-3">
                            @if($p->image)
                            <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="w-10 h-10 rounded object-cover">
                            @else
                            <div style="height: 40px;" class="w-10 rounded bg-light d-flex align-items-center justify-content-center text-secondary">
                                <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            @endif
                            <div>
                                <p class="fw-medium text-secondary">{{ $p->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center fw-bold text-primary">{{ $p->formatted_price }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-pill small fw-medium {{ $p->is_active ? 'bg-success text-white text-success' : 'bg-light text-secondary' }}">
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="d-flex align-items-center justify-content-center g-3">
                            <a href="{{ route('admin.products.edit', $p) }}" class="px-3 py-1.5 small fw-medium text-info bg-info text-white rounded hover:bg-info text-white">Edit</a>
                            <form id="form-delete-product-{{ $p->id }}" action="{{ route('admin.products.destroy', $p) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('form-delete-product-{{ $p->id }}', 'Hapus produk ini?')" class="px-3 py-1.5 small fw-medium text-danger bg-danger text-white rounded hover:bg-danger text-white">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-secondary">
                        <!-- <svg style="height: 40px;" class="w-10 mx-auto mb-2 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg> -->
                        <p class="fs-6">Belum ada produk untuk katering ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($products->hasPages())
        <div class="px-6 py-3 border-t">{{ $products->links() }}</div>
        @endif
    </div>

    {{-- Extra Section --}}
    <div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden">
        <div class="px-6 py-4 border-b bg-light d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-secondary">Daftar Extra</h3>
                <p class="small text-secondary mt-0.5">Extra tambahan yang dapat dipesan pelanggan</p>
            </div>
            <button type="button" onclick="openExtraModal()" class="btn btn-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Tambah Extra</span>
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
                            <button type="button" onclick="openEditExtraModal({{ $extra->id }}, '{{ $extra->name }}', {{ $extra->price }}, {{ $extra->is_active ? 'true' : 'false' }})" class="px-3 py-1.5 small fw-medium text-info bg-info text-white rounded hover:bg-info text-white">Edit</button>
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
                        <!-- <svg style="height: 40px;" class="w-10 mx-auto mb-2 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg> -->
                        <p class="fs-6">Belum ada extra. Contoh: Sambal Tambahan, Kerupuk, Air Mineral.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah Extra --}}
<div id="addExtraModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-md mx-4">
        <div class="d-flex align-items-center justify-content-between p-6 border-b border border-secondary">
            <h3 class="fs-5 fw-bold text-secondary">Tambah Extra</h3>
            <button onclick="document.getElementById('addExtraModal').style.display='none'" class="p-1 text-secondary hover:text-secondary rounded hover:bg-light">
                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('admin.catering.options.store', $catering) }}" method="POST" class="p-6 d-flex flex-column gap-3">
            @csrf
            <input type="hidden" name="type" value="extra">
            <div class="mb-3">
            <label class="form-label fw-bold">Nama *</label>
                <input type="text" name="name" required class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary" placeholder="cth: Sambal Tambahan">
            </div>
            <div class="mb-3">
            <label class="form-label fw-bold">Harga (Rp) *</label>
                <input type="text" name="price" value="0" class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary rupiah-input">
            </div>
            <div class="d-flex align-items-center g-3">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border border-secondary text-primary">
                <span class="fs-6 fw-medium text-secondary">Aktif</span>
            </div>
            <div class="d-flex g-3">
                <button type="button" onclick="document.getElementById('addExtraModal').style.display='none'" class="w-100 px-6 py-2.5 text-secondary bg-light fw-medium rounded hover:bg-light">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Extra --}}
<div id="editExtraModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-md mx-4">
        <div class="d-flex align-items-center justify-content-between p-6 border-b border border-secondary">
            <h3 class="fs-5 fw-bold text-secondary">Edit Extra</h3>
            <button onclick="document.getElementById('editExtraModal').style.display='none'" class="p-1 text-secondary hover:text-secondary rounded hover:bg-light">
                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="editExtraForm" method="POST" class="p-6 d-flex flex-column gap-3">
            @csrf @method('PUT')
            <div class="mb-3">
            <label class="form-label fw-bold">Nama *</label>
                <input type="text" name="name" id="editExtraName" required class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary">
            </div>
            <div class="mb-3">
            <label class="form-label fw-bold">Harga (Rp) *</label>
                <input type="text" name="price" id="editExtraPrice" value="0" class="form-control w-100 px-4 py-2.5 rounded border border border-secondary focus:border border-primary rupiah-input">
            </div>
            <div class="d-flex align-items-center g-3">
                <input type="checkbox" name="is_active" value="1" id="editExtraActive" class="rounded border border-secondary text-primary">
                <span class="fs-6 fw-medium text-secondary">Aktif</span>
            </div>
            <div class="d-flex g-3">
                <button type="button" onclick="document.getElementById('editExtraModal').style.display='none'" class="w-100 px-6 py-2.5 text-secondary bg-light fw-medium rounded hover:bg-light">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
@php
    $productsJson = $allProducts->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'price_label' => 'Rp ' . number_format($p->price, 0, ',', '.')
        ];
    })->values()->all();

    $scheduleItems = $currentSchedule && $currentSchedule->items ? $currentSchedule->items->map(function($i) {
        return [
            'menu_date' => $i->menu_date->format('Y-m-d'),
            'product_id' => $i->product_id,
            'status' => $i->status ?? 'tersedia',
        ];
    })->all() : [];

    $oldItemsJson = old('items', $scheduleItems);
@endphp
<script>
const cateringId = {{ $catering->id }};

function openExtraModal() {
    document.getElementById('addExtraModal').style.display = 'flex';
}

function openEditExtraModal(id, name, price, isActive) {
    document.getElementById('editExtraName').value = name;
    document.getElementById('editExtraPrice').value = formatRupiah(price);
    document.getElementById('editExtraActive').checked = isActive;
    document.getElementById('editExtraForm').action = `/admin/catering/${cateringId}/options/${id}`;
    document.getElementById('editExtraModal').style.display = 'flex';
}

document.getElementById('addExtraModal')?.addEventListener('click', function(e) { if (e.target === this) this.style.display='none'; });
document.getElementById('editExtraModal')?.addEventListener('click', function(e) { if (e.target === this) this.style.display='none'; });

// === Jadwal Menu Mingguan Script ===
document.addEventListener('DOMContentLoaded', function() {
    const products = {!! json_encode($productsJson) !!};
    const oldItems = {!! json_encode($oldItemsJson) !!};

    const inputStart = document.getElementById('input_start_date');
    const inputEnd = document.getElementById('input_end_date');
    const btnGenerate = document.getElementById('btn_generate');
    const tbody = document.getElementById('schedule_tbody');
    const emptyState = document.getElementById('table_empty_state');
    const tableFooter = document.getElementById('table_footer');
    const badgeCount = document.getElementById('badge_count');

    const indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function formatIndonesianDate(dateStr) {
        const parts = dateStr.split('-');
        if (parts.length !== 3) return { formatted: dateStr, dayName: '' };
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        const dateObj = new Date(year, month, day);
        const dayName = indonesianDays[dateObj.getDay()];
        const monthName = indonesianMonths[month];
        return {
            formatted: `${day} ${monthName} ${year}`,
            dayName: dayName
        };
    }

    function renderTable(datesList, existingMapping = {}) {
        if (!tbody || !emptyState || !tableFooter) return;
        tbody.innerHTML = '';

        if (datesList.length === 0) {
            emptyState.style.display = 'block';
            tableFooter.style.display = 'none';
            return;
        }

        emptyState.style.display = 'none';
        tableFooter.style.display = 'flex';
        if (badgeCount) badgeCount.textContent = `${datesList.length} hari`;

        datesList.forEach((dateStr, index) => {
            const dateInfo = formatIndonesianDate(dateStr);
            const mappingItem = existingMapping[dateStr] || {};
            const selectedProductId = typeof mappingItem === 'object' ? (mappingItem.product_id || '') : mappingItem;
            const selectedStatus = typeof mappingItem === 'object' ? (mappingItem.status || 'tersedia') : 'tersedia';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-orange-50/30 transition-colors';

            let optionsHtml = `<option value="">-- Pilih Menu untuk ${dateInfo.dayName} --</option>`;
            products.forEach(p => {
                const isSelected = String(p.id) === String(selectedProductId) ? 'selected' : '';
                optionsHtml += `<option value="${p.id}" ${isSelected}>${p.name} (${p.price_label})</option>`;
            });

            tr.innerHTML = `
                <td class="px-6 py-4 fw-medium text-secondary">
                    ${dateInfo.formatted}
                    <input type="hidden" name="items[${index}][menu_date]" value="${dateStr}">
                </td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-pill small fw-medium bg-primary text-white text-primary">
                        ${dateInfo.dayName}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <select name="items[${index}][product_id]" required
                        class="form-select w-100 px-3.5 py-2 rounded border border border-secondary fs-6 focus:border border-primary -2 bg-white">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="px-6 py-4">
                    <select name="items[${index}][status]" required
                        class="form-select w-100 px-3.5 py-2 rounded border border border-secondary fs-6 focus:border border-primary -2 bg-white fw-medium ${selectedStatus === 'habis' ? 'text-danger' : 'text-success'}">
                        <option value="tersedia" ${selectedStatus === 'tersedia' ? 'selected' : ''}>Tersedia</option>
                        <option value="habis" ${selectedStatus === 'habis' ? 'selected' : ''}>Habis</option>
                    </select>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function generateDates(startStr, endStr) {
        const dates = [];
        let curr = new Date(startStr);
        const end = new Date(endStr);
        
        while (curr <= end) {
            const y = curr.getFullYear();
            const m = String(curr.getMonth() + 1).padStart(2, '0');
            const d = String(curr.getDate()).padStart(2, '0');
            dates.push(`${y}-${m}-${d}`);
            curr.setDate(curr.getDate() + 1);
        }
        return dates;
    }

    if (btnGenerate) {
        btnGenerate.addEventListener('click', function() {
            if (!inputStart || !inputEnd) return;
            const startVal = inputStart.value;
            const endVal = inputEnd.value;

            if (!startVal || !endVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal Belum Lengkap',
                    text: 'Silakan isi Tanggal Mulai dan Tanggal Selesai terlebih dahulu.',
                    confirmButtonColor: '#f97316'
                });
                return;
            }

            if (new Date(startVal) > new Date(endVal)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Rentang Tanggal Tidak Valid',
                    text: 'Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.',
                    confirmButtonColor: '#f97316'
                });
                return;
            }

            const currentMapping = {};
            const existingSelects = tbody.querySelectorAll('select[name^="items"][name$="[product_id]"]');
            const existingStatusSelects = tbody.querySelectorAll('select[name^="items"][name$="[status]"]');
            const existingInputs = tbody.querySelectorAll('input[type="hidden"][name^="items"][name$="[menu_date]"]');
            
            existingInputs.forEach((inp, idx) => {
                if (existingSelects[idx]) {
                    currentMapping[inp.value] = {
                        product_id: existingSelects[idx].value,
                        status: existingStatusSelects[idx] ? existingStatusSelects[idx].value : 'tersedia'
                    };
                }
            });

            oldItems.forEach(item => {
                if (!currentMapping[item.menu_date]) {
                    currentMapping[item.menu_date] = {
                        product_id: item.product_id,
                        status: item.status || 'tersedia'
                    };
                }
            });

            const newDates = generateDates(startVal, endVal);
            renderTable(newDates, currentMapping);
        });
    }

    // Inisialisasi awal jika ada old items atau current schedule
    if (oldItems && oldItems.length > 0) {
        const initialMapping = {};
        const datesList = [];
        oldItems.forEach(item => {
            datesList.push(item.menu_date);
            initialMapping[item.menu_date] = {
                product_id: item.product_id,
                status: item.status || 'tersedia'
            };
        });
        renderTable(datesList, initialMapping);
    }
});
</script>
@endpush
@endsection
