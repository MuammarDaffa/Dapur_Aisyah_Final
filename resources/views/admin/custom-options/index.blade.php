@extends('layouts.admin')
@section('title', 'Custom Options')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-bold text-lg text-gray-800">⚙️ Custom Options</h3>
        <a href="{{ route('admin.custom-options.create') }}" class="px-5 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">+ Tambah</a>
    </div>

    {{-- Type Filters --}}
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.custom-options.index') }}" class="px-4 py-2 rounded-full text-sm {{ !request('type') ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">Semua</a>
        @foreach(['menu'=>'Menu','decoration'=>'Dekorasi','serving_type'=>'Penyajian','extra'=>'Extra'] as $k=>$v)
            <a href="{{ route('admin.custom-options.index', ['type'=>$k]) }}" class="px-4 py-2 rounded-full text-sm {{ request('type')==$k ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ $v }}</a>
        @endforeach
        {{-- Tipe custom tambahan dari database --}}
        @foreach($types as $t)
            @if(!in_array($t, ['menu','decoration','serving_type','extra']))
            <a href="{{ route('admin.custom-options.index', ['type'=>$t]) }}" class="px-4 py-2 rounded-full text-sm {{ request('type')==$t ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ ucfirst(str_replace('_',' ',$t)) }}</a>
            @endif
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3.5 text-left font-semibold">Nama</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Tipe</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Layanan</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($options as $o)
                    <tr class="hover:bg-orange-50/30 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $o->name }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ ucfirst($o->type) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-orange-600">Rp {{ number_format($o->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $o->cateringService->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.custom-options.edit', $o) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                                <form action="{{ route('admin.custom-options.destroy', $o) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <p class="text-4xl mb-2">⚙️</p>
                            <p class="font-medium">Tidak ada data.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $options->withQueryString()->links() }}</div>
</div>
@endsection
