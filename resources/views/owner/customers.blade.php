@extends('layouts.owner')

@section('title', 'Data Pelanggan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Pelanggan</h2>
            <p class="text-sm text-gray-500 mt-1">Pelanggan yang paling sering memesan</p>
        </div>
        <div class="bg-gradient-to-r from-pink-500 to-rose-600 text-white px-4 py-2 rounded-xl font-semibold shadow flex items-center gap-2">
            <span>👥</span>
            <span>{{ $customers->count() }} Top Pelanggan</span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Peringkat</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Pelanggan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Email</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Telepon</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Total Pesanan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Bergabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $index => $customer)
                        <tr class="hover:bg-teal-50/30 transition-colors">
                            <td class="px-6 py-4">
                                @if($index < 3)
                                    <div class="w-8 h-8 bg-gradient-to-br {{ $index === 0 ? 'from-yellow-400 to-amber-500' : ($index === 1 ? 'from-gray-300 to-gray-400' : 'from-orange-400 to-amber-600') }} rounded-full flex items-center justify-center text-white text-sm font-bold shadow">
                                        {{ $index + 1 }}
                                    </div>
                                @else
                                    <span class="text-gray-400 font-medium ml-2">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $customer->email }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $customer->phone ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 bg-teal-50 text-teal-700 px-3 py-1 rounded-full text-xs font-bold">
                                    📦 {{ $customer->orders_count }} pesanan
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-sm">
                                {{ $customer->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-4xl">👥</span>
                                    <p class="text-gray-400 font-medium">Belum ada data pelanggan</p>
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
