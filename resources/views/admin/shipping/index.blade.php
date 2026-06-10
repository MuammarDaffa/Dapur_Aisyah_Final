@extends('layouts.admin')

@section('title', 'Manajemen Ongkos Kirim')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Ongkos Kirim</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola biaya pengiriman per kecamatan</p>
        </div>
        <a href="{{ route('admin.shipping.create') }}"
           class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Ongkir
        </a>
    </div>

    {{-- Info Card --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-3">
        <span class="text-2xl">💡</span>
        <div>
            <p class="font-medium text-blue-800">Default Ongkos Kirim</p>
            <p class="text-sm text-blue-600">Kecamatan yang belum diatur akan menggunakan ongkir default: <strong>Rp 20.000</strong></p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">No</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Kecamatan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Biaya</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Catatan</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($shippingCosts as $index => $shipping)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="px-6 py-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">📍</span>
                                    <span class="font-medium text-gray-800">{{ $shipping->district->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-green-600 text-base">
                                    Rp {{ number_format($shipping->cost, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $shipping->notes ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.shipping.edit', $shipping) }}"
                                       class="text-blue-500 hover:text-blue-700 hover:bg-blue-50 p-2 rounded-lg transition-all"
                                       title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.shipping.destroy', $shipping) }}" method="POST"
                                          onsubmit="event.preventDefault(); Swal.fire({ title: 'Hapus ongkos kirim?', text: 'Data ini akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Hapus', cancelButtonText: 'Batal' }).then((r) => { if(r.isConfirmed) this.submit(); })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all"
                                                title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-4xl">🚚</span>
                                    <p class="text-gray-400 font-medium">Belum ada data ongkos kirim</p>
                                    <a href="{{ route('admin.shipping.create') }}" class="text-orange-500 hover:text-orange-600 font-medium text-sm">+ Tambah Sekarang</a>
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
