<?php

namespace App\Services\Payments\DTOs;

final class WebhookResult
{
    public function __construct(
        public readonly string $reference,
        public readonly string $transactionId,
        public readonly bool $success,
        public readonly int $amountCents,
        public readonly array $raw,
    ) {}
}
