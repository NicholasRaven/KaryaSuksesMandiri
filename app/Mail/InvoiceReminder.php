<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $customerName;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, string $customerName)
    {
        $this->invoice = $invoice;
        $this->customerName = $customerName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat Pembayaran Invoice: ' . $this->invoice->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-reminder',
            with: [
                'invoice' => $this->invoice,
                'customerName' => $this->customerName,
                'due_date' => \Carbon\Carbon::parse($this->invoice->due_date)->translatedFormat('d F Y'),
                'total_amount' => number_format($this->invoice->total_amount, 2, ',', '.'),
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