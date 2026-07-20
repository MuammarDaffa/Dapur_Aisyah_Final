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

class PaymentSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pesanan $pesanan
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Berhasil — ' . $this->pesanan->nomor_pesanan,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
            with: [
                'pesanan' => $this->pesanan,
                'user' => $this->pesanan->user,
            ],
        );
    }
}
