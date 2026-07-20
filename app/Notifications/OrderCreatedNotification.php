<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Pesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
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
            ->subject('Pesanan Berhasil Dibuat — ' . $this->pesanan->nomor_pesanan)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pesanan Anda telah berhasil dibuat.')
            ->line('**Nomor Pesanan:** ' . $this->pesanan->nomor_pesanan)
            ->line('**Total:** Rp ' . number_format($this->pesanan->total, 0, ',', '.'))
            ->line('**Status:** ' . $this->pesanan->status_label)
            ->action('Lihat Pesanan', url('/dashboard/pesanan/' . $this->pesanan->id))
            ->line('Terima kasih telah memesan di Dapur Aisyah!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_created',
            'pesanan_id' => $this->pesanan->id,
            'nomor_pesanan' => $this->pesanan->nomor_pesanan,
            'total' => $this->pesanan->total,
            'message' => 'Pesanan #' . $this->pesanan->nomor_pesanan . ' berhasil dibuat dan sedang diproses.',
        ];
    }
}
