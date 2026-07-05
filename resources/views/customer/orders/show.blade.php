@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('customer.orders') }}" class="inline-flex items-center text-sm text-orange-500 hover:text-orange-600 mb-6">← Kembali ke Pesanan</a>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $order->order_number }}</h2>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-sm font-medium
                        {{ match($order->status) {
                            'pending_payment' => 'bg-yellow-100 text-yellow-700',
                            'processing' => 'bg-blue-100 text-blue-700',
                            'on_delivery' => 'bg-purple-100 text-purple-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700'
                        } }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Layanan:</span><br><span class="font-medium">{{ $order->cateringService->name ?? '-' }}</span></div>
                    <div><span class="text-gray-500">Tanggal Acara:</span><br><span class="font-medium">{{ $order->order_date->format('d M Y') }}</span></div>
                    <div><span class="text-gray-500">Metode:</span><br><span class="font-medium">{{ $order->pickup_method === 'delivery' ? 'Delivery' : 'Pick Up' }}</span></div>
                    <div><span class="text-gray-500">Pembayaran:</span><br><span class="font-medium">Transfer (Midtrans)</span></div>
                </div>
                @if($order->pickup_method === 'delivery')
                    <div class="mt-4 pt-4 border-t border-gray-100 text-sm">
                        <p class="text-gray-500 mb-1">Alamat Pengiriman:</p>
                        <p class="font-medium">{{ $order->district->name ?? '' }}, {{ $order->village->name ?? '' }}</p>
                        <p class="text-gray-600">{{ $order->address_detail }}</p>
                    </div>
                @endif
                @if($order->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100 text-sm">
                        <p class="text-gray-500 mb-1">Catatan:</p>
                        <p>{{ $order->notes }}</p>
                    </div>
                @endif
                @if($order->cancellation_reason)
                    <div class="mt-4 pt-4 border-t border-gray-100 text-sm">
                        <p class="text-red-600 font-medium mb-1">Alasan Pembatalan:</p>
                        <p>{{ $order->cancellation_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4">Item Pesanan</h3>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="py-3 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-900">{{ $item->item_name }}</p>
                                <p class="text-xs text-gray-500">Rp {{ number_format($item->unit_price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                            </div>
                            <p class="font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Review Form (only for completed orders without review) -->
            @if($order->status === 'completed' && !$order->review)
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">⭐ Beri Ulasan</h3>
                    <form action="{{ route('customer.reviews.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                            <div class="flex space-x-1" id="star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})" class="text-3xl text-gray-300 hover:text-yellow-400 transition-colors star-btn" data-rating="{{ $i }}">★</button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="rating-input" value="5" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
                            <textarea name="comment" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" placeholder="Tulis ulasan Anda..."></textarea>
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Kirim Ulasan</button>
                    </form>
                </div>
            @endif

            @if($order->review)
                <div class="bg-orange-50 rounded-xl p-6 border border-orange-100">
                    <h3 class="font-bold text-gray-900 mb-2">⭐ Ulasan Anda</h3>
                    <div class="flex text-yellow-400 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $order->review->rating ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600">{{ $order->review->comment ?? 'Tidak ada komentar.' }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 sticky top-24">
                <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Ongkos Kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                        <span>Total</span>
                        <span class="text-orange-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->status === 'pending_payment' && $order->midtrans_snap_token)
                    <button onclick="payNow()" class="w-full mt-4 px-6 py-3 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition-colors">
                        💳 Bayar Sekarang
                    </button>
                @endif

                @php
                    $isProcessing = $order->status === 'processing';
                    $isEvent = $order->cateringService?->isEvent() ?? false;
                    $refundPercent = 100;
                    $timeElapsedStr = '';
                    
                    if ($isProcessing) {
                        $paymentTime = $order->updated_at;
                        $now = now();
                        
                        if ($isEvent) {
                            $hoursPassed = $paymentTime->diffInHours($now);
                            $refundPercent = $hoursPassed <= 24 ? 100 : 50;
                            $timeElapsedStr = $hoursPassed . ' jam';
                        } else {
                            $minutesPassed = $paymentTime->diffInMinutes($now);
                            $refundPercent = $minutesPassed <= 10 ? 100 : 50;
                            $timeElapsedStr = $minutesPassed . ' menit';
                        }
                    }
                @endphp

                @if(in_array($order->status, ['pending_payment', 'processing']))
                    <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="w-full mt-3 px-6 py-2.5 border border-red-300 text-red-600 font-medium rounded-xl hover:bg-red-50 transition-colors">
                        Batalkan Pesanan
                    </button>

                    <!-- Cancel Modal -->
                    <div id="cancelModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('cancelModal').classList.add('hidden')"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <form action="{{ route('customer.orders.cancel', $order) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="cancellation_reason" value="Dibatalkan oleh pelanggan">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <div class="sm:flex sm:items-start">
                                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                <span class="text-2xl">⚠️</span>
                                            </div>
                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                    Batalkan Pesanan
                                                </h3>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500 mb-3">Apakah Anda yakin ingin membatalkan pesanan ini?</p>
                                                    
                                                    @if($isProcessing)
                                                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-3">
                                                            <h4 class="font-semibold text-blue-800 text-sm mb-1">Informasi Refund</h4>
                                                            <p class="text-sm text-blue-700">
                                                                Waktu berlalu sejak pembayaran: <strong>{{ $timeElapsedStr }}</strong><br>
                                                                Syarat Refund Anda: <strong>{{ $refundPercent }}%</strong>
                                                            </p>
                                                            <p class="text-xs text-blue-600 mt-2">
                                                                * Ketentuan: Event (<= 24 jam: 100%, > 24 jam: 50%). Daily (<= 10 menit: 100%, > 10 menit: 50%).
                                                            </p>
                                                        </div>
                                                    @else
                                                        <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 mb-3">
                                                            <p class="text-sm text-yellow-700">
                                                                Pesanan ini belum dibayar. Pembatalan tidak memerlukan proses refund.
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:w-auto sm:text-sm">
                                            Ya, Batalkan
                                        </button>
                                        <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                            Tutup
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tombol Refund WhatsApp --}}
                @if($order->status === 'cancelled' && ($order->refund_status ?? 'none') === 'pending')
                    @php
                        $waNumber = '6289655951299';
                        $waText = urlencode("Halo Admin Dapur Aisyah,\n\nSaya ingin mengajukan refund untuk pesanan:\n- No. Order: {$order->order_number}\n- Total: Rp " . number_format($order->total, 0, ',', '.') . "\n\nMohon bantuannya. Terima kasih.");
                        $waUrl = "https://wa.me/{$waNumber}?text={$waText}";
                    @endphp
                    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <p class="text-sm text-green-800 font-medium mb-2">💰 Pesanan Anda telah dibatalkan dan memenuhi syarat refund.</p>
                        <a href="{{ $waUrl }}" target="_blank" class="block w-full px-6 py-3 bg-green-600 text-white font-semibold text-center rounded-xl hover:bg-green-700 transition-colors">
                            📱 Ajukan Refund via WhatsApp
                        </a>
                    </div>
                @endif

                @if($order->invoice)
                    <p class="mt-4 text-xs text-gray-500 text-center">Invoice: {{ $order->invoice->invoice_number }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function setRating(rating) {
    document.getElementById('rating-input').value = rating;
    document.querySelectorAll('.star-btn').forEach((btn, i) => {
        btn.classList.toggle('text-yellow-400', i < rating);
        btn.classList.toggle('text-gray-300', i >= rating);
    });
}
setRating(5);

@if($order->midtrans_snap_token)
function payNow() {
    window.snap.pay('{{ $order->midtrans_snap_token }}', {
        onSuccess: () => location.reload(),
        onPending: () => location.reload(),
        onError: () => alert('Pembayaran gagal'),
        onClose: () => {}
    });
}
@endif
</script>
@if($order->midtrans_snap_token)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif
@endpush
@endsection
