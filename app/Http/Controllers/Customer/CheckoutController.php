<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Kecamatan;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\OngkosKirim;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Checkout page untuk Daily.
     */
    public function index($menu_date = null)
    {
        $user = auth()->user();
        if ($user) {
            \App\Models\Keranjang::cleanupInvalidAndExpiredItems($user->id);
        }
        $cartsQuery = $user->keranjang()
            ->whereNull('cart_group_id') // Hanya daily
            ->with(['produk.layananKatering', 'opsiKustom', 'paketKatering']);
            
        $keranjang = $cartsQuery->get();

        $existingOrder = Pesanan::where('user_id', $user->id)
            ->where('status', 'menunggu_pembayaran')
            ->where('status_pembayaran', 'belum_dibayar')
            ->latest()
            ->first();

        if ($keranjang->isEmpty() && !$existingOrder) {
            return redirect()->route('customer.keranjang')
                ->with('error', 'Keranjang harian kosong atau tanggal tidak valid.');
        }

        // Group (untuk daily, setiap item = 1 group)
        $groupedCarts = $keranjang->groupBy(function ($keranjang) {
            return 'ungrouped_' . $keranjang->id;
        });

        $kecamatan = Kecamatan::with('desa')->get();
        $subtotal = $keranjang->sum(fn ($keranjang) => $keranjang->subtotal);
        if ($keranjang->isEmpty() && $existingOrder) {
            $subtotal = (float) $existingOrder->total;
        }
        
        $orderDate = $keranjang->min('menu_date') ? $keranjang->min('menu_date')->format('Y-m-d') : ($existingOrder ? $existingOrder->tanggal_pesanan->format('Y-m-d') : date('Y-m-d'));
        
        // Pass cutoff data if available
        $firstCart = $keranjang->first();
        $service = $firstCart && $firstCart->produk ? $firstCart->produk->layananKatering : ($firstCart && $firstCart->opsiKustom ? $firstCart->opsiKustom->layananKatering : null);

        return view('customer.checkout', compact('keranjang', 'groupedCarts', 'kecamatan', 'subtotal', 'user', 'orderDate', 'service', 'existingOrder'));
    }

    /**
     * Store daily checkout.
     */
    public function store(Request $request, $menu_date = null)
    {
        $user = auth()->user();
        if ($user) {
            \App\Models\Keranjang::cleanupInvalidAndExpiredItems($user->id);
        }
        $cartsQuery = $user->keranjang()
            ->whereNull('cart_group_id') // Hanya daily
            ->with(['produk.layananKatering', 'opsiKustom', 'paketKatering']);
            
        $keranjang = $cartsQuery->get();

        if ($keranjang->isEmpty()) {
            $existingOrder = Pesanan::where('user_id', $user->id)
                ->where('status', 'menunggu_pembayaran')
                ->where('status_pembayaran', 'belum_dibayar')
                ->latest()
                ->first();

            if ($existingOrder) {
                if (!$existingOrder->midtrans_snap_token) {
                    try {
                        $snapToken = PaymentService::createSnapToken($existingOrder);
                        $existingOrder->update(['midtrans_snap_token' => $snapToken]);
                    } catch (\Exception $e) {
                        \Log::error('Midtrans Snap Token Error on existingOrder (#' . $existingOrder->nomor_pesanan . '): ' . $e->getMessage(), [
                            'pesanan_id' => $existingOrder->id,
                            'exception' => $e
                        ]);
                        if ($request->expectsJson() || $request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Pesanan sudah ada (#' . $existingOrder->nomor_pesanan . '), namun gagal membuat token pembayaran Midtrans: ' . $e->getMessage(),
                                'pesanan_id' => $existingOrder->id,
                                'nomor_pesanan' => $existingOrder->nomor_pesanan,
                            ], 500);
                        }
                        return redirect()->route('customer.pesanan.show', $existingOrder)
                            ->with('error', 'Pesanan sudah ada, namun gagal membuat token pembayaran Midtrans: ' . $e->getMessage());
                    }
                }

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'pesanan_id' => $existingOrder->id,
                        'nomor_pesanan' => $existingOrder->nomor_pesanan,
                        'snap_token' => $existingOrder->midtrans_snap_token,
                        'redirect_url' => route('customer.pesanan.show', $existingOrder),
                    ]);
                }

                return redirect()->route('customer.pesanan.show', $existingOrder);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Keranjang belanja kosong atau tanggal tidak valid.'], 400);
            }
            return back()->with('error', 'Keranjang belanja kosong atau tanggal tidak valid.');
        }

        $validated = app(\App\Http\Requests\CheckoutRequest::class)->validated();
        
        $finalAddressDetail = $validated['detail_alamat'] ?? null;
        if (!empty($validated['osm_address']) && $finalAddressDetail) {
            $finalAddressDetail = $validated['osm_address'] . "\nDetail Patokan: " . $finalAddressDetail;
        } else if (!empty($validated['osm_address'])) {
            $finalAddressDetail = $validated['osm_address'];
        }

        // Tentukan catering service dan package
        $firstCart = $keranjang->first();
        $cateringServiceId = null;
        $packageId = null;

        if ($firstCart->produk) {
            $cateringServiceId = $firstCart->produk->layanan_katering_id;
        } elseif ($firstCart->opsiKustom) {
            $cateringServiceId = $firstCart->opsiKustom->layanan_katering_id;
        }

        // Cek apakah ada paket dalam keranjang
        $packageCart = $keranjang->firstWhere('item_type', 'package');
        if ($packageCart && $packageCart->paketKatering) {
            $packageId = $packageCart->catering_package_id;
            $cateringServiceId = $packageCart->paketKatering->layanan_katering_id;
        }

        // Validasi tanggal pemesanan berdasarkan jenis layanan
        OrderService::validateOrderDate($validated['tanggal_pesanan'], $cateringServiceId);

        $pesanan = DB::transaction(function () use ($validated, $user, $keranjang, $cateringServiceId, $packageId, $finalAddressDetail) {
            // Hitung biaya
            $subtotal = $keranjang->sum(fn ($keranjang) => $keranjang->subtotal);
            $ongkosKirim = $validated['metode_pengambilan'] === 'delivery'
                ? OngkosKirim::getCostByDistrict($validated['kecamatan_id'])
                : 0;
            $total = $subtotal + $ongkosKirim;

            // Buat pesanan
            $pesanan = Pesanan::create([
                'nomor_pesanan' => OrderService::generateOrderNumber(),
                'user_id' => $user->id,
                'layanan_katering_id' => $cateringServiceId,
                'paket_katering_id' => $packageId,
                'tanggal_pesanan' => $keranjang->min('menu_date') ? $keranjang->min('menu_date')->format('Y-m-d') : date('Y-m-d'),
                'metode_pengambilan' => $validated['metode_pengambilan'],
                'kecamatan_id' => $validated['kecamatan_id'] ?? null,
                'desa_id' => $validated['desa_id'] ?? null,
                'detail_alamat' => $finalAddressDetail,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'tipe_penyajian' => $validated['tipe_penyajian'] ?? null,
                'porsi' => $validated['porsi'] ?? null,
                'subtotal' => $subtotal,
                'ongkos_kirim' => $ongkosKirim,
                'total' => $total,
                'metode_pembayaran' => 'transfer',
                'status_pembayaran' => 'belum_dibayar',
                'status' => 'menunggu_pembayaran',
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // Buat pesanan items
            foreach ($keranjang as $keranjang) {
                // Skip package entry (hanya marker, bukan item fisik)
                if ($keranjang->item_type === 'package') {
                    continue;
                }

                // Tentukan harga snapshot
                $unitPrice = 0;
                $itemName = 'Item';

                if ($keranjang->produk) {
                    $unitPrice = (float) $keranjang->produk->harga;
                    $itemName = $keranjang->produk->name;
                } elseif ($keranjang->opsiKustom) {
                    // Package items & extras → harga 0 (sudah termasuk harga paket)
                    $unitPrice = in_array($keranjang->item_type, ['package_item', 'package_extra'])
                        ? 0
                        : (float) $keranjang->opsiKustom->harga;
                    $itemName = $keranjang->opsiKustom->name;
                }

                $extrasPrice = 0;
                if (!empty($keranjang->extras)) {
                    $extraIds = array_column($keranjang->extras, 'id');
                    $options = \App\Models\OpsiKustom::whereIn('id', $extraIds)->get()->keyBy('id');
                    $extraNames = [];
                    foreach ($keranjang->extras as $extraData) {
                        if ($opt = $options->get($extraData['id'])) {
                            $exPrice = (float) $opt->harga * $extraData['qty'];
                            $extrasPrice += $exPrice;
                            $extraNames[] = $opt->name . ' ' . $extraData['qty'] . 'x (+Rp' . number_format($exPrice, 0, ',', '.') . ')';
                        }
                    }
                    if (!empty($extraNames)) {
                        $itemName .= " (Extra: " . implode(', ', $extraNames) . ")";
                    }
                }
                
                // Append menu date specifically for daily items since they are checked out together
                if ($keranjang->menu_date) {
                    $itemName .= " [Kirim: " . $keranjang->menu_date->format('d M Y') . "]";
                }

                $itemSubtotal = ($unitPrice * $keranjang->jumlah) + $extrasPrice;

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $keranjang->produk_id,
                    'opsi_kustom_id' => $keranjang->opsi_kustom_id,
                    'item_name' => $itemName,
                    'jumlah' => $keranjang->jumlah,
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            // Buat tagihan
            InvoiceService::createInvoice($pesanan);

            // Hapus semua keranjang daily yang sudah dicheckout
            $user->keranjang()->whereNull('cart_group_id')->delete();

            return $pesanan;
        });

        // 2. Pesanan sudah berhasil disimpan dan di-commit di database. Minta Snap Token ke Midtrans.
        try {
            $snapToken = PaymentService::createSnapToken($pesanan);
            $pesanan->update(['midtrans_snap_token' => $snapToken]);
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Token Error on store (Pesanan #' . $pesanan->nomor_pesanan . '): ' . $e->getMessage(), [
                'pesanan_id' => $pesanan->id,
                'exception' => $e
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan berhasil disimpan (#' . $pesanan->nomor_pesanan . '), namun gagal membuat token pembayaran Midtrans: ' . $e->getMessage(),
                    'pesanan_id' => $pesanan->id,
                    'nomor_pesanan' => $pesanan->nomor_pesanan,
                ], 500);
            }

            return redirect()->route('customer.pesanan.show', $pesanan)
                ->with('error', 'Pesanan berhasil disimpan (#' . $pesanan->nomor_pesanan . '), namun gagal membuat token pembayaran Midtrans: ' . $e->getMessage());
        }

        // Kirim notifikasi pesanan dibuat
        NotificationService::notifyOrderCreated($pesanan);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'pesanan_id' => $pesanan->id,
                'nomor_pesanan' => $pesanan->nomor_pesanan,
                'snap_token' => $pesanan->midtrans_snap_token,
                'redirect_url' => route('customer.pesanan.show', $pesanan),
            ]);
        }

        return redirect()->route('customer.pesanan.show', $pesanan)
            ->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Tampilkan halaman checkout untuk event group (atau semua event group jika $groupId === 'all').
     */
    public function showEventCheckout(string $groupId)
    {
        $user = auth()->user();
        if ($user) {
            \App\Models\Keranjang::cleanupInvalidAndExpiredItems($user->id);
        }
        if ($groupId === 'all') {
            $groupItems = $user->keranjang()
                ->whereNotNull('cart_group_id')
                ->with(['opsiKustom', 'paketKatering', 'layananKatering', 'servingType'])
                ->get();
        } else {
            $groupItems = $user->keranjang()
                ->where('cart_group_id', $groupId)
                ->with(['opsiKustom', 'paketKatering', 'layananKatering', 'servingType'])
                ->get();
        }

        $existingOrder = Pesanan::where('user_id', $user->id)
            ->where('status', 'menunggu_pembayaran')
            ->where('status_pembayaran', 'belum_dibayar')
            ->latest()
            ->first();

        if ($groupItems->isEmpty() && !$existingOrder) {
            return redirect()->route('customer.keranjang', ['tab' => 'acara'])
                ->with('error', 'Pesanan event tidak ditemukan.');
        }

        $eventGroups = $groupItems->groupBy('cart_group_id');
        $packageItem = $groupItems->firstWhere('item_type', 'package');
        $menuItems = $groupItems->whereIn('item_type', ['package_item', 'custom_menu']);
        $additionItems = $groupItems->where('item_type', 'addition');
        $service = $groupItems->first()?->layananKatering;
        $servingType = $groupItems->first()?->servingType;
        $minDays = $service?->minimal_order_days ?? ($groupItems->max(fn ($item) => $item->layananKatering?->minimal_order_days) ?? 1);

        // Hitung subtotal
        $subtotal = $groupItems->sum(fn ($c) => $c->subtotal);
        if ($groupItems->isEmpty() && $existingOrder) {
            $subtotal = (float) $existingOrder->total;
        }

        $kecamatan = Kecamatan::with('desa')->get();

        return view('customer.acara_checkout', compact(
            'groupId', 'groupItems', 'eventGroups', 'packageItem', 'menuItems',
            'additionItems', 'service', 'servingType', 'subtotal', 'kecamatan', 'minDays', 'existingOrder'
        ));
    }

    /**
     * Checkout per event group atau seluruh event group sekaligus.
     */
    public function checkoutEventGroup(Request $request, string $groupId)
    {
        $user = auth()->user();
        if ($user) {
            \App\Models\Keranjang::cleanupInvalidAndExpiredItems($user->id);
        }
        if ($groupId === 'all') {
            $groupItems = $user->keranjang()
                ->whereNotNull('cart_group_id')
                ->with(['opsiKustom', 'paketKatering', 'layananKatering', 'servingType'])
                ->get();
        } else {
            $groupItems = $user->keranjang()
                ->where('cart_group_id', $groupId)
                ->with(['opsiKustom', 'paketKatering', 'layananKatering', 'servingType'])
                ->get();
        }

        if ($groupItems->isEmpty()) {
            $existingOrder = Pesanan::where('user_id', $user->id)
                ->where('status', 'menunggu_pembayaran')
                ->where('status_pembayaran', 'belum_dibayar')
                ->latest()
                ->first();

            if ($existingOrder) {
                if (!$existingOrder->midtrans_snap_token) {
                    try {
                        $snapToken = PaymentService::createSnapToken($existingOrder);
                        $existingOrder->update(['midtrans_snap_token' => $snapToken]);
                    } catch (\Exception $e) {
                        \Log::error('Midtrans Snap Token Error on existingEventOrder (#' . $existingOrder->nomor_pesanan . '): ' . $e->getMessage(), [
                            'pesanan_id' => $existingOrder->id,
                            'exception' => $e
                        ]);
                        if ($request->expectsJson() || $request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Pesanan event sudah ada (#' . $existingOrder->nomor_pesanan . '), namun gagal membuat token pembayaran Midtrans: ' . $e->getMessage(),
                                'pesanan_id' => $existingOrder->id,
                                'nomor_pesanan' => $existingOrder->nomor_pesanan,
                            ], 500);
                        }
                        return redirect()->route('customer.pesanan.show', $existingOrder)
                            ->with('error', 'Pesanan event sudah ada, namun gagal membuat token pembayaran Midtrans: ' . $e->getMessage());
                    }
                }

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'pesanan_id' => $existingOrder->id,
                        'nomor_pesanan' => $existingOrder->nomor_pesanan,
                        'snap_token' => $existingOrder->midtrans_snap_token,
                        'redirect_url' => route('customer.pesanan.show', $existingOrder),
                    ]);
                }

                return redirect()->route('customer.pesanan.show', $existingOrder);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Pesanan event tidak ditemukan atau sudah diproses.'], 400);
            }
            return back()->with('error', 'Pesanan event tidak ditemukan.');
        }

        $validated = $request->validate([
            'tanggal_pesanan' => 'required|date|after:today',
            'metode_pengambilan' => 'required|in:delivery,pickup',
            'kecamatan_id' => 'required_if:metode_pengambilan,delivery|nullable|exists:kecamatan,id',
            'desa_id' => 'nullable|exists:desa,id',
            'detail_alamat' => 'nullable|string',
            'osm_address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'metode_pembayaran' => 'required|in:transfer',
            'catatan' => 'nullable|string',
        ]);

        if ($validated['metode_pengambilan'] === 'delivery') {
            $locValidation = \App\Services\LocationService::validateLocation(
                $validated['latitude'] ?? null,
                $validated['longitude'] ?? null,
                $validated['kecamatan_id'] ?? null,
                null,
                $validated['osm_address'] ?? null
            );

            if (!$locValidation['is_in_pontianak']) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $locValidation['message'] ?? 'Lokasi berada di luar wilayah Pontianak.'
                    ], 422);
                }
                return back()->with('error', $locValidation['message'] ?? 'Lokasi berada di luar wilayah Pontianak.');
            }
        }

        $finalAddressDetail = $validated['detail_alamat'] ?? null;
        if (!empty($validated['osm_address']) && $finalAddressDetail) {
            $finalAddressDetail = $validated['osm_address'] . "\nDetail Patokan: " . $finalAddressDetail;
        } else if (!empty($validated['osm_address'])) {
            $finalAddressDetail = $validated['osm_address'];
        }

        // Tentukan catering service dan package
        $cateringServiceId = $groupItems->first()->layanan_katering_id;
        $packageItem = $groupItems->firstWhere('item_type', 'package');
        $packageId = $packageItem?->catering_package_id;

        // Validasi H-3
        OrderService::validateOrderDate($validated['tanggal_pesanan'], $cateringServiceId);

        // Hitung total porsi & validasi batas maksimal custom menu
        $menuItems = $groupItems->whereIn('item_type', ['package_item', 'custom_menu']);
        $totalPortions = 0;
        foreach ($groupItems->groupBy('cart_group_id') as $gId => $gItems) {
            $pkg = $gItems->firstWhere('item_type', 'package');
            if ($pkg && $pkg->paketKatering) {
                $totalPortions += $pkg->paketKatering->total_portions * $pkg->jumlah;
            } else {
                $totalPortions += $gItems->whereIn('item_type', ['package_item', 'custom_menu'])->sum('jumlah');
            }
        }

        $customMenuItems = $groupItems->where('item_type', 'custom_menu');
        if ($customMenuItems->isNotEmpty()) {
            $service = $groupItems->first()->layananKatering;
            $customPortions = $customMenuItems->sum('jumlah');
            if ($service && $service->maksimal_porsi && $customPortions > $service->maksimal_porsi) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Total porsi melebihi batas maksimal ({$service->maksimal_porsi} porsi)."
                    ], 422);
                }
                return back()->with('error', "Total porsi melebihi batas maksimal ({$service->maksimal_porsi} porsi).");
            }
        }

        // Hitung penyajian
        $servingType = $groupItems->first()->servingType;

        $pesanan = DB::transaction(function () use ($request, $validated, $user, $groupItems, $groupId, $cateringServiceId, $packageId, $packageItem, $totalPortions, $servingType, $finalAddressDetail) {
            // Hitung subtotal
            $subtotal = $groupItems->sum(fn ($c) => $c->subtotal);
            $ongkosKirim = $validated['metode_pengambilan'] === 'delivery'
                ? OngkosKirim::getCostByDistrict($validated['kecamatan_id'])
                : 0;
            $total = $subtotal + $ongkosKirim;

            // Buat pesanan
            $pesanan = Pesanan::create([
                'nomor_pesanan' => OrderService::generateOrderNumber(),
                'user_id' => $user->id,
                'layanan_katering_id' => $cateringServiceId,
                'paket_katering_id' => $packageId,
                'tanggal_pesanan' => $validated['tanggal_pesanan'],
                'metode_pengambilan' => $validated['metode_pengambilan'],
                'kecamatan_id' => $validated['kecamatan_id'] ?? null,
                'desa_id' => $validated['desa_id'] ?? null,
                'detail_alamat' => $finalAddressDetail,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'tipe_penyajian' => $servingType?->name,
                'porsi' => $totalPortions,
                'subtotal' => $subtotal,
                'ongkos_kirim' => $ongkosKirim,
                'total' => $total,
                'metode_pembayaran' => 'transfer',
                'status_pembayaran' => 'belum_dibayar',
                'status' => 'menunggu_pembayaran',
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // Buat DetailPesanan
            foreach ($groupItems as $keranjang) {
                // Skip package marker / custom_header marker
                if ($keranjang->item_type === 'package' || $keranjang->item_type === 'custom_header') {
                    if ($keranjang->item_type === 'package') {
                        // Simpan sebagai DetailPesanan paket header
                        DetailPesanan::create([
                            'pesanan_id' => $pesanan->id,
                            'item_name' => 'Paket: ' . ($keranjang->paketKatering?->name ?? 'Paket'),
                            'jumlah' => $keranjang->jumlah,
                            'unit_price' => (float) ($keranjang->paketKatering?->harga ?? 0),
                            'subtotal' => (float) ($keranjang->paketKatering?->harga ?? 0) * $keranjang->jumlah,
                        ]);
                    }
                    continue;
                }

                $unitPrice = 0;
                $itemName = $keranjang->opsiKustom?->name ?? 'Item';

                if ($keranjang->item_type === 'package_item') {
                    // Termasuk dalam paket, harga 0
                    $unitPrice = 0;
                    $itemName = 'Menu: ' . $itemName;
                } elseif ($keranjang->item_type === 'package_extra') {
                    // Termasuk dalam paket, harga 0
                    $unitPrice = 0;
                    $itemName = 'Extra: ' . $itemName;
                } elseif ($keranjang->item_type === 'custom_menu') {
                    $unitPrice = (float) ($keranjang->opsiKustom?->harga ?? 0);
                    $itemName = 'Menu: ' . $itemName;
                } elseif ($keranjang->item_type === 'addition') {
                    $unitPrice = (float) ($keranjang->opsiKustom?->harga ?? 0);
                    $itemName = 'Extra: ' . $itemName;
                }

                $itemSubtotal = $unitPrice * $keranjang->jumlah;

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'opsi_kustom_id' => $keranjang->opsi_kustom_id,
                    'item_name' => $itemName,
                    'jumlah' => $keranjang->jumlah,
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            // Simpan penyajian sebagai DetailPesanan
            if ($servingType) {
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'opsi_kustom_id' => $servingType->id,
                    'item_name' => 'Penyajian: ' . $servingType->name,
                    'jumlah' => $totalPortions,
                    'unit_price' => 0,
                    'subtotal' => 0,
                ]);
            }

            // Buat tagihan
            InvoiceService::createInvoice($pesanan);

            // Hapus keranjang group setelah pesanan dibuat
            if ($groupId === 'all') {
                $user->keranjang()->whereNotNull('cart_group_id')->delete();
            } else {
                $user->keranjang()->where('cart_group_id', $groupId)->delete();
            }

            return $pesanan;
        });

        // 2. Pesanan sudah berhasil disimpan dan di-commit di database. Minta Snap Token ke Midtrans.
        try {
            $snapToken = PaymentService::createSnapToken($pesanan);
            $pesanan->update(['midtrans_snap_token' => $snapToken]);
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Token Error on checkoutEventGroup (Pesanan #' . $pesanan->nomor_pesanan . '): ' . $e->getMessage(), [
                'pesanan_id' => $pesanan->id,
                'exception' => $e
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan event berhasil disimpan (#' . $pesanan->nomor_pesanan . '), namun gagal membuat token pembayaran Midtrans: ' . $e->getMessage(),
                    'pesanan_id' => $pesanan->id,
                    'nomor_pesanan' => $pesanan->nomor_pesanan,
                ], 500);
            }

            return redirect()->route('customer.pesanan.show', $pesanan)
                ->with('error', 'Pesanan event berhasil disimpan (#' . $pesanan->nomor_pesanan . '), namun gagal membuat token pembayaran Midtrans: ' . $e->getMessage());
        }

        // Kirim notifikasi
        NotificationService::notifyOrderCreated($pesanan);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'pesanan_id' => $pesanan->id,
                'nomor_pesanan' => $pesanan->nomor_pesanan,
                'snap_token' => $pesanan->midtrans_snap_token,
                'redirect_url' => route('customer.pesanan.show', $pesanan),
            ]);
        }

        return redirect()->route('customer.pesanan.show', $pesanan)
            ->with('success', 'Pesanan event berhasil dibuat! Silakan lakukan pembayaran.');
    }
}
