<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DownloadLinkMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order) // php, Constructor krijgt hele order mee
    {
        $this->order = $order;
    }

    /**
     * Definieert de "envelope" van de mail, zoals het onderwerp.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Je Download Links - Bestelling #' . $this->order->id,
        );
    }

    /**
     * Definieert de inhoud van de mail: welke view en welke data wordt meegegeven.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.download-links',
            with: [
                'order' => $this->order,
                'downloadableItems' => $this->order->items->filter(function ($item) {
                    return $item->product && $item->product->download_link;
                }),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
