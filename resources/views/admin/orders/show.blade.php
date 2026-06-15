@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')
<a href="{{ route('admin.orders') }}" class="text-sm text-orange-500 hover:text-orange-600 mb-4 inline-block">← Kembali</a>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold">{{ $order->order_number }}</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ match($order->status) { 'pending_payment'=>'bg-yellow-100 text-yellow-700','processing'=>'bg-blue-100 text-blue-700','on_delivery'=>'bg-purple-100 text-purple-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-700' } }}">{{ $order->status_label }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Pelanggan:</span><br><b>{{ $order->user->name }}</b><br>{{ $order->user->phone }}<br>{{ $order->user->email }}</div>
                <div><span class="text-gray-500">Layanan:</span><br><b>{{ $order->cateringService->name ?? '-' }}</b></div>
                <div><span class="text-gray-500">Tanggal:</span><br><b>{{ $order->order_date->format('d M Y') }}</b></div>
                @if($order->event_start_time)
                    <div><span class="text-gray-500">Jam Acara:</span><br><b>{{ \Carbon\Carbon::parse($order->event_start_time)->format('H:i') }} WIB</b></div>
                @endif
                <div><span class="text-gray-500">Metode:</span><br><b>{{ ucfirst($order->pickup_method) }}</b></div>
            </div>
            @if($order->pickup_method === 'delivery')
            <div class="mt-4 pt-4 border-t text-sm">
                <b>Alamat:</b> {{ $order->district->name ?? '' }}, {{ $order->village->name ?? '' }}<br>{{ $order->address_detail }}
            </div>
            @endif
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <h3 class="font-bold mb-4">Item Pesanan</h3>
            <div class="divide-y">
                @foreach($order->items as $item)
                <div class="py-3 flex justify-between"><span>{{ $item->item_name }} (×{{ $item->quantity }})</span><span class="font-medium">Rp {{ number_format($item->subtotal,0,',','.') }}</span></div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border sticky top-24">
            <h3 class="font-bold mb-4">Pembayaran</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Rp {{ number_format($order->subtotal,0,',','.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Ongkir</span><span>Rp {{ number_format($order->shipping_cost,0,',','.') }}</span></div>
                <div class="flex justify-between text-lg font-bold pt-3 border-t"><span>Total</span><span class="text-orange-600">Rp {{ number_format($order->total,0,',','.') }}</span></div>
                <p class="text-xs text-gray-400 mt-2">Pembayaran: {{ $order->payment_method == 'transfer' ? 'Transfer (Midtrans)' : 'COD' }}</p>
                <p class="text-xs text-gray-400">Status: {{ $order->payment_status }}</p>
            </div>
            @if(!in_array($order->status, ['completed','cancelled']))
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4">
                @csrf @method('PUT')
                <label class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border text-sm mb-2">
                    @foreach(['pending_payment'=>'Menunggu Bayar','processing'=>'Diproses','on_delivery'=>'Dikirim','completed'=>'Selesai'] as $k=>$v)
                    <option value="{{ $k }}" {{ $order->status==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <button class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Update Status</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
