<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembayaran Berhasil — ' . $this->order->order_number)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pembayaran untuk pesanan Anda telah berhasil dikonfirmasi.')
            ->line('**Nomor Pesanan:** ' . $this->order->order_number)
            ->line('**Total:** Rp ' . number_format($this->order->total, 0, ',', '.'))
            ->line('**Metode:** ' . ucfirst($this->order->payment_method))
            ->action('Lihat Pesanan', url('/dashboard/orders/' . $this->order->id))
            ->line('Pesanan Anda sedang diproses oleh tim kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_success',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'message' => 'Pembayaran untuk pesanan #' . $this->order->order_number . ' telah dikonfirmasi.',
        ];
    }
}
