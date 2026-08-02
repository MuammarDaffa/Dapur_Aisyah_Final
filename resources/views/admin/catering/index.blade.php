@extends('layouts.admin')

@section('title', 'Kelola Katering')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Daftar Katering</h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.catering.create') }}" class="btn btn-primary btn-sm">
                        <!-- <i class="fa-solid fa-plus"></i> -->
                         Tambah Katering
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama Katering</th>
                                <th class="text-center">Tipe</th>
                                
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($caterings as $c)
                            <tr>
                                <td class="align-middle">
                                    <span class="fw-bold">{{ $c->nama }}</span>
                                    
                                </td>
                                <td class="align-middle text-center">
                                    @if($c->isHarian())
                                        <span >Harian</span>
                                    @elseif($c->isAcara())
                                        <span>Acara</span>
                                    @else
                                        <span class="badge text-bg-secondary">-</span>
                                    @endif
                                </td>
                                
                                <td class="align-middle text-center">
                                    <span class="badge {{ $c->status ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $c->status ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <!-- kolom aksi -->
                                <td class="align-middle text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        {{-- Tombol untuk melihat detail/manajemen katering --}}
                                        {{-- Jika tipe harian, arahkan ke manajemen harian. Jika acara, tetap ke show --}}
                                        @if($c->isHarian())
                                            <a href="{{ route('admin.catering.harian', $c->id) }}" class="btn btn-sm btn-info text-white" title="Manajemen Harian">
                                                <!-- <i class="bi bi-eye"></i> -->
                                                Lihat
                                            </a>
                                        @else
                                            <a href="{{ route('admin.catering.acara', $c->id) }}" class="btn btn-sm btn-info text-white" title="Detail Acara">
                                                <!-- <i class="bi bi-eye"></i> -->
                                                 Lihat
                                            </a>
                                        @endif

                                        {{-- Tombol Edit Modal --}}
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $c->id }}" title="Edit">
                                            Edit
                                        </button>

                                        <form action="{{ route('admin.catering.destroy', $c) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Yakin ingin menghapus layanan ini?');">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <!-- <i class="bi bi-trash"></i> -->
                                                 Hapus
                                            </button>
                                        </form>

                                    </div>
                                </td>


                            </tr>

                            {{-- Modal Edit Katering --}}
                            <div class="modal fade" id="editModal{{ $c->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $c->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.catering.update', $c->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $c->id }}">Edit Katering</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                {{-- Nama --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Nama Katering <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama" required value="{{ $c->nama }}" class="form-control">
                                                </div>

                                                {{-- Tipe Katering (Read Only / Disabled via radio) --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold d-block">Tipe Katering</label>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" value="harian" {{ $c->isHarian() ? 'checked' : '' }} disabled>
                                                        <label class="form-check-label">Harian</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" value="acara" {{ $c->isAcara() ? 'checked' : '' }} disabled>
                                                        <label class="form-check-label">Acara</label>
                                                    </div>
                                                </div>

                                                {{-- Status --}}
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="checkbox" name="status" value="1" {{ $c->status ? 'checked' : '' }} id="statusCheck{{ $c->id }}">
                                                    <label class="form-check-label fw-bold" for="statusCheck{{ $c->id }}">
                                                        Aktif
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fa-solid fa-utensils fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted fw-medium mb-1">Belum ada katering.</p>
                                    <small class="text-muted">Buat layanan katering pertama Anda.</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($caterings->hasPages())
            <div class="card-footer">
                {{ $caterings->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
