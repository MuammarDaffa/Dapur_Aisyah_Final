@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- <h2 class="fs-3 fw-bold text-secondary mb-6 d-flex align-items-center">
        <!-- <svg class="w-7 h-7 text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        <span><span class="text-primary">Profil</span> Saya</span> -->
    </h2> -->


    <div class="space-y-6">
        <!-- Informasi Akun -->
        <div class="card shadow-sm mb-4 p-4">
            <h3 class="fw-bold text-secondary mb-1">Informasi Akun</h3>
            <p class="text-muted small mb-4">Perbarui informasi profil dan alamat email akun Anda.</p>
            <form action="{{ route('pelanggan.profile.update') }}" method="POST" class="d-flex flex-column gap-3">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control focus:border-primary @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="form-control focus:border-primary @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control focus:border-primary @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        <!-- Ubah Password -->
        <div class="card shadow-sm mb-4 p-4">
            <h3 class="fw-bold text-secondary mb-1">Perbarui Password</h3>
            <p class="text-muted small mb-4">Pastikan akun Anda menggunakan kata sandi acak yang panjang untuk tetap aman.</p>
            <form action="{{ route('password.update') }}" method="POST" class="d-flex flex-column gap-3">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Password Saat Ini</label>
                    <input type="password" name="current_password" required class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                    @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Password Baru</label>
                    <input type="password" name="password" required class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                    @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror">
                    @error('password_confirmation', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Simpan Password</button>
                    @if (session('status') === 'password-updated')
                        <span class="ms-3 text-success small fw-bold">Berhasil disimpan.</span>
                    @endif
                </div>
            </form>
        </div>

        <!-- Hapus Akun -->
        <div class="card shadow-sm mb-4 p-4">
            <h3 class="fw-bold text-secondary mb-1">Hapus Akun</h3>
            <p class="text-muted small mb-4">Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Harap masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.</p>
            <form action="{{ route('pelanggan.profile.destroy') }}" method="POST" class="d-flex flex-column gap-3">
                @csrf @method('DELETE')
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" required class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Password Saat Ini">
                    @error('password', 'userDeletion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun Anda secara permanen? Tindakan ini tidak dapat dibatalkan.')">Hapus Akun Secara Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
