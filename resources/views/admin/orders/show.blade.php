@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')
<a href="{{ route('admin.orders') }}" class="text-sm text-orange-500 hover:text-orange-600 mb-4 inline-block">← Kembali</a>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-2 mb-4">
                <h3 class="text-xl font-bold">{{ $order->order_number }}</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ match($order->status) { 'processing'=>'bg-blue-100 text-blue-700','on_delivery'=>'bg-purple-100 text-purple-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-700' } }}">{{ $order->status_label }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Pelanggan:</span><br><b>{{ $order->user->name }}</b><br>{{ $order->user->phone }}<br>{{ $order->user->email }}</div>
                <div><span class="text-gray-500">Layanan:</span><br><b>{{ $order->cateringService->name ?? '-' }}</b></div>
                <div><span class="text-gray-500">Tanggal:</span><br><b>{{ $order->order_date->format('d M Y') }}</b></div>
                <div><span class="text-gray-500">Metode:</span><br><b>{{ ucfirst($order->pickup_method) }}</b></div>
            </div>
            @if($order->pickup_method === 'delivery')
            <div class="mt-4 pt-4 border-t text-sm">
                <b>Alamat:</b> {{ $order->district->name ?? '' }}, {{ $order->village->name ?? '' }}<br>{{ $order->address_detail }}
                @if($order->latitude && $order->longitude)
                    <p class="text-xs text-gray-400 mt-1">Koordinat: {{ $order->latitude }}, {{ $order->longitude }}</p>
                @endif
            </div>
            @endif
            @if($order->cancellation_reason)
            <div class="mt-4 pt-4 border-t text-sm">
                <p class="text-red-600 font-medium">Alasan Pembatalan:</p>
                <p class="text-gray-700">{{ $order->cancellation_reason }}</p>
            </div>
            @endif
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <h3 class="font-bold mb-4">Item Pesanan</h3>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                <div class="py-3">
                    <p class="font-medium text-gray-900">{{ $item->formatted_menu_name }}</p>
                    @if($item->formatted_extras)
                        <p class="text-sm text-gray-600 mt-0.5"><span class="font-medium">Extra:</span> {{ $item->formatted_extras }}</p>
                    @endif
                    <p class="text-sm font-medium text-gray-900 mt-1">Total: Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
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
                <p class="text-xs text-gray-400 mt-2">Pembayaran: Transfer (Midtrans)</p>
                <p class="text-xs text-gray-400">Status: {{ $order->payment_status }}</p>
                @if($order->refund_status && $order->refund_status !== 'none')
                    <p class="text-xs font-medium {{ $order->refund_status === 'pending' ? 'text-yellow-600' : 'text-green-600' }}">
                        Refund: {{ $order->refund_status === 'pending' ? '⏳ Menunggu Refund' : '✅ Sudah Direfund' }}
                    </p>
                @endif
            </div>
            @if(!in_array($order->status, ['completed','cancelled']))
            {{-- Form Update Status --}}
            <form id="statusForm" action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4">
                @csrf @method('PUT')
                <label class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                <select name="status" id="statusSelect" class="w-full px-3 py-2 rounded-lg border text-sm mb-2">
                    @foreach(['processing'=>'Diproses','on_delivery'=>'Dikirim','completed'=>'Selesai'] as $k=>$v)
                    <option value="{{ $k }}" {{ $order->status==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Update Status</button>
            </form>

            {{-- Tombol Batalkan --}}
            <form id="cancelForm" action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="mt-3">
                @csrf @method('PUT')
                <input type="hidden" name="cancellation_reason" id="cancelReasonInput">
                <button type="button" onclick="confirmCancel()" class="w-full px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                    ❌ Batalkan Pesanan
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmCancel() {
    Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Masukkan alasan pembatalan pesanan ini:',
        input: 'textarea',
        inputLabel: 'Alasan Pembatalan',
        inputPlaceholder: 'Tulis alasan pembatalan...',
        inputAttributes: {
            'aria-label': 'Tulis alasan pembatalan',
            maxlength: 500
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        inputValidator: (value) => {
            if (!value || value.trim().length < 5) {
                return 'Alasan pembatalan harus diisi (minimal 5 karakter).';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelReasonInput').value = result.value;
            document.getElementById('cancelForm').submit();
        }
    });
}
</script>
@endpush
@endsection
