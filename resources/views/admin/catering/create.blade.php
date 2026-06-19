@extends('layouts.admin')
@section('title', 'Tambah Katering')
@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.catering.index') }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors">← Kembali ke Daftar Katering</a>
    </div>

    <form action="{{ route('admin.catering.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-5">
        @csrf

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Katering *</label>
            <input type="text" name="name" required value="{{ old('name') }}"
                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500"
                   placeholder="cth: Katering Harian">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
            <textarea name="description" rows="3" required
                      class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500"
                      placeholder="Deskripsi singkat katering...">{{ old('description') }}</textarea>
        </div>

        {{-- Tipe Katering --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Katering *</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" name="catering_type" value="daily" {{ old('catering_type', 'daily') === 'daily' ? 'checked' : '' }}
                           class="peer sr-only">
                    <div class="p-4 border-2 rounded-xl transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📦</span>
                            <div>
                                <p class="font-semibold text-gray-800">Daily</p>
                                <p class="text-xs text-gray-500">Menu harian dengan produk</p>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="catering_type" value="event" {{ old('catering_type') === 'event' ? 'checked' : '' }}
                           class="peer sr-only">
                    <div class="p-4 border-2 rounded-xl transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-gray-300">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🎉</span>
                            <div>
                                <p class="font-semibold text-gray-800">Event</p>
                                <p class="text-xs text-gray-500">Acara dengan paket catering</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            @error('catering_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Harga & Porsi --}}
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Dasar (Rp) *</label>
                <input type="text" name="base_price" required value="{{ old('base_price') }}" 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all rupiah-input">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Min. Porsi *</label>
                <input type="number" name="min_portion" required value="{{ old('min_portion', 1) }}" min="1"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max. Porsi</label>
                <input type="number" name="max_portion" value="{{ old('max_portion') }}" placeholder="Tidak dibatasi"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
        </div>

        {{-- Ketentuan & Jadwal --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ketentuan Pemesanan</label>
                <textarea name="order_terms" rows="2"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500"
                          placeholder="Ketentuan khusus...">{{ old('order_terms') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Jadwal</label>
                <textarea name="schedule_notes" rows="2"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500"
                          placeholder="Info jadwal...">{{ old('schedule_notes') }}</textarea>
            </div>
        </div>

        {{-- Pengaturan Cutoff --}}
        <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-4 space-y-4">
            <div>
                <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2">⏰ Pengaturan Cutoff Pemesanan</h4>
                <p class="text-xs text-gray-500 mt-0.5">Batas waktu minimal pemesanan untuk layanan ini</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimal Hari Pemesanan</label>
                    <input type="number" name="minimal_order_days" value="{{ old('minimal_order_days') }}" min="0" placeholder="cth: 3"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-xs text-gray-400 mt-1">Jumlah hari minimal sebelum tanggal acara/pengiriman</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Jam Pemesanan</label>
                    <input type="time" name="cutoff_time" value="{{ old('cutoff_time') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika hanya validasi hari, tanpa batas jam</p>
                </div>
            </div>
        </div>

        {{-- Gambar --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-50 file:text-orange-700 file:font-medium hover:file:bg-orange-100">
        </div>

        {{-- Status --}}
        <div class="flex items-center gap-3 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="pt-4 border-t">
            <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                Simpan Katering
            </button>
        </div>
    </form>
</div>
@endsection
