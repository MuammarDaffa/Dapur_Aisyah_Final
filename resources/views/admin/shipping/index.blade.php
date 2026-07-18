@extends('layouts.admin')

@section('title', 'Manajemen Ongkos Kirim')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Ongkos Kirim</h2>
            <p class="fs-6 text-secondary mt-1">Kelola biaya pengiriman per kecamatan</p>
        </div>
        <a href="{{ route('admin.shipping.create') }}"
           class="hover: hover: text-white px-5 py-2.5 rounded fw-bold shadow-md hover:shadow d-flex align-items-center g-3">
            <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Ongkir
        </a>
    </div>

    {{-- Info Card --}}
    <div class="bg-info text-white border border-blue-200 rounded p-4 d-flex align-items-center g-3">
        <svg style="width: 24px; height: 24px;" class="text-info flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <p class="fw-medium text-info">Default Ongkos Kirim</p>
            <p class="fs-6 text-info">Kecamatan yang belum diatur akan menggunakan ongkir default: <strong>Rp 20.000</strong></p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded shadow-md overflow-hidden border border border-secondary">
        <div class="overflow-x-auto">
            <table class="w-100 fs-6">
                <thead class="border-b border border-secondary">
                    <tr>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">No</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Kecamatan</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Biaya</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Catatan</th>
                        <th class="text-center px-6 py-4 fw-bold text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($shippingCosts as $index => $shipping)
                        <tr class="hover:bg-primary text-white/30">
                            <td class="px-6 py-4 text-secondary fw-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="d-flex align-items-center g-3">
                                    <svg style="width: 20px; height: 20px;" class="text-danger flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="fw-medium text-secondary">{{ $shipping->district->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="fw-bold text-success text-base">
                                    Rp {{ number_format($shipping->cost, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-secondary">{{ $shipping->notes ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="d-flex align-items-center justify-content-center g-3">
                                    <a href="{{ route('admin.shipping.edit', $shipping) }}"
                                       class="text-info hover:text-info hover:bg-info text-white p-2 rounded"
                                       title="Edit">
                                        <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.shipping.destroy', $shipping) }}" method="POST"
                                          onsubmit="event.preventDefault(); Swal.fire({ title: 'Hapus ongkos kirim?', text: 'Data ini akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Hapus', cancelButtonText: 'Batal' }).then((r) => { if(r.isConfirmed) this.submit(); })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger btn btn-danger text-danger hover:text-danger hover:bg-danger text-white p-2 rounded"
                                                title="Hapus">
                                            <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="d-flex d-flex-column align-items-center g-3">
                                    <div style="width: 64px; height: 64px;" class="bg-light rounded-pill d-flex align-items-center justify-content-center mb-1">
                                        <svg style="width: 32px; height: 32px;" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                    </div>
                                    <p class="text-secondary fw-medium">Belum ada data ongkos kirim</p>
                                    <a href="{{ route('admin.shipping.create') }}" class="text-primary hover:text-primary fw-medium fs-6">+ Tambah Sekarang</a>
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
