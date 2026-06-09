<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public User $sender,
    ) {}

    public function envelope(): Envelope
    {
        $companyName = $this->sender->company_name ?? $this->sender->name;

        return new Envelope(
            subject: "Factuur {$this->invoice->invoice_number} — {$companyName}",
            replyTo: [$this->sender->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    public function attachments(): array
    {
        $this->invoice->load(['lineItems', 'client']);

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
            'user' => $this->sender,
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "factuur-{$this->invoice->invoice_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
