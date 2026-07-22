<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;

use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Tagihan;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    /**
     * Lanjut Ke Pembayaran page untuk Daily.
     */
    public function index($menu_date = null)
    {
        abort(404, 'Fitur checkout sedang dalam perbaikan.');
    }

    /**
     * Store checkout harian.
     */
    public function store(Request $request, $menu_date = null)
    {
        abort(404, 'Fitur checkout sedang dalam perbaikan.');
    }

    /**
     * Tampilkan halaman checkout untuk acara group (atau semua acara group jika $groupId === 'all').
     */
    public function showAcaraCheckout(string $groupId)
    {
        abort(404, 'Fitur checkout sedang dalam perbaikan.');
    }

    /**
     * Lanjut Ke Pembayaran per acara group atau seluruh acara group sekaligus.
     */
    public function checkoutGrupAcara(Request $request, string $groupId)
    {
        abort(404, 'Fitur checkout sedang dalam perbaikan.');
    }
}
