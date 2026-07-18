@extends('layouts.owner')

@section('title', 'Data Pelanggan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Data Pelanggan</h2>
            <p class="fs-6 text-secondary mt-1">Pelanggan yang paling sering memesan</p>
        </div>
        <div class="text-white px-4 py-2 rounded fw-bold shadow d-flex align-items-center g-3">
            <span>👥</span>
            <span>{{ $customers->count() }} Top Pelanggan</span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded shadow-md overflow-hidden border border border-secondary">
        <div class="overflow-x-auto">
            <table class="w-100 fs-6">
                <thead class="border-b border border-secondary">
                    <tr>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Peringkat</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Pelanggan</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Email</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Telepon</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Total Pesanan</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Bergabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $index => $customer)
                        <tr class="/30">
                            <td class="px-6 py-4">
                                @if($index < 3)
                                    <div style="width: 32px; height: 32px;" class="{{ $index === 0 ? ' ' : ($index === 1 ? ' ' : ' ') }} rounded-pill d-flex align-items-center justify-content-center text-white fs-6 fw-bold shadow">
                                        {{ $index + 1 }}
                                    </div>
                                @else
                                    <span class="text-secondary fw-medium ms-2">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="d-flex align-items-center g-3">
                                    <div style="height: 36px;" class="w-9 rounded-pill d-flex align-items-center justify-content-center text-white fs-6 fw-bold">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-medium text-secondary">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-secondary">{{ $customer->email }}</td>
                            <td class="px-6 py-4 text-secondary">{{ $customer->phone ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="d-inline-d-flex align-items-center g-3 bg-teal-50 text-teal-700 px-3 py-1 rounded-pill small fw-bold">
                                    📦 {{ $customer->orders_count }} pesanan
                                </span>
                            </td>
                            <td class="px-6 py-4 text-secondary fs-6">
                                {{ $customer->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="d-flex d-flex-column align-items-center g-3">
                                    <span class="fs-1">👥</span>
                                    <p class="text-secondary fw-medium">Belum ada data pelanggan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
