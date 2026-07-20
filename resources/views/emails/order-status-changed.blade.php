<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Status Pesanan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .header p { color: rgba(255,255,255,0.9); margin: 8px 0 0; font-size: 14px; }
        .content { padding: 30px; }
        .greeting { font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-weight: 600; font-size: 14px; margin: 8px 0; }
        .status-processing { background: #dbeafe; color: #1d4ed8; }
        .status-on_delivery { background: #ede9fe; color: #6d28d9; }
        .status-completed { background: #d1fae5; color: #047857; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }
        .info-box { background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; font-size: 14px; }
        .info-value { color: #1f2937; font-weight: 600; font-size: 14px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; margin: 16px 0; }
        .footer { background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍲 Dapur Aisyah</h1>
            <p>Update Status Pesanan</p>
        </div>
        <div class="content">
            <p class="greeting">Halo, {{ $user->name }}!</p>

            @php
                $statusLabels = [
                    'diproses' => 'Diproses',
                    'dikirim' => 'Sedang Dikirim',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ];
                $statusMessages = [
                    'diproses' => 'Pesanan Anda sedang diproses oleh dapur kami.',
                    'dikirim' => 'Pesanan Anda sedang dalam perjalanan ke lokasi Anda.',
                    'selesai' => 'Pesanan Anda telah selesai. Terima kasih telah memesan!',
                    'dibatalkan' => 'Pesanan Anda telah dibatalkan.',
                ];
                $statusLabel = $statusLabels[$pesanan->status] ?? $pesanan->status;
                $statusMessage = $statusMessages[$pesanan->status] ?? 'Status pesanan Anda telah diperbarui.';
            @endphp

            <p style="color: #4b5563; line-height: 1.6;">{{ $statusMessage }}</p>

            <div style="text-align: center; margin: 20px 0;">
                <span class="status-badge status-{{ $pesanan->status }}">
                    {{ $statusLabel }}
                </span>
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Nomor Pesanan</span>
                    <span class="info-value">{{ $pesanan->nomor_pesanan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total</span>
                    <span class="info-value">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/dashboard/pesanan/' . $pesanan->id) }}" class="btn">Lihat Detail Pesanan</a>
            </div>

            @if($pesanan->status === 'selesai')
                <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin-top: 16px; text-align: center;">
                    ⭐ Jangan lupa berikan ulasan untuk pesanan Anda!
                </p>
            @endif
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Dapur Aisyah. Semua hak dilindungi.</p>
            <p>Email ini dikirim otomatis, mohon jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html>
