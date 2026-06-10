@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">👤 <span class="text-orange-500">Profil</span> Saya</h2>
    <div class="space-y-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-4">Informasi Akun</h3>
            <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400 @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400 @error('phone') border-red-400 @enderror" placeholder="08xxxxxxxxxx">
                    @error('phone') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400 @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Simpan Perubahan</button>
            </form>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-4">Ubah Password</h3>
            <form action="{{ route('customer.profile.password') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400">
                    @error('current_password') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="new_password" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="new_password_confirmation" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-900 transition-colors">Ubah Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
