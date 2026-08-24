@extends('layouts.owner')
@section('title', 'Edit Admin')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        
        <!-- Card Informasi Admin -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-user-pen me-2 text-primary"></i> Informasi Admin</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.admins.update', $admin->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $admin->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $admin->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">No. Telepon / WhatsApp</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $admin->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Status Aktif</label>
                        </div>
                        <div class="form-text mt-1">Centang untuk mengaktifkan akun. Jika tidak dicentang, admin ini tidak akan bisa login.</div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('owner.admins.index') }}" class="btn btn-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card Reset Password -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-key me-2 text-warning"></i> Reset Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.admins.update_password', $admin->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter.</div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('owner.admins.index') }}" class="btn btn-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-warning px-4 fw-bold text-white">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>
@endsection
