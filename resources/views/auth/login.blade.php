@extends('layouts.app')
@section('title', 'Masuk - Dapur Aisyah')
@section('content')
<div class="container-fluid p-0" style="min-height: calc(100vh - 70px);">
    <div class="row g-0 h-100">
        <!-- Kolom Kiri: Gambar Makanan -->
        <div class="col-lg-6 d-none d-lg-block position-relative" style="min-height: calc(100vh - 70px);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('{{ asset('images/harian.png') }}'); background-size: cover; background-position: center;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>
            <div class="position-absolute top-50 start-50 translate-middle text-center w-100 px-5">
                <h1 class="display-4 fw-bold text-white mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Selamat Datang</h1>
            
            </div>
        </div>

        <!-- Kolom Kanan: Form Login -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center py-5" style="background-color: var(--bg-light); min-height: calc(100vh - 70px);">
            <div class="w-100 px-4 px-md-5" style="max-width: 500px;">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-dark">Masuk ke Akun</h2>
                    <p class="text-secondary">Masukkan email dan password Anda</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-4 rounded-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold text-secondary">Email</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-mc"><i class="fa-solid fa-envelope"></i></span>
                            <input id="email" class="form-control form-control-mc with-icon @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
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
                            <input id="password" class="form-control form-control-mc with-icon @error('password') is-invalid @enderror" type="password" name="password" required placeholder="********">
                        </div>
                        @error('password')
                            <div class="text-danger mt-1 fs-6">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                            <label class="form-check-label text-secondary" for="remember_me">
                                Ingat Saya
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="text-primary-mc text-decoration-none fw-semibold" href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn-primary-mc btn-lg fs-5">
                             Masuk Sekarang
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-secondary">Belum punya akun? <a href="{{ route('register') }}" class="text-primary-mc fw-bold text-decoration-none">Daftar di sini</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
