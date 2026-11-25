<?php

namespace App\Services\Payments\DTOs;

final class PaymentInstruction
{
    public function __construct(
        public readonly string $type,
        public readonly string $value,
    ) {}
}
