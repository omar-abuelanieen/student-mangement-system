<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboxMessage extends Model
{
    protected $fillable = [
        'type', 'payload', 'status', 'attempts',
        'last_error', 'available_at', 'processed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'available_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}
