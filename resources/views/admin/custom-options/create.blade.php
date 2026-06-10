@extends('layouts.admin') @section('title', 'Tambah Custom Option') @section('content')
<div class="max-w-2xl"><form action="{{ route('admin.custom-options.store') }}" method="POST" class="bg-white rounded-xl p-6 shadow-sm border space-y-4">@csrf
<div><label class="block text-sm font-medium mb-1">Layanan *</label><select name="catering_service_id" required class="w-full px-4 py-2 rounded-lg border">@foreach($services as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
<div><label class="block text-sm font-medium mb-1">Tipe *</label>
<select name="type" required class="w-full px-4 py-2 rounded-lg border">
    <option value="menu" {{ old('type') == 'menu' ? 'selected' : '' }}>Menu</option>
    <option value="decoration" {{ old('type') == 'decoration' ? 'selected' : '' }}>Dekorasi</option>
    <option value="serving_type" {{ old('type') == 'serving_type' ? 'selected' : '' }}>Tipe Penyajian</option>
    <option value="extra" {{ old('type') == 'extra' ? 'selected' : '' }}>Extra</option>
</select>
</div>
<div><label class="block text-sm font-medium mb-1">Nama *</label><input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border"></div>
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium mb-1">Harga (per porsi) *</label><input type="number" name="price" required value="{{ old('price') }}" class="w-full px-4 py-2 rounded-lg border"></div>
<div><label class="block text-sm font-medium mb-1">Min. Qty</label><input type="number" name="min_qty" value="{{ old('min_qty', 0) }}" min="0" class="w-full px-4 py-2 rounded-lg border"><p class="text-xs text-gray-500 mt-1">Minimum jumlah pemesanan (0 = tidak ada minimum).</p></div>
</div>
<label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded"><span class="text-sm">Aktif</span></label>
<button class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg">Simpan</button></form></div>@endsection
