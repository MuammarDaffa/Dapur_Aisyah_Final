@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6"><span class="text-orange-500">Profil</span> Saya</h2>

    {{-- Suspend Warnings --}}
    @if($user->isSuspended())
        <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-xl">
            <p class="text-red-700 font-medium">⛔ Akun Anda Ditangguhkan</p>
            <p class="text-sm text-red-600 mt-1">Akun Anda ditangguhkan karena terindikasi data tidak valid. Anda tidak dapat melakukan pemesanan. Silakan perbarui nomor HP Anda di bawah, lalu tunggu verifikasi oleh Admin.</p>
        </div>
    @elseif($user->isPendingVerification())
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-300 rounded-xl">
            <p class="text-yellow-700 font-medium">⏳ Menunggu Verifikasi</p>
            <p class="text-sm text-yellow-600 mt-1">Nomor HP baru Anda sedang dalam proses verifikasi oleh Admin. Anda belum dapat melakukan pemesanan sampai Admin mengaktifkan kembali akun Anda.</p>
        </div>
    @endif

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
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full px-4 py-2 rounded-lg border {{ $user->isSuspended() ? 'border-red-400 ring-2 ring-red-100' : 'border-gray-200' }} focus:border-orange-400 @error('phone') border-red-400 @enderror" placeholder="08xxxxxxxxxx">
                    @if($user->isSuspended())
                        <p class="text-xs text-red-500 mt-1 font-medium">⚠️ Silakan perbarui nomor HP Anda untuk mengajukan verifikasi ulang.</p>
                    @endif
                    @if($user->old_phone)
                        <p class="text-xs text-gray-400 mt-1">HP sebelumnya: <span class="line-through">{{ $user->old_phone }}</span></p>
                    @endif
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
    </div>
</div>
@endsection
