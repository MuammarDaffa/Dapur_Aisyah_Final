@extends('layouts.admin')
@section('title', 'Pelanggan')
@section('content')

{{-- Search --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-4">
    <form action="{{ route('admin.customers') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..." class="px-4 py-2 rounded-lg border border-gray-200 text-sm flex-1 min-w-[200px]">
        <button type="submit" class="px-6 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600">Cari</button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[500px]">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
                <th class="px-6 py-3 text-left">Nama</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-center">Telepon</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($customers as $c)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $c->name }}</td>
                <td class="px-6 py-4">{{ $c->email }}</td>
                <td class="px-6 py-4 text-center">{{ $c->phone ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Tidak ada pelanggan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $customers->withQueryString()->links() }}</div>
@endsection
