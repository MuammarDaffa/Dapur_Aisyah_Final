<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pesanan Berhasil Dibuat — ' . $this->order->order_number)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pesanan Anda telah berhasil dibuat.')
            ->line('**Nomor Pesanan:** ' . $this->order->order_number)
            ->line('**Total:** Rp ' . number_format($this->order->total, 0, ',', '.'))
            ->line('**Status:** ' . $this->order->status_label)
            ->action('Lihat Pesanan', url('/dashboard/orders/' . $this->order->id))
            ->line('Terima kasih telah memesan di Dapur Aisyah!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_created',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'message' => 'Pesanan #' . $this->order->order_number . ' berhasil dibuat dan sedang diproses.',
        ];
    }
}
