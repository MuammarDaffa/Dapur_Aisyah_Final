@extends('layouts.app')
@section('title', 'Profil Saya')

@push('styles')
<style>
    .page-header {
        position: relative;
        padding: 80px 0 40px;
        background-color: var(--bg-cream);
        overflow: hidden;
    }
    
    .blob-header {
        position: absolute;
        top: -50px; right: -10%;
        width: 400px; height: 400px;
        background: var(--primary-terracotta);
        opacity: 0.05;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        animation: morph 15s ease-in-out infinite alternate;
        z-index: 0;
    }

    .bento-box {
        background: white;
        border-radius: var(--bento-radius);
        box-shadow: var(--soft-shadow);
        padding: 30px;
        position: relative;
        overflow: hidden;
        height: 100%;
        border: 2px solid rgba(0,0,0,0.03);
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        background: var(--primary-terracotta);
        color: white;
        font-size: 3.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        margin: 0 auto 20px;
        box-shadow: 0 10px 25px rgba(224, 93, 54, 0.2);
        animation: morph 8s ease-in-out infinite alternate;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="blob-header"></div>
    <div class="container position-relative z-1">
        <div class="text-center mb-4">
            <span class="d-inline-flex align-items-center gap-2 bg-white rounded-pill px-4 py-2 shadow-sm mb-3 border border-light">
                <span class="bg-primary-mc rounded-circle" style="width: 8px; height: 8px;"></span>
                <span class="fw-bold text-secondary small text-uppercase tracking-wider">Akun Pengguna</span>
            </span>
            <h1 class="display-4 fw-bold text-dark mb-2">
                Profil <span style="color: var(--primary-terracotta); font-style: italic;">Saya</span>
            </h1>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-4 pb-5 mt-n4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            
            <div class="bento-box text-center mb-4 border-2" style="border-color: rgba(224, 93, 54, 0.1) !important;">
                <div class="profile-avatar fw-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="fw-bold text-dark mb-2">{{ $user->name }}</h2>
                <div class="d-inline-flex align-items-center gap-2 bg-warning bg-opacity-10 text-dark rounded-pill px-4 py-2 border border-warning border-opacity-25">
                    <i class="fa-solid fa-star text-warning"></i>
                    <span class="fw-bold small text-uppercase tracking-wider">Pelanggan Setia</span>
                </div>
            </div>

            <div class="bento-box mb-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                    <div class="bg-primary-mc bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-user-pen text-primary-mc"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Informasi Akun</h4>
                        <span class="text-secondary small">Perbarui informasi profil dan detail kontak Anda.</span>
                    </div>
                </div>
                
                <form action="{{ route('pelanggan.profile.update') }}" method="POST" id="form-profile">
                    @csrf @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase tracking-wider">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-primary-mc px-4" style="border-radius: 12px 0 0 12px;">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control bg-light border-start-0 py-3 fw-semibold @error('name') is-invalid @enderror" style="border-radius: 0 12px 12px 0;">
                        </div>
                        @error('name') <div class="text-danger small mt-2 fw-semibold"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase tracking-wider">No. Telepon / WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-success px-4" style="border-radius: 12px 0 0 12px;">
                                <i class="fa-brands fa-whatsapp fs-5"></i>
                            </span>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="form-control bg-light border-start-0 py-3 fw-semibold @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx" style="border-radius: 0 12px 12px 0;">
                        </div>
                        @error('phone') <div class="text-danger small mt-2 fw-semibold"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div> @enderror
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold text-dark small text-uppercase tracking-wider">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-warning px-4" style="border-radius: 12px 0 0 12px;">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control bg-light border-start-0 py-3 fw-semibold @error('email') is-invalid @enderror" style="border-radius: 0 12px 12px 0;">
                        </div>
                        @error('email') <div class="text-danger small mt-2 fw-semibold"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn-primary-mc btn-lg rounded-pill shadow-lg d-flex justify-content-center align-items-center gap-2 w-100 fw-bold py-3">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </form>
            </div>

            <div class="bento-box bg-light border-0 text-center p-5 mb-5 border border-secondary border-opacity-10">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-4" style="width: 70px; height: 70px;">
                    <i class="fa-solid fa-headset fs-2 text-secondary"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Butuh Bantuan?</h4>
                <p class="text-secondary mb-4">Jika Anda mengalami kendala saat mengubah profil, silakan hubungi layanan pelanggan kami.</p>
                <a href="#" class="btn btn-dark rounded-pill px-5 py-2 fw-bold d-inline-flex align-items-center gap-2 hover-opacity">
                    <i class="fa-brands fa-whatsapp text-success fs-5"></i> Hubungi CS
                </a>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2C4A3B'
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc3545'
            });
        @endif
        
        // Ensure form submission is confirmed nicely (optional)
        document.getElementById('form-profile').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';
            btn.disabled = true;
        });
    });
</script>
@endpush
@endsection
