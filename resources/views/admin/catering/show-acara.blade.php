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
                            <i class="fa-solid fa-glass-cheers fs-1 text-purple"></i>
                        </div>
                        @endif
                        <div>
                            <h2 class="fs-4 fw-bold mb-1">{{ $catering->name }}</h2>
                            <div class="mb-2">
                                <span class="badge text-bg-primary" style="background-color: #6f42c1 !important;"><i class="fa-solid fa-glass-cheers"></i> Acara</span>
                                <span class="badge {{ $catering->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $catering->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            @if($catering->deskripsi)
                            <p class="text-muted mb-0">{{ $catering->deskripsi }}</p>
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
                    <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
                        <div class="p-3 bg-light rounded">
                            <span class="d-block small text-muted text-uppercase">Min. Porsi</span>
                            <strong class="fs-5 text-dark">{{ $catering->min_portion }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
                        <div class="p-3 bg-light rounded">
                            <span class="d-block small text-muted text-uppercase">Max. Porsi</span>
                            <strong class="fs-5 text-dark">{{ $catering->maksimal_porsi ?? '∞' }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 bg-light rounded">
                            <span class="d-block small text-muted text-uppercase">Total Paket</span>
                            <strong class="fs-5 text-dark">{{ $catering->packages()->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Menu Section -->
    <div class="col-md-12">
        <div class="card card-outline card-success mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold"><i class="fa-solid fa-utensils text-success"></i> Menu</h3>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openOptionModal('menu')">
                        <i class="fa-solid fa-plus"></i> Tambah Menu
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
                            @forelse($menus as $menu)
                            <tr>
                                <td class="align-middle fw-medium">{{ $menu->name }}</td>
                                <td class="align-middle text-center fw-bold text-success">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td class="align-middle text-center">
                                    <span class="badge {{ $menu->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $menu->is_active ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <button type="button" data-items="{{ json_encode($menu->items ?? []) }}" onclick="openEditModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->harga }}, {{ $menu->is_active ? 'true' : 'false' }}, 'menu', this)" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-edit"></i> Edit</button>
                                        <form id="form-delete-menu-{{ $menu->id }}" action="{{ route('admin.catering.options.destroy', [$catering, $menu]) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('form-delete-menu-{{ $menu->id }}', 'Hapus menu ini?')" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada menu. Tambahkan menu untuk digunakan dalam paket.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Extra Section -->
    <div class="col-md-12">
        <div class="card card-outline card-warning mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold"><i class="fa-solid fa-plus-circle text-warning"></i> Extra</h3>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openOptionModal('extra')">
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
                                <td class="align-middle text-center fw-bold text-success">Rp {{ number_format($extra->harga, 0, ',', '.') }}</td>
                                <td class="align-middle text-center">
                                    <span class="badge {{ $extra->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $extra->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <button type="button" data-items="{{ json_encode($extra->items ?? []) }}" onclick="openEditModal({{ $extra->id }}, '{{ addslashes($extra->name) }}', {{ $extra->harga }}, {{ $extra->is_active ? 'true' : 'false' }}, 'extra', this)" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-edit"></i> Edit</button>
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

<div class="row">
    <!-- Paket Section -->
    <div class="col-md-12">
        <div class="card card-outline card-primary mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold"><i class="fa-solid fa-box text-primary"></i> Daftar Paket</h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.paket_katering.create') }}?layanan_katering_id={{ $catering->id }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-plus"></i> Tambah Paket
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama Paket</th>
                                <th class="text-center">Porsi</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Isi Menu & Penyajian</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pakets as $pkg)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        @if($pkg->image)
                                        <img src="{{ Storage::url($pkg->image) }}" alt="{{ $pkg->name }}" class="rounded border me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <span class="fw-bold d-block">{{ $pkg->name }}</span>
                                            @if($pkg->deskripsi)
                                            <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">{{ $pkg->deskripsi }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="fw-bold">{{ $pkg->total_portions }}</span> <small class="text-muted">porsi</small>
                                </td>
                                <td class="align-middle text-center fw-bold text-success">
                                    Rp {{ number_format($pkg->harga, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-center">
                                    @php
                                        $menus = $pkg->getIncludedMenus();
                                        $serving = $pkg->getIncludedServingTypes()->first();
                                    @endphp
                                    @if($menus->count() > 0 || $serving)
                                    <div>
                                        @if($serving)
                                        <span class="badge text-bg-info mb-1">{{ $serving->name }}</span>
                                        @endif
                                        @foreach($menus->take(3) as $opt)
                                        <span class="badge text-bg-light border text-dark mb-1">{{ $opt->name }}</span>
                                        @endforeach
                                        @if($menus->count() > 3)
                                        <span class="badge text-bg-secondary mb-1">+{{ $menus->count() - 3 }} menu</span>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge {{ $pkg->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.paket_katering.edit', $pkg) }}" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-edit"></i> Edit</a>
                                        <form id="form-delete-package-{{ $pkg->id }}" action="{{ route('admin.paket_katering.destroy', $pkg) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('form-delete-package-{{ $pkg->id }}', 'Hapus paket ini?')" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada paket untuk katering ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pakets->hasPages())
            <div class="card-footer">{{ $pakets->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Option (Menu/Extra) -->
<div class="modal fade" id="addOptionModal" tabindex="-1" aria-labelledby="addOptionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="addModalTitle">Tambah Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addOptionForm" action="{{ route('admin.catering.options.store', $catering) }}" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
              @csrf
              <input type="hidden" name="type" id="addOptionType">
              <div class="mb-3">
                  <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                  <input type="text" name="name" required class="form-control" placeholder="Nama item...">
              </div>
              <div id="addItemsField" style="display:none;" class="mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label fw-bold mb-0">Item Menu <span class="text-danger">*</span></label>
                      <button type="button" onclick="addAddMenuItem()" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-plus"></i> Tambah Item</button>
                  </div>
                  <div id="addItemsContainer" class="d-flex flex-column gap-2"></div>
              </div>
              <div id="addImageField" style="display:none;" class="mb-3">
                  <label class="form-label fw-bold">Gambar</label>
                  <input type="file" name="image" accept="image/*" class="form-control">
              </div>
              <div id="addPriceField" class="mb-3">
                  <label class="form-label fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                  <input type="text" name="harga" id="addPrice" value="0" class="form-control rupiah-input">
              </div>
              <div class="form-check">
                  <input type="checkbox" name="is_active" value="1" checked class="form-check-input" id="checkActiveAddOpt">
                  <label class="form-check-label fw-bold" for="checkActiveAddOpt">Aktif</label>
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

<!-- Modal Edit Option (Menu/Extra) -->
<div class="modal fade" id="editOptionModal" tabindex="-1" aria-labelledby="editOptionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="editOptionModalLabel">Edit Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editOptionForm" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
              @csrf @method('PUT')
              <input type="hidden" name="type" id="editOptionType">
              <div class="mb-3">
                  <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="editName" required class="form-control">
              </div>
              <div id="editItemsField" style="display:none;" class="mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label fw-bold mb-0">Item Menu <span class="text-danger">*</span></label>
                      <button type="button" onclick="addEditMenuItem()" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-plus"></i> Tambah Item</button>
                  </div>
                  <div id="editItemsContainer" class="d-flex flex-column gap-2"></div>
              </div>
              <div id="editImageField" style="display:none;" class="mb-3">
                  <label class="form-label fw-bold">Gambar (Kosongkan jika tidak diubah)</label>
                  <input type="file" name="image" accept="image/*" class="form-control">
              </div>
              <div id="editPriceField" class="mb-3">
                  <label class="form-label fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                  <input type="text" name="harga" id="editPrice" value="0" class="form-control rupiah-input">
              </div>
              <div class="form-check">
                  <input type="checkbox" name="is_active" value="1" id="editActive" class="form-check-input">
                  <label class="form-check-label fw-bold" for="editActive">Aktif</label>
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
<script>
const cateringId = {{ $catering->id }};
const typeLabels = { menu: 'Menu', extra: 'Extra' };

let addModal, editModal;
document.addEventListener('DOMContentLoaded', function() {
    addModal = new bootstrap.Modal(document.getElementById('addOptionModal'));
    editModal = new bootstrap.Modal(document.getElementById('editOptionModal'));
});

function openOptionModal(type) {
    document.getElementById('addOptionType').value = type;
    document.getElementById('addModalTitle').textContent = 'Tambah ' + typeLabels[type];
    
    if (type === 'menu') {
        document.getElementById('addItemsField').style.display = 'block';
        document.getElementById('addImageField').style.display = 'block';
        const container = document.getElementById('addItemsContainer');
        container.innerHTML = '';
        addAddMenuItem(); 
    } else {
        document.getElementById('addItemsField').style.display = 'none';
        document.getElementById('addImageField').style.display = 'none';
        document.getElementById('addItemsContainer').innerHTML = '';
    }
    
    document.getElementById('addPriceField').style.display = 'block';
    document.getElementById('addPrice').value = '0';
    if(addModal) addModal.show();
}

function openEditModal(optionId, name, harga, isActive, type, btnElement) {
    document.getElementById('editName').value = name;
    
    if (type === 'menu') {
        document.getElementById('editItemsField').style.display = 'block';
        document.getElementById('editImageField').style.display = 'block';
        
        const container = document.getElementById('editItemsContainer');
        container.innerHTML = '';
        
        let items = [];
        if(btnElement && btnElement.getAttribute('data-items')) {
            try { items = JSON.parse(btnElement.getAttribute('data-items')); } catch(e) {}
        }
        
        if (items && items.length > 0) {
            items.forEach(item => addEditMenuItem(item));
        } else {
            addEditMenuItem();
        }
    } else {
        document.getElementById('editItemsField').style.display = 'none';
        document.getElementById('editImageField').style.display = 'none';
        document.getElementById('editItemsContainer').innerHTML = '';
    }
    
    document.getElementById('editPriceField').style.display = 'block';
    document.getElementById('editPrice').value = formatRupiah(harga);
    
    document.getElementById('editActive').checked = isActive;
    document.getElementById('editOptionForm').action = `/admin/catering/${cateringId}/options/${optionId}`;
    if(editModal) editModal.show();
}

function addAddMenuItem(value = '') {
    const container = document.getElementById('addItemsContainer');
    container.insertAdjacentHTML('beforeend', `
        <div class="input-group mb-2">
            <input type="text" name="items[]" value="${value}" required class="form-control" placeholder="Misal: Nasi Putih">
            <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger"><i class="fa-solid fa-times"></i></button>
        </div>
    `);
}

function addEditMenuItem(value = '') {
    const container = document.getElementById('editItemsContainer');
    container.insertAdjacentHTML('beforeend', `
        <div class="input-group mb-2">
            <input type="text" name="items[]" value="${value}" required class="form-control" placeholder="Misal: Nasi Putih">
            <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger"><i class="fa-solid fa-times"></i></button>
        </div>
    `);
}
</script>
@endpush
@endsection
