<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id', 'gateway', 'reference', 'gateway_transaction_id',
        'amount_cents', 'status', 'raw_payload', 'paid_at', 'receipt_sent_at',
    ];

    protected $casts = [
        'status'          => PaymentStatus::class,
        'raw_payload'     => 'array',
        'paid_at'         => 'datetime',
        'receipt_sent_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
