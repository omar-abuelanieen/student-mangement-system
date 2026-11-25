<?php

namespace App\Services\Payments\Contracts;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payments\DTOs\PaymentInstruction;
use App\Services\Payments\DTOs\WebhookResult;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function createPayment(Payment $payment, Invoice $invoice): PaymentInstruction;

    public function parseWebhook(Request $request): WebhookResult;
}
