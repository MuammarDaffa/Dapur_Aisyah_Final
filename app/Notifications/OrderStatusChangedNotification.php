<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
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
        $statusLabels = [
            'pending_payment' => 'Menunggu Pembayaran',
            'processing' => 'Diproses',
            'on_delivery' => 'Sedang Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $statusLabel = $statusLabels[$this->order->status] ?? $this->order->status;
        $message = $this->getStatusMessage();

        $mail = (new MailMessage)
            ->subject('Update Pesanan — ' . $this->order->order_number)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line($message)
            ->line('**Nomor Pesanan:** ' . $this->order->order_number)
            ->line('**Status:** ' . $statusLabel)
            ->action('Lihat Pesanan', url('/dashboard/orders/' . $this->order->id));

        if ($this->order->status === 'completed') {
            $mail->line('Terima kasih telah memesan di Dapur Aisyah! Jangan lupa berikan ulasan.');
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'status_changed',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'message' => $this->getStatusMessage(),
        ];
    }

    protected function getStatusMessage(): string
    {
        return match ($this->order->status) {
            'processing' => 'Pesanan #' . $this->order->order_number . ' sedang diproses oleh dapur kami.',
            'on_delivery' => 'Pesanan #' . $this->order->order_number . ' sedang dalam perjalanan ke lokasi Anda.',
            'completed' => 'Pesanan #' . $this->order->order_number . ' telah selesai. Terima kasih!',
            'cancelled' => 'Pesanan #' . $this->order->order_number . ' telah dibatalkan.',
            default => 'Status pesanan #' . $this->order->order_number . ' telah diperbarui.',
        };
    }
}
