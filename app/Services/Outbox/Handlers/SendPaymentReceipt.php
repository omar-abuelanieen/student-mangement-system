<?php

namespace App\Services\Outbox\Handlers;

use App\Mail\PaymentReceipt;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceipt
{
    public function handle(int $paymentId): void
    {
        $payment = Payment::with('invoice.guardian.user')->findOrFail($paymentId);

        if ($payment->receipt_sent_at) {
            return;
        }

        $email = $payment->invoice->guardian?->user?->email;

        if (! $email) {
            Log::warning("No guardian email for payment {$payment->id}");

            return;
        }

        Mail::to($email)->send(new PaymentReceipt($payment));

        $payment->update(['receipt_sent_at' => now()]);
    }
}
