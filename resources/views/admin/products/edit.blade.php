@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors">← Kembali</a>
    </div>
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 shadow-sm border space-y-4">
        @csrf @method('PUT')
        <input type="hidden" name="catering_service_id" value="{{ $product->catering_service_id }}">
        <div><label class="block text-sm font-medium mb-1">Nama Produk *</label><input type="text" name="name" required value="{{ old('name', $product->name) }}" class="w-full px-4 py-2 rounded-lg border"></div>
        <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border">{{ old('description', $product->description) }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Harga (Rp) *</label><input type="number" name="price" required value="{{ old('price', $product->price) }}" class="w-full px-4 py-2 rounded-lg border"></div>
            <div>
                <label class="block text-sm font-medium mb-1">Status Produk *</label>
                <select name="status" class="w-full px-4 py-2 rounded-lg border">
                    <option value="tersedia" {{ old('status', $product->status ?? 'tersedia') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="habis" {{ old('status', $product->status ?? 'tersedia') === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
        </div>
        <div><label class="block text-sm font-medium mb-1">Gambar</label><input type="file" name="image" accept="image/*" class="w-full px-4 py-2 rounded-lg border">@if($product->image)<p class="text-xs text-gray-500 mt-1">📷 Sudah ada gambar. Upload baru untuk mengganti.</p>@endif</div>
        <div class="flex gap-6"><label class="flex items-center gap-2"><input type="checkbox" name="is_best_seller" value="1" {{ $product->is_best_seller?'checked':'' }} class="rounded border-gray-300 text-orange-500"><span class="text-sm">Best Seller</span></label><label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ $product->is_active?'checked':'' }} class="rounded border-gray-300 text-orange-500"><span class="text-sm">Aktif</span></label></div>
        <div><label class="block text-sm font-medium mb-1">Hari Tersedia *</label><select name="available_days" required class="w-full px-4 py-2 rounded-lg border"><option value="" disabled>Pilih Hari</option>@foreach(['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $d)<option value="{{ $d }}" {{ old('available_days', $product->available_days) === $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>@endforeach</select>@error('available_days')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror</div>
        @if(isset($extras) && $extras->count() > 0)
        @php $selectedExtras = $product->extras->pluck('id')->toArray(); @endphp
        <div><label class="block text-sm font-medium mb-1">Extra Tambahan (Opsional)</label><div class="flex flex-wrap gap-2">@foreach($extras as $extra)<label class="flex items-center gap-1"><input type="checkbox" name="extras[]" value="{{ $extra->id }}" {{ in_array($extra->id, old('extras', $selectedExtras)) ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500"><span class="text-sm">{{ $extra->name }}</span></label>@endforeach</div>@error('extras')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror</div>
        @endif
        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600">Update Produk</button>
    </form>
</div>
@endsection
