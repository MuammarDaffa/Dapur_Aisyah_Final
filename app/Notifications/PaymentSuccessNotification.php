<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Pesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Pesanan $pesanan
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembayaran Berhasil — ' . $this->pesanan->nomor_pesanan)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pembayaran untuk pesanan Anda telah berhasil dikonfirmasi.')
            ->line('**Nomor Pesanan:** ' . $this->pesanan->nomor_pesanan)
            ->line('**Total:** Rp ' . number_format($this->pesanan->total, 0, ',', '.'))
            ->line('**Metode:** ' . ucfirst($this->pesanan->metode_pembayaran))
            ->action('Lihat Pesanan', url('/dashboard/pesanan/' . $this->pesanan->id))
            ->line('Pesanan Anda sedang diproses oleh tim kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_success',
            'pesanan_id' => $this->pesanan->id,
            'nomor_pesanan' => $this->pesanan->nomor_pesanan,
            'total' => $this->pesanan->total,
            'message' => 'Pembayaran untuk pesanan #' . $this->pesanan->nomor_pesanan . ' telah dikonfirmasi.',
        ];
    }
}
