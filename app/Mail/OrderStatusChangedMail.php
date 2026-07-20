<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Pesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pesanan $pesanan
    ) {}

    public function envelope(): Envelope
    {
        $statusLabels = [
            'diproses' => 'Diproses',
            'dikirim' => 'Sedang Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $statusLabel = $statusLabels[$this->pesanan->status] ?? $this->pesanan->status;

        return new Envelope(
            subject: "Update Pesanan {$this->pesanan->nomor_pesanan} — {$statusLabel}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pesanan-status-changed',
            with: [
                'pesanan' => $this->pesanan,
                'user' => $this->pesanan->user,
            ],
        );
    }
}
