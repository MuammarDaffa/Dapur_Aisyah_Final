<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Pesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
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
        $statusLabels = [
            'diproses' => 'Diproses',
            'dikirim' => 'Sedang Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $statusLabel = $statusLabels[$this->pesanan->status] ?? $this->pesanan->status;
        $message = $this->getStatusMessage();

        $mail = (new MailMessage)
            ->subject('Update Pesanan — ' . $this->pesanan->nomor_pesanan)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line($message)
            ->line('**Nomor Pesanan:** ' . $this->pesanan->nomor_pesanan)
            ->line('**Status:** ' . $statusLabel)
            ->action('Lihat Pesanan', url('/dashboard/pesanan/' . $this->pesanan->id));

        if ($this->pesanan->status === 'selesai') {
            $mail->line('Terima kasih telah memesan di Dapur Aisyah! Jangan lupa berikan ulasan.');
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'status_changed',
            'pesanan_id' => $this->pesanan->id,
            'nomor_pesanan' => $this->pesanan->nomor_pesanan,
            'status' => $this->pesanan->status,
            'message' => $this->getStatusMessage(),
        ];
    }

    protected function getStatusMessage(): string
    {
        return match ($this->pesanan->status) {
            'diproses' => 'Pesanan #' . $this->pesanan->nomor_pesanan . ' sedang diproses oleh dapur kami.',
            'dikirim' => 'Pesanan #' . $this->pesanan->nomor_pesanan . ' sedang dalam perjalanan ke lokasi Anda.',
            'selesai' => 'Pesanan #' . $this->pesanan->nomor_pesanan . ' telah selesai. Terima kasih!',
            'dibatalkan' => 'Pesanan #' . $this->pesanan->nomor_pesanan . ' telah dibatalkan.',
            default => 'Status pesanan #' . $this->pesanan->nomor_pesanan . ' telah diperbarui.',
        };
    }
}
