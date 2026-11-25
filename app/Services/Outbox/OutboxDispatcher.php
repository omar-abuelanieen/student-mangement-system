<?php

namespace App\Services\Outbox;

use App\Models\OutboxMessage;
use App\Services\Outbox\Handlers\SendPaymentReceipt;
use RuntimeException;

class OutboxDispatcher
{
    public function handle(OutboxMessage $message): void
    {
        match ($message->type) {
            'payment.succeeded' => app(SendPaymentReceipt::class)
                ->handle((int) $message->payload['payment_id']),
            default => throw new RuntimeException("Unknown outbox type: {$message->type}"),
        };
    }
}
