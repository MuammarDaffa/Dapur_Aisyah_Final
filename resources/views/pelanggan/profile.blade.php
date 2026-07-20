@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="fs-3 fw-bold text-secondary mb-6 d-flex align-items-center">
        <svg class="w-7 h-7 text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        <span><span class="text-primary">Profil</span> Saya</span>
    </h2>


    <div class="space-y-6">
        <div class="card shadow-sm mb-4 p-4">
            <h3 class="fw-bold text-secondary mb-4">Informasi Akun</h3>
            <form action="{{ route('pelanggan.profile.update') }}" method="POST" class="d-flex flex-column gap-3">
                @csrf @method('PUT')
                <div class="mb-3">
            <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-100 px-4 py-2 rounded border border border-secondary focus:border border-primary @error('name') border-red-400 @enderror">
                    @error('name') <p class="fs-6 text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-3">
            <label class="form-label fw-bold">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-100 px-4 py-2 rounded border border border-secondary focus:border border-primary @error('phone') border-red-400 @enderror" placeholder="08xxxxxxxxxx">
                    @error('phone') <p class="fs-6 text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-3">
            <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-100 px-4 py-2 rounded border border border-secondary focus:border border-primary @error('email') border-red-400 @enderror">
                    @error('email') <p class="fs-6 text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
