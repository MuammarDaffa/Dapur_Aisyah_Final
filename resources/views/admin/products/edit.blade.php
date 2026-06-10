@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-4">
        @csrf @method('PUT')
        <div><label class="block text-sm font-medium mb-1">Layanan *</label><select name="catering_service_id" required class="w-full px-4 py-2 rounded-lg border">@foreach($services as $s)<option value="{{ $s->id }}" {{ $product->catering_service_id==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium mb-1">Nama Produk *</label><input type="text" name="name" required value="{{ old('name', $product->name) }}" class="w-full px-4 py-2 rounded-lg border"></div>
        <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border">{{ old('description', $product->description) }}</textarea></div>
        <div><label class="block text-sm font-medium mb-1">Harga (Rp) *</label><input type="number" name="price" required value="{{ old('price', $product->price) }}" class="w-full px-4 py-2 rounded-lg border"></div>
        <div><label class="block text-sm font-medium mb-1">Gambar</label><input type="file" name="image" accept="image/*" class="w-full px-4 py-2 rounded-lg border">@if($product->image)<p class="text-xs text-gray-500 mt-1">Sudah ada gambar</p>@endif</div>
        <div class="flex gap-6"><label class="flex items-center gap-2"><input type="checkbox" name="is_best_seller" value="1" {{ $product->is_best_seller?'checked':'' }} class="rounded border-gray-300 text-orange-500"><span class="text-sm">Best Seller</span></label><label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ $product->is_active?'checked':'' }} class="rounded border-gray-300 text-orange-500"><span class="text-sm">Aktif</span></label></div>
        <div><label class="block text-sm font-medium mb-1">Hari Tersedia</label><div class="flex flex-wrap gap-2">@foreach(['senin','selasa','rabu','kamis','jumat','sabtu'] as $d)<label class="flex items-center gap-1"><input type="checkbox" name="available_days[]" value="{{ $d }}" {{ in_array($d, $product->available_days ?? [])?'checked':'' }} class="rounded border-gray-300 text-orange-500"><span class="text-sm">{{ ucfirst($d) }}</span></label>@endforeach</div></div>
        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600">Update Produk</button>
    </form>
</div>
@endsection
