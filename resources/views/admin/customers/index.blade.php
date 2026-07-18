@extends('layouts.admin')
@section('title', 'Pelanggan')
@section('content')

{{-- Search --}}
<div class="bg-white rounded shadow-sm border p-4 mb-4">
    <form action="{{ route('admin.customers') }}" method="GET" class="d-flex d-flex-wrap g-3 items-end">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..." class="form-control px-4 py-2 rounded border border border-secondary fs-6 d-flex-1 min-w-[200px]">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-100 fs-6 min-w-[500px]">
        <thead class="bg-light small uppercase text-secondary">
            <tr>
                <th class="px-6 py-3 text-start">Nama</th>
                <th class="px-6 py-3 text-start">Email</th>
                <th class="px-6 py-3 text-center">Telepon</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($customers as $c)
            <tr class="hover:bg-light">
                <td class="px-6 py-4 fw-medium">{{ $c->name }}</td>
                <td class="px-6 py-4">{{ $c->email }}</td>
                <td class="px-6 py-4 text-center">{{ $c->phone ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-6 py-8 text-center text-secondary">Tidak ada pelanggan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $customers->withQueryString()->links() }}</div>
@endsection
