<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\GuardianProfile;
use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        return Invoice::create([
            'student_profile_id'  => $data['student_profile_id'],
            'guardian_profile_id' => $data['guardian_profile_id'],
            'title'               => $data['title'],
            'amount_cents'        => $this->toCents($data['amount']),
            'currency'            => 'EGP',
            'status'              => InvoiceStatus::Pending,
            'due_date'            => $data['due_date'] ?? null,
        ]);
    }

    public function paginateForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::latest()->paginate($perPage);
    }

    public function paginateForGuardian(GuardianProfile $guardian, int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::where('guardian_profile_id', $guardian->id)
            ->latest()
            ->paginate($perPage);
    }

    public function cancel(Invoice $invoice): Invoice
    {
        if ($invoice->status !== InvoiceStatus::Pending) {
            throw ValidationException::withMessages([
                'invoice' => 'Only pending invoices can be cancelled.',
            ]);
        }

        $invoice->update(['status' => InvoiceStatus::Cancelled]);

        return $invoice;
    }

    private function toCents(string|int|float $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', (string) $amount, 2), 2, '0');

        return ((int) $whole) * 100 + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }
}
