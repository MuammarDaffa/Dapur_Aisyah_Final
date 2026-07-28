<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konfirmasi Pesanan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #f97316, #f59e0b); padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .header p { color: rgba(255,255,255,0.9); margin: 8px 0 0; font-size: 14px; }
        .content { padding: 30px; }
        .greeting { font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; }
        .info-box { background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; font-size: 14px; }
        .info-value { color: #1f2937; font-weight: 600; font-size: 14px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #f97316, #f59e0b); color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; margin: 16px 0; }
        .footer { background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍲 Dapur Aisyah</h1>
            <p>Pesanan Berhasil Dibuat</p>
        </div>
        <div class="content">
            <p class="greeting">Halo, {{ $user->name }}!</p>
            <p style="color: #4b5563; line-height: 1.6;">Pesanan Anda telah berhasil dibuat. Berikut detail pesanan Anda:</p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Nomor Pesanan</span>
                    <span class="info-value">{{ $pesanan->nomor_pesanan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total</span>
                    <span class="info-value">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value">{{ $pesanan->status_label }}</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/dashboard/pesanan/' . $pesanan->id) }}" class="btn">Lihat Pesanan</a>
            </div>

            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin-top: 16px;">
                Terima kasih telah memesan di Dapur Aisyah! Kami akan segera memproses pesanan Anda setelah pembayaran dikonfirmasi.
            </p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Dapur Aisyah. Semua hak dilindungi.</p>
            <p>Email ini dikirim otomatis, mohon jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html>
