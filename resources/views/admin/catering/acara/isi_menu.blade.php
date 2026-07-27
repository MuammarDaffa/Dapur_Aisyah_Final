@extends('layouts.admin')

@section('title', 'Kelola Isi Menu')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.catering.acara', $menu->layanan_id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Menu Acara
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-info">
            <div class="card-header d-flex align-items-center">
                <div>
                    <h3 class="card-title mb-0">Isi Menu</h3>
                    <p class="text-muted small mb-0">Subjudul: {{ $menu->nama_menu }}</p>
                </div>
                <div class="ms-auto">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahIsi">
                        Tambah Isi Menu
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Isi Menu</th>
                                <th>Harga</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($isiMenus as $index => $isi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $isi->nama }}</td>
                                <td>Rp{{ number_format($isi->harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditIsi{{ $isi->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.isi-menu.destroy', $isi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus isi menu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit Isi Menu -->
                            <div class="modal fade" id="modalEditIsi{{ $isi->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.isi-menu.update', $isi->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Isi Menu</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Isi Menu</label>
                                                    <input type="text" name="nama" class="form-control" value="{{ $isi->nama }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Harga</label>
                                                    <input type="number" name="harga" class="form-control" value="{{ $isi->harga }}" required min="0">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada isi menu.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Isi Menu -->
<div class="modal fade" id="modalTambahIsi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.isi-menu.store', $menu->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Isi Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Isi Menu</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Isi Menu</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
