@extends('layouts.admin')
@section('title', 'Kelola Paket')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="font-bold text-lg text-gray-800">📋 Paket Event</h3>
        <a href="{{ route('admin.packages.create') }}" class="px-5 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">+ Tambah Paket</a>
    </div>

    {{-- Service Filter --}}
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.packages.index') }}" class="px-4 py-2 rounded-full text-sm {{ !request('service') ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">Semua</a>
        @foreach($services as $s)
        <a href="{{ route('admin.packages.index', ['service'=>$s->id]) }}" class="px-4 py-2 rounded-full text-sm {{ request('service')==$s->id ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ $s->name }}</a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3.5 text-left font-semibold">Nama Paket</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Layanan</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Porsi</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Status</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($packages as $pkg)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $pkg->name }}</p>
                        @if($pkg->description)<p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $pkg->description }}</p>@endif
                    </td>
                    <td class="px-6 py-4 text-center text-gray-600">{{ $pkg->cateringService->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-center font-medium">{{ $pkg->total_portions }} porsi</td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">Rp {{ number_format($pkg->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $pkg->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.packages.edit', $pkg) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                            <form action="{{ route('admin.packages.destroy', $pkg) }}" method="POST" class="inline" onsubmit="return confirm('Hapus paket ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <p class="text-4xl mb-2">📋</p>
                        <p class="font-medium">Belum ada paket.</p>
                        <p class="text-xs mt-1">Buat paket event untuk layanan Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $packages->withQueryString()->links() }}</div>
</div>
@endsection
