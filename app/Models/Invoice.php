<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'student_profile_id', 'guardian_profile_id', 'title',
        'amount_cents', 'currency', 'status', 'due_date',
    ];

    protected $casts = [
        'status'   => InvoiceStatus::class,
        'due_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(GuardianProfile::class, 'guardian_profile_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
