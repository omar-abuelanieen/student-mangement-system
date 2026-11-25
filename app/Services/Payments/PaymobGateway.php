<?php

namespace App\Services\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\DTOs\PaymentInstruction;
use App\Services\Payments\DTOs\WebhookResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymobGateway implements PaymentGateway
{
    private function baseUrl(): string
    {
        return rtrim(config('paymob.base_url', 'https://accept.paymob.com'), '/');
    }

 public function createPayment(Payment $payment, Invoice $invoice): PaymentInstruction
{
    $guardian = $invoice->guardian;
    $user = $guardian?->user;

    $firstName = $user?->first_name ?: 'Guardian';
    $lastName  = $user?->last_name ?: 'NA';

    $payload = array_filter([
        'amount'          => $payment->amount_cents,
        'currency'        => config('paymob.currency'),
        'payment_methods' => config('paymob.integration_ids'),
        'items' => [[
            'name'     => $invoice->title,
            'amount'   => $payment->amount_cents,
            'quantity' => 1,
        ]],
        'billing_data' => [
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'email'        => $user?->email ?? 'noreply@example.com',
            'phone_number' => $guardian?->phone ?? '01000000000',
        ],
        'special_reference' => $payment->reference,
        'notification_url'  => config('paymob.notification_url'),
        'redirection_url'   => config('paymob.redirection_url'),
    ], fn ($value) => $value !== null);

    $response = Http::withHeaders([
            'Authorization' => 'Token ' . config('paymob.secret_key'),
        ])
        ->acceptJson()
        ->asJson()
        ->timeout(15)
        ->post($this->baseUrl() . '/v1/intention/', $payload);

    if ($response->failed()) {
        report(new RuntimeException(
            'Paymob intention failed [' . $response->status() . ']: ' . $response->body()
        ));

        throw new RuntimeException('Unable to start payment.');
    }

    $clientSecret = $response->json('client_secret');

    if (! $clientSecret) {
        throw new RuntimeException('Paymob did not return a client secret.');
    }

    $url = $this->baseUrl() . '/unifiedcheckout/?' . http_build_query([
        'publicKey'    => config('paymob.public_key'),
        'clientSecret' => $clientSecret,
    ]);

    return new PaymentInstruction('redirect', $url);
}

  public function parseWebhook(Request $request): WebhookResult
{
    $obj = $request->input('obj');

    if (! is_array($obj)) {
        throw new RuntimeException('Invalid Paymob payload.');
    }

    $received = strtolower((string) $request->query('hmac', ''));

    if (! hash_equals($this->calculateHmac($obj), $received)) {
        throw new RuntimeException('Invalid Paymob HMAC.');
    }

    $reference = data_get($obj, 'order.merchant_order_id');

    if (! $reference) {
        throw new RuntimeException('Missing merchant order id.');
    }

    return new WebhookResult(
        reference:     (string) $reference,
        transactionId: (string) data_get($obj, 'id'),
        success:       $this->toBool(data_get($obj, 'success'))
                       && ! $this->toBool(data_get($obj, 'pending')),
        amountCents:   (int) data_get($obj, 'amount_cents'),
        raw:           $obj,
    );
}

private function calculateHmac(array $obj): string
{
    $paths = [
        'amount_cents', 'created_at', 'currency', 'error_occured',
        'has_parent_transaction', 'id', 'integration_id', 'is_3d_secure',
        'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment',
        'is_voided', 'order.id', 'owner', 'pending',
        'source_data.pan', 'source_data.sub_type', 'source_data.type', 'success',
    ];

    $string = '';

    foreach ($paths as $path) {
        $value = data_get($obj, $path);
        $string .= is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
    }

    return hash_hmac('sha512', $string, (string) config('paymob.hmac_secret'));
}

private function toBool(mixed $value): bool
{
    return $value === true || $value === 'true' || $value === 1 || $value === '1';
}
}
