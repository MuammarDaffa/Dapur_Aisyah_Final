@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-4">
    <form action="{{ route('admin.orders') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari order/nama..." class="px-4 py-2 rounded-lg border border-gray-200 text-sm flex-1 min-w-[180px]">
        <select name="status" class="px-4 py-2 rounded-lg border border-gray-200 text-sm">
            <option value="">Semua Status</option>
            @foreach(['pending_payment'=>'Menunggu Bayar','processing'=>'Diproses','on_delivery'=>'Dikirim','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $k=>$v)
                <option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
        <select name="date_type" class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-orange-400 focus:ring-0">
            <option value="order_date" {{ request('date_type') == 'order_date' ? 'selected' : '' }}>Tanggal Pengiriman</option>
            <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Tanggal Order</option>
        </select>
        <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-orange-400 focus:ring-0">
        <button class="px-6 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg">Filter</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[800px]">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
            <tr>
                <th class="px-6 py-3 text-left">Order</th>
                <th class="px-6 py-3 text-left">Pelanggan</th>
                <th class="px-6 py-3 text-left">Layanan</th>
                <th class="px-6 py-3 text-left">Total</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Tanggal Order</th>
                <th class="px-6 py-3 text-left">Tanggal Pengiriman</th>
                <th class="px-6 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $order->order_number }}</td>
                    <td class="px-6 py-4">{{ $order->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $order->cateringService->name ?? '-' }}</td>
                    <td class="px-6 py-4 font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ match($order->status) { 'pending_payment'=>'bg-yellow-100 text-yellow-700','processing'=>'bg-blue-100 text-blue-700','on_delivery'=>'bg-purple-100 text-purple-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700', default=>'bg-gray-100 text-gray-700' } }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 font-medium text-orange-600">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-orange-500 hover:text-orange-600 font-medium">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">Tidak ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $orders->withQueryString()->links() }}</div>
@endsection
