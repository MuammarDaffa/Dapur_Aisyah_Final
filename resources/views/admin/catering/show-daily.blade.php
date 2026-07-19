@extends('layouts.admin')
@section('title', 'Detail Katering: ' . $catering->name)
@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.catering.index') }}" class="btn btn-default"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Katering</a>
    </div>
</div>

<div class="row">
    <!-- Header Card -->
    <div class="col-12">
        <div class="card card-outline card-primary mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8 d-flex align-items-center">
                        @if($catering->image)
                        <img src="{{ asset('storage/' . $catering->image) }}" alt="{{ $catering->name }}" class="img-thumbnail me-3" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                        <div class="bg-light d-flex align-items-center justify-content-center border rounded me-3" style="width: 100px; height: 100px;">
                            <i class="fa-solid fa-utensils fs-1 text-primary"></i>
                        </div>
                        @endif
                        <div>
                            <h2 class="fs-4 fw-bold mb-1">{{ $catering->name }}</h2>
                            <div class="mb-2">
                                <span class="badge text-bg-info"><i class="fa-solid fa-calendar-day"></i> Daily</span>
                                <span class="badge {{ $catering->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $catering->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            @if($catering->description)
                            <p class="text-muted mb-0">{{ $catering->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('admin.catering.edit', $catering) }}" class="btn btn-info text-white"><i class="fa-solid fa-edit"></i> Edit Katering</a>
                    </div>
                </div>
                
                <hr>
                <div class="row text-center">
                    <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
                        <div class="p-3 bg-light rounded">
                            <span class="d-block small text-muted text-uppercase">Harga Mulai</span>
                            <strong class="fs-5 text-primary">Rp {{ number_format($catering->base_price, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 bg-light rounded">
                            <span class="d-block small text-muted text-uppercase">Total Produk</span>
                            <strong class="fs-5 text-dark">{{ $catering->products()->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Jadwal Menu Mingguan -->
    <div class="col-12" id="schedule_section">
        <div class="card card-outline card-info mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold">Jadwal Menu Mingguan</h3>
            </div>
            <form action="{{ route('admin.menu-periods.store', $catering) }}" method="POST" id="schedule_form">
                @csrf
                <div class="card-body border-bottom">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" id="input_start_date" name="start_date" 
                                value="{{ old('start_date', $currentSchedule?->start_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" id="input_end_date" name="end_date" 
                                value="{{ old('end_date', $currentSchedule?->end_date?->format('Y-m-d') ?? now()->addDays(6)->format('Y-m-d')) }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="btn_generate" class="btn btn-primary w-100 w-md-auto"><i class="fa-solid fa-calendar-plus"></i> Buat Jadwal</button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Tanggal</th>
                                    <th style="width: 20%;">Hari</th>
                                    <th style="width: 40%;">Produk Menu</th>
                                    <th style="width: 20%;">Status Produk</th>
                                </tr>
                            </thead>
                            <tbody id="schedule_tbody">
                                <!-- Rows generated by JS -->
                            </tbody>
                        </table>
                    </div>
                    <div id="table_empty_state" class="text-center py-5">
                        <h5 class="text-muted fw-bold">Daftar Tanggal Belum Dibuat</h5>
                        <p class="text-muted">Silakan tentukan Tanggal Mulai dan Tanggal Selesai di atas, kemudian klik tombol <strong>Buat Jadwal</strong>.</p>
                    </div>
                </div>

                <div id="table_footer" class="card-footer d-flex justify-content-between align-items-center" style="display: none !important;">
                    <span class="text-muted">Total: <strong id="badge_count" class="text-dark">0 hari</strong></span>
                    <button type="submit" id="btn_save_schedule" class="btn btn-success"><i class="fa-solid fa-save"></i> Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daftar Produk -->
    <div class="col-md-12">
        <div class="card card-outline card-success mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Daftar Produk</h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.products.create') }}?catering_service_id={{ $catering->id }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Tambah Produk</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $p)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        @if($p->image)
                                        <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-utensils text-muted"></i>
                                        </div>
                                        @endif
                                        <span class="fw-medium">{{ $p->name }}</span>
                                    </div>
                                </td>
                                <td class="align-middle text-center fw-bold text-success">{{ $p->formatted_price }}</td>
                                <td class="align-middle text-center">
                                    <span class="badge {{ $p->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-edit"></i> Edit</a>
                                        <form id="form-delete-product-{{ $p->id }}" action="{{ route('admin.products.destroy', $p) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('form-delete-product-{{ $p->id }}', 'Hapus produk ini?')" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada produk untuk katering ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($products->hasPages())
            <div class="card-footer">{{ $products->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- Daftar Extra -->
    <div class="col-md-12">
        <div class="card card-outline card-warning">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Daftar Extra</h3>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExtraModal">
                        <i class="fa-solid fa-plus"></i> Tambah Extra
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($extras as $extra)
                            <tr>
                                <td class="align-middle fw-medium">{{ $extra->name }}</td>
                                <td class="align-middle text-center fw-bold text-success">Rp {{ number_format($extra->price, 0, ',', '.') }}</td>
                                <td class="align-middle text-center">
                                    <span class="badge {{ $extra->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $extra->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <button type="button" onclick="openEditExtraModal({{ $extra->id }}, '{{ $extra->name }}', {{ $extra->price }}, {{ $extra->is_active ? 'true' : 'false' }})" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-edit"></i> Edit</button>
                                        <form id="form-delete-extra-{{ $extra->id }}" action="{{ route('admin.catering.options.destroy', [$catering, $extra]) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('form-delete-extra-{{ $extra->id }}', 'Hapus extra ini?')" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada extra.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Extra -->
<div class="modal fade" id="addExtraModal" tabindex="-1" aria-labelledby="addExtraModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="addExtraModalLabel">Tambah Extra</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.catering.options.store', $catering) }}" method="POST">
          <div class="modal-body">
              @csrf
              <input type="hidden" name="type" value="extra">
              <div class="mb-3">
                  <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                  <input type="text" name="name" required class="form-control" placeholder="cth: Sambal Tambahan">
              </div>
              <div class="mb-3">
                  <label class="form-label fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                  <input type="text" name="price" value="0" class="form-control rupiah-input">
              </div>
              <div class="form-check">
                  <input type="checkbox" name="is_active" value="1" checked class="form-check-input" id="checkActiveAdd">
                  <label class="form-check-label fw-bold" for="checkActiveAdd">Aktif</label>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Extra -->
<div class="modal fade" id="editExtraModal" tabindex="-1" aria-labelledby="editExtraModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="editExtraModalLabel">Edit Extra</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editExtraForm" method="POST">
          <div class="modal-body">
              @csrf @method('PUT')
              <div class="mb-3">
                  <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="editExtraName" required class="form-control">
              </div>
              <div class="mb-3">
                  <label class="form-label fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                  <input type="text" name="price" id="editExtraPrice" value="0" class="form-control rupiah-input">
              </div>
              <div class="form-check">
                  <input type="checkbox" name="is_active" value="1" id="editExtraActive" class="form-check-input">
                  <label class="form-check-label fw-bold" for="editExtraActive">Aktif</label>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update</button>
          </div>
      </form>
    </div>
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

let editExtraModal;
document.addEventListener('DOMContentLoaded', function() {
    editExtraModal = new bootstrap.Modal(document.getElementById('editExtraModal'));
});

function openEditExtraModal(id, name, price, isActive) {
    document.getElementById('editExtraName').value = name;
    document.getElementById('editExtraPrice').value = formatRupiah(price);
    document.getElementById('editExtraActive').checked = isActive;
    document.getElementById('editExtraForm').action = `/admin/catering/${cateringId}/options/${id}`;
    if(editExtraModal) editExtraModal.show();
}

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
            tableFooter.style.setProperty('display', 'none', 'important');
            return;
        }

        emptyState.style.display = 'none';
        tableFooter.style.setProperty('display', 'flex', 'important');
        if (badgeCount) badgeCount.textContent = `${datesList.length} hari`;

        datesList.forEach((dateStr, index) => {
            const dateInfo = formatIndonesianDate(dateStr);
            const mappingItem = existingMapping[dateStr] || {};
            const selectedProductId = typeof mappingItem === 'object' ? (mappingItem.product_id || '') : mappingItem;
            const selectedStatus = typeof mappingItem === 'object' ? (mappingItem.status || 'tersedia') : 'tersedia';

            const tr = document.createElement('tr');

            let optionsHtml = `<option value="">-- Pilih Menu untuk ${dateInfo.dayName} --</option>`;
            products.forEach(p => {
                const isSelected = String(p.id) === String(selectedProductId) ? 'selected' : '';
                optionsHtml += `<option value="${p.id}" ${isSelected}>${p.name} (${p.price_label})</option>`;
            });

            tr.innerHTML = `
                <td class="align-middle fw-medium">
                    ${dateInfo.formatted}
                    <input type="hidden" name="items[${index}][menu_date]" value="${dateStr}">
                </td>
                <td class="align-middle">
                    <span class="badge text-bg-primary">
                        ${dateInfo.dayName}
                    </span>
                </td>
                <td class="align-middle">
                    <select name="items[${index}][product_id]" required class="form-select">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="align-middle">
                    <select name="items[${index}][status]" required class="form-select fw-medium ${selectedStatus === 'habis' ? 'text-danger' : 'text-success'}">
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
                    text: 'Silakan isi Tanggal Mulai dan Tanggal Selesai terlebih dahulu.'
                });
                return;
            }

            if (new Date(startVal) > new Date(endVal)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Rentang Tanggal Tidak Valid',
                    text: 'Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.'
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
