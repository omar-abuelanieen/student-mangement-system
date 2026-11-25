<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\Payments\PaymentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Models\Payment;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private PaymentService $payments) {}


    public function store(Invoice $invoice): JsonResponse
    {
$this->authorize('pay', $invoice);

        $instruction = $this->payments->initiate($invoice);

        return response()->json([
            'type'        => $instruction->type,
            'payment_url' => $instruction->value,
        ]);
    }
    public function show(Payment $payment): JsonResponse
{
    $this->authorize('view', $payment->invoice);

    return response()->json([
        'id'             => $payment->id,
        'reference'      => $payment->reference,
        'status'         => $payment->status->value,
        'amount_cents'   => $payment->amount_cents,
        'currency'       => $payment->invoice->currency,
        'paid_at'        => $payment->paid_at,
        'invoice_id'     => $payment->invoice_id,
        'invoice_status' => $payment->invoice->status->value,
    ]);
}
}
