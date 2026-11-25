<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {

    return $user->hasRole('admin') || $this->isOwnerGuardian($user, $invoice);

    }

    public function pay(User $user, Invoice $invoice): bool
    {
        return $this->isOwnerGuardian($user, $invoice);
    }

    private function isOwnerGuardian(User $user, Invoice $invoice): bool
    {
        return $user->guardianProfile !== null
            && $invoice->guardian_profile_id === $user->guardianProfile->id;
    }
}
