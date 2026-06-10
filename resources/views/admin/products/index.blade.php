@extends('layouts.admin')
@section('title', 'Kelola Produk')
@section('content')
<div class="flex justify-between items-center mb-6">
    <form action="{{ route('admin.products.index') }}" method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="px-4 py-2 rounded-lg border text-sm">
        <select name="service" class="px-4 py-2 rounded-lg border text-sm">
            <option value="">Semua Layanan</option>
            @foreach($services as $s)<option value="{{ $s->id }}" {{ request('service')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach
        </select>
        <button class="px-4 py-2 bg-gray-200 text-sm rounded-lg">Filter</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="px-5 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600">+ Tambah Produk</a>
</div>
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600"><tr><th class="px-6 py-3 text-left">Produk</th><th class="px-6 py-3">Layanan</th><th class="px-6 py-3">Harga</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Aksi</th></tr></thead>
        <tbody class="divide-y">
            @forelse($products as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $p->name }} @if($p->is_best_seller)<span class="text-xs text-red-500">🔥</span>@endif</td>
                <td class="px-6 py-4 text-center">{{ $p->cateringService->name ?? '-' }}</td>
                <td class="px-6 py-4 text-center">{{ $p->formatted_price }}</td>
                <td class="px-6 py-4 text-center"><span class="px-2 py-1 rounded-full text-xs {{ $p->is_active?'bg-green-100 text-green-700':'bg-gray-100 text-gray-500' }}">{{ $p->is_active?'Aktif':'Nonaktif' }}</span></td>
                <td class="px-6 py-4 text-center space-x-2">
                    <a href="{{ route('admin.products.edit', $p) }}" class="text-blue-500 hover:underline">Edit</a>
                    <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk?')">@csrf @method('DELETE')<button class="text-red-500 hover:underline">Hapus</button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->withQueryString()->links() }}</div>
@endsection
