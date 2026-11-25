<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'student_profile_id'  => $this->student_profile_id,
            'guardian_profile_id' => $this->guardian_profile_id,
            'title'               => $this->title,
            'amount'              => number_format($this->amount_cents / 100, 2, '.', ''),
            'amount_cents'        => $this->amount_cents,
            'currency'            => $this->currency,
            'status'              => $this->status->value,
            'due_date'            => $this->due_date?->toDateString(),
            'created_at'          => $this->created_at,
        ];
    }
}
