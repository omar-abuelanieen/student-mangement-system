<?php

namespace App\Services\Payments;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\OutboxMessage;
use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\DTOs\PaymentInstruction;
use App\Services\Payments\DTOs\WebhookResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class PaymentService
{
    public function __construct(private PaymentGateway $gateway) {}

    public function initiate(Invoice $invoice): PaymentInstruction
    {
        if ($invoice->status !== InvoiceStatus::Pending) {
            throw ValidationException::withMessages([
                'invoice' => 'This invoice is not payable.',
            ]);
        }

        $payment = Payment::create([
            'invoice_id'   => $invoice->id,
            'gateway'      => 'paymob',
            'reference'    => (string) Str::uuid(),
            'amount_cents' => $invoice->amount_cents,
            'status'       => PaymentStatus::Pending,
        ]);

        try {
            return $this->gateway->createPayment($payment, $invoice);
        } catch (Throwable $e) {
            $payment->update(['status' => PaymentStatus::Failed]);

            throw $e;
        }
    }

   public function handleWebhook(WebhookResult $result): void
{
    DB::transaction(function () use ($result) {
        $payment = Payment::where('reference', $result->reference)
            ->lockForUpdate()
            ->first();

        if (! $payment) {
            return;
        }

        // idempotent: اتعالج قبل كده
        if ($payment->status !== PaymentStatus::Pending) {
            return;
        }

        if (! $result->success) {
            $payment->update([
                'status'      => PaymentStatus::Failed,
                'raw_payload' => $result->raw,
            ]);

            return;
        }

        if ($result->amountCents !== $payment->amount_cents) {
            $payment->update([
                'status'      => PaymentStatus::Failed,
                'raw_payload' => $result->raw,
            ]);

            report(new RuntimeException("Amount mismatch for payment {$payment->id}"));

            return;
        }

        // نقفل الفاتورة عشان لو جه webhookين مع بعض ما يعدّوش الاتنين
        $invoice = Invoice::lockForUpdate()->find($payment->invoice_id);

        // الفاتورة اتدفعت قبل كده: ده دفع مزدوج
        if ($invoice->status === InvoiceStatus::Paid) {
            $payment->update([
                'status'                 => PaymentStatus::Duplicate,
                'gateway_transaction_id' => $result->transactionId,
                'raw_payload'            => $result->raw,
                'paid_at'                => now(),
            ]);

            report(new RuntimeException(
                "Duplicate payment {$payment->id} for already paid invoice {$invoice->id}. Needs refund."
            ));

            return;
        }

        $payment->update([
            'status'                 => PaymentStatus::Success,
            'gateway_transaction_id' => $result->transactionId,
            'raw_payload'            => $result->raw,
            'paid_at'                => now(),
        ]);

        $invoice->update(['status' => InvoiceStatus::Paid]);

        OutboxMessage::create([
            'type'    => 'payment.succeeded',
            'payload' => ['payment_id' => $payment->id],
             'available_at' => now(),
        ]);
    });
}
}
