@extends('layouts.admin')
@section('title', 'Pelanggan')
@section('content')

{{-- Tab Filter --}}
<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('admin.customers') }}"
       class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ !request('tab') ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
        Semua
    </a>
    <a href="{{ route('admin.customers', ['tab' => 'pending']) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('tab') === 'pending' ? 'bg-yellow-500 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
        ⏳ Menunggu Verifikasi
        @if($pendingCount > 0)
            <span class="ml-1 px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('admin.customers', ['tab' => 'suspended']) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('tab') === 'suspended' ? 'bg-red-500 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
        🚫 Ditangguhkan
        @if($suspendedCount > 0)
            <span class="ml-1 px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ $suspendedCount }}</span>
        @endif
    </a>
</div>

{{-- Search --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-4">
    <form action="{{ route('admin.customers') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        @if(request('tab'))
            <input type="hidden" name="tab" value="{{ request('tab') }}">
        @endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..." class="px-4 py-2 rounded-lg border border-gray-200 text-sm flex-1 min-w-[200px]">
        <button class="px-6 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600">Cari</button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
                <th class="px-6 py-3 text-left">Nama</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-center">Telepon</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-center">Pesanan</th>
                <th class="px-6 py-3 text-center">Total Belanja</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($customers as $c)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $c->name }}</td>
                <td class="px-6 py-4">{{ $c->email }}</td>
                <td class="px-6 py-4 text-center">
                    {{ $c->phone ?? '-' }}
                    @if($c->old_phone)
                        <br><span class="text-xs text-gray-400 line-through">{{ $c->old_phone }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    @if($c->status_suspend === 'suspended')
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">🚫 Ditangguhkan</span>
                    @elseif($c->status_suspend === 'pending_verification')
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">⏳ Menunggu Verifikasi</span>
                    @else
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">✅ Aktif</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">{{ $c->orders_count }}</td>
                <td class="px-6 py-4 text-center">Rp {{ number_format($c->orders_sum_total ?? 0,0,',','.') }}</td>
                <td class="px-6 py-4 text-center">
                    <a href="{{ route('admin.customers.show', $c) }}" class="text-orange-500 font-medium hover:text-orange-600">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada pelanggan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $customers->withQueryString()->links() }}</div>
@endsection
