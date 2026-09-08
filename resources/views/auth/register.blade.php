@extends('layouts.app')
@section('title', 'Daftar - Dapur Aisyah')
@section('content')
<div class="container-fluid p-0" style="min-height: calc(100vh - 70px);">
    <div class="row g-0 h-100">
        <!-- Kolom Kiri: Form Register -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center py-5" style="background-color: var(--bg-light); min-height: calc(100vh - 70px);">
            <div class="w-100 px-4 px-md-5" style="max-width: 500px;">
                <div class="text-center mb-5">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" width="80" class="mb-3">
                    <h2 class="fw-bold text-dark">Buat Akun Baru</h2>
                    <p class="text-secondary">Daftar sekarang untuk mulai memesan katering</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold text-secondary">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-mc"><i class="fa-solid fa-user"></i></span>
                            <input id="name" class="form-control form-control-mc with-icon @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama lengkap">
                        </div>
                        @error('name')
                            <div class="text-danger mt-1 fs-6">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-4">
                        <label for="phone" class="form-label fw-bold text-secondary">Nomor HP</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-mc"><i class="fa-solid fa-phone"></i></span>
                            <input id="phone" class="form-control form-control-mc with-icon @error('phone') is-invalid @enderror" type="text" name="phone" value="{{ old('phone') }}" required placeholder="0812xxxxxx">
                        </div>
                        @error('phone')
                            <div class="text-danger mt-1 fs-6">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold text-secondary">Email</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-mc"><i class="fa-solid fa-envelope"></i></span>
                            <input id="email" class="form-control form-control-mc with-icon @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <div class="text-danger mt-1 fs-6">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold text-secondary">Password</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-mc"><i class="fa-solid fa-lock"></i></span>
                            <input id="password" class="form-control form-control-mc with-icon @error('password') is-invalid @enderror" type="password" name="password" required placeholder="Minimal 8 karakter">
                        </div>
                        @error('password')
                            <div class="text-danger mt-1 fs-6">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-5">
                        <label for="password_confirmation" class="form-label fw-bold text-secondary">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-mc"><i class="fa-solid fa-lock"></i></span>
                            <input id="password_confirmation" class="form-control form-control-mc with-icon @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required placeholder="Ulangi password">
                        </div>
                        @error('password_confirmation')
                            <div class="text-danger mt-1 fs-6">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn-primary-mc btn-lg fs-5">
                             Daftar Sekarang
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-secondary">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-mc fw-bold text-decoration-none">Masuk di sini</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Gambar Makanan -->
        <div class="col-lg-6 d-none d-lg-block position-relative" style="min-height: calc(100vh - 70px);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('{{ asset('images/acara.jpg') }}'); background-size: cover; background-position: center;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>
            <div class="position-absolute top-50 start-50 translate-middle text-center w-100 px-5">
                <h1 class="display-4 fw-bold text-white mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Jadi Bagian dari Kami</h1>
                <p class="fs-4 text-white" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">Ratusan pelanggan telah membuktikan kelezatan masakan kami.</p>
            </div>
        </div>
    </div>
</div>
@endsection
