@extends('layouts.admin')
@section('title', 'Tambah Layanan')
@section('content')
<div class="max-w-2xl"><form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-4">@csrf
<div><label class="block text-sm font-medium mb-1">Nama *</label><input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border"></div>
<div><label class="block text-sm font-medium mb-1">Deskripsi *</label><textarea name="description" rows="3" required class="w-full px-4 py-2 rounded-lg border">{{ old('description') }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">Harga Dasar *</label><input type="number" name="base_price" required value="{{ old('base_price') }}" class="w-full px-4 py-2 rounded-lg border"></div>
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium mb-1">Min. Porsi *</label><input type="number" name="min_portion" required value="{{ old('min_portion', 1) }}" class="w-full px-4 py-2 rounded-lg border"></div>
<div><label class="block text-sm font-medium mb-1">Max. Porsi</label><input type="number" name="max_portion" value="{{ old('max_portion') }}" placeholder="Kosongkan jika tidak dibatasi" class="w-full px-4 py-2 rounded-lg border"></div>
</div>
<div><label class="block text-sm font-medium mb-2">Fitur Tersedia</label>
<div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-lg border">
@foreach(['daily_menu' => '📦 Menu Harian (Produk)', 'packages' => '📋 Paket Event', 'full_custom' => '🎨 Full Custom Event'] as $key => $label)
<label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="available_features[]" value="{{ $key }}" {{ in_array($key, old('available_features', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-400"><span class="text-sm">{{ $label }}</span></label>
@endforeach
</div>
<p class="text-xs text-gray-500 mt-1">Pilih fitur yang tersedia untuk layanan ini. Layanan harian menggunakan produk, layanan event menggunakan paket/custom.</p>
</div>
<div><label class="block text-sm font-medium mb-1">Ketentuan</label><textarea name="order_terms" rows="2" class="w-full px-4 py-2 rounded-lg border">{{ old('order_terms') }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">Jadwal</label><textarea name="schedule_notes" rows="2" class="w-full px-4 py-2 rounded-lg border">{{ old('schedule_notes') }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">Gambar</label><input type="file" name="image" accept="image/*" class="w-full px-4 py-2 rounded-lg border"></div>
<label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded"><span class="text-sm">Aktif</span></label>
<button class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg">Simpan</button></form></div>
@endsection
