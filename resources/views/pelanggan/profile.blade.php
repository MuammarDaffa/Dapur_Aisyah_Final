@extends('layouts.app')
@section('title', 'Profil Saya')

@push('styles')
<style>
    /* Yummy Clean Section Title */
    .section-title {
        text-align: center;
        padding-bottom: 30px;
    }
    .section-title h2 {
        font-size: 13px;
        letter-spacing: 1px;
        font-weight: 400;
        margin: 0;
        padding: 0;
        color: #7f7f90;
        text-transform: uppercase;
        font-family: "Inter", sans-serif;
    }
    .section-title p {
        margin: 0;
        font-size: 48px;
        font-weight: 700;
        font-family: "Amatic SC", sans-serif;
        color: #37373f;
    }
    .section-title p span {
        color: #ce1212;
    }

    /* Minimalist Card */
    .yummy-card {
        background: #fff;
        border: none;
        border-radius: 8px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
        padding: 40px;
    }

    /* Yummy Button */
    .btn-yummy {
        background: #ce1212;
        color: #fff;
        border: 2px solid #ce1212;
        border-radius: 50px;
        padding: 10px 30px;
        font-family: "Inter", sans-serif;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-yummy:hover {
        background: transparent;
        color: #ce1212;
    }

    /* Minimalist Inputs */
    .form-control {
        border-radius: 0;
        box-shadow: none;
        font-size: 14px;
        border: 1px solid #ced4da;
    }
    .form-control:focus {
        border-color: #ce1212;
        box-shadow: none;
    }
    .input-group-text {
        background-color: transparent;
        border-radius: 0;
        border-right: none;
    }
    .form-control {
        border-left: none;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        background: #ce1212;
        color: white;
        font-size: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 20px;
        font-family: "Amatic SC", sans-serif;
    }
    
    body {
        background-color: #f2f2f2;
</style>
@endpush

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    
    <div class="section-title">
        <h2>Informasi Anda</h2>
        <p>Profil <span>Saya</span></p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            
            <div class="yummy-card mb-4 text-center">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h3 class="fw-bold mb-1" style="color: #37373f;">{{ $user->name }}</h3>
                <span class="badge rounded-pill bg-light text-dark border"><i class="fa-solid fa-star text-warning"></i> Pelanggan Setia</span>
            </div>

            <div class="yummy-card mb-4">
                <h5 class="fw-bold mb-4" style="color: #37373f; border-bottom: 2px solid #f2f2f2; padding-bottom: 15px;">Informasi Akun</h5>
                
                <form action="{{ route('pelanggan.profile.update') }}" method="POST" id="form-profile">
                    @csrf @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control @error('name') is-invalid @enderror">
                        </div>
                        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small text-uppercase">No. Telepon / WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted">
                                <i class="fa-brands fa-whatsapp fs-5"></i>
                            </span>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="form-control @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx">
                        </div>
                        @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-muted small text-uppercase">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control @error('email') is-invalid @enderror">
                        </div>
                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn-yummy w-100">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-5 text-muted">
                <p class="mb-2">Butuh Bantuan? Hubungi layanan pelanggan kami.</p>
                <a href="#" class="text-decoration-none" style="color: #ce1212;"><i class="fa-brands fa-whatsapp"></i> Hubungi CS</a>
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
                confirmButtonColor: '#ce1212'
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
        
        document.getElementById('form-profile').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';
            btn.disabled = true;
        });
    });
</script>
@endpush
@endsection
