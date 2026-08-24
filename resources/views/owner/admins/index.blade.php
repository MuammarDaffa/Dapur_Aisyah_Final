@extends('layouts.owner')
@section('title', 'Kelola Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold">Daftar Admin</h5>
                <a href="{{ route('owner.admins.create') }}" class="btn btn-primary btn-sm fw-bold px-3">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Admin Baru
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">No</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Tanggal Aktif   </th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>{{ $admin->phone ?? '-' }}</td>
                                    <td>{{ $admin->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        @if($admin->is_active)
                                            <span class="badge bg-success px-2 py-1">Aktif</span>
                                        @else
                                            <span class="badge bg-danger px-2 py-1">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Edit -->
                                            <a href="{{ route('owner.admins.edit', $admin->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Data">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            
                                            <!-- Reset Password (Modal) -->
                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetModal{{ $admin->id }}" title="Reset Password">
                                                <i class="fa-solid fa-key"></i>
                                            </button>

                                            <!-- Toggle Status -->
                                            <form action="{{ route('owner.admins.toggle_status', $admin->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-sm {{ $admin->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                                    onclick="confirmDeleteForm(this.form, 'Apakah Anda yakin ingin {{ $admin->is_active ? 'menonaktifkan' : 'mengaktifkan' }} admin ini?')" 
                                                    title="{{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fa-solid {{ $admin->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Reset Password Modal -->
                                        <div class="modal fade text-start" id="resetModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('owner.admins.update_password', $admin->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reset Password Admin</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-muted mb-3">Masukkan password baru untuk <strong>{{ $admin->name }}</strong>.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Password Baru</label>
                                                                <input type="password" class="form-control" name="password" required minlength="8">
                                                                <div class="form-text">Minimal 8 karakter.</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                                                                <input type="password" class="form-control" name="password_confirmation" required minlength="8">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-warning">Reset Password</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-users-slash fs-1 d-block mb-2"></i>
                                        Belum ada data admin terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
