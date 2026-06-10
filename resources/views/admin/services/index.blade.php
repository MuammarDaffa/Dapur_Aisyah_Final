@extends('layouts.admin')
@section('title', 'Kelola Layanan')
@section('content')
<div class="flex justify-between mb-6"><h3 class="font-bold">Layanan Katering</h3><a href="{{ route('admin.services.create') }}" class="px-5 py-2 bg-orange-500 text-white text-sm rounded-lg">+ Tambah</a></div>
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
<table class="w-full text-sm"><thead class="bg-gray-50 text-xs uppercase text-gray-600"><tr><th class="px-6 py-3 text-left">Nama</th><th class="px-6 py-3">Harga Mulai</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Aksi</th></tr></thead>
<tbody class="divide-y">@forelse($services as $s)<tr class="hover:bg-gray-50"><td class="px-6 py-4 font-medium">{{ $s->name }}</td><td class="px-6 py-4 text-center">Rp {{ number_format($s->base_price,0,',','.') }}</td><td class="px-6 py-4 text-center"><span class="px-2 py-1 rounded-full text-xs {{ $s->is_active?'bg-green-100 text-green-700':'bg-gray-100 text-gray-500' }}">{{ $s->is_active?'Aktif':'Nonaktif' }}</span></td><td class="px-6 py-4 text-center"><a href="{{ route('admin.services.edit', $s) }}" class="text-blue-500">Edit</a> <form action="{{ route('admin.services.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 ml-2">Hapus</button></form></td></tr>@empty<tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Tidak ada layanan.</td></tr>@endforelse</tbody></table></div>
<div class="mt-4">{{ $services->links() }}</div>
@endsection
