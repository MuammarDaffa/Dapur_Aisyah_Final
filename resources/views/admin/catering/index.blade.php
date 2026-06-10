@extends('layouts.admin')
@section('title', 'Kelola Katering')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">🍲 Katering</h3>
            <p class="text-sm text-gray-500 mt-1">Kelola semua layanan katering Anda</p>
        </div>
        <a href="{{ route('admin.catering.create') }}" class="px-5 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
            + Tambah Katering
        </a>
    </div>

    {{-- Filters --}}
    <form action="{{ route('admin.catering.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[200px] max-w-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari katering..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-orange-500 focus:border-orange-500">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.catering.index', ['search' => request('search')]) }}"
               class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ !request('type') ? 'bg-gray-800 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.catering.index', ['search' => request('search'), 'type' => 'daily']) }}"
               class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request('type') === 'daily' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                📦 Daily
            </a>
            <a href="{{ route('admin.catering.index', ['search' => request('search'), 'type' => 'event']) }}"
               class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request('type') === 'event' ? 'bg-purple-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                🎉 Event
            </a>
        </div>
        <button type="submit" class="px-4 py-2.5 bg-gray-200 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">Filter</button>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3.5 text-left font-semibold">Nama Katering</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Tipe</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Harga Mulai</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Status</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($caterings as $c)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $c->name }}</p>
                        @if($c->description)
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ Str::limit($c->description, 60) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($c->isDaily())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">📦 Daily</span>
                        @elseif($c->isEvent())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">🎉 Event</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">
                        Rp {{ number_format($c->base_price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $c->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.catering.show', $c) }}" class="px-3 py-1.5 text-xs font-medium text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">Detail</a>
                            <a href="{{ route('admin.catering.edit', $c) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                            <form action="{{ route('admin.catering.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Hapus katering ini beserta semua data terkait?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <p class="text-4xl mb-2">🍲</p>
                        <p class="font-medium">Belum ada katering.</p>
                        <p class="text-xs mt-1">Buat layanan katering pertama Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $caterings->withQueryString()->links() }}</div>
</div>
@endsection
