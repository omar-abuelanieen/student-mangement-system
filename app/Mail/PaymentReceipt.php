<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentReceipt extends Mailable
{
    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Payment receipt');
    }

    public function content(): Content
    {
        $invoice = $this->payment->invoice;
        $amount  = number_format($this->payment->amount_cents / 100, 2);

        $html = "<h2>Payment received</h2>"
              . "<p>Invoice: " . e($invoice->title) . "</p>"
              . "<p>Amount: {$amount} {$invoice->currency}</p>"
              . "<p>Reference: " . e($this->payment->reference) . "</p>";

        return new Content(htmlString: $html);
    }
}
