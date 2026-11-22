<?php

namespace App\Rules;

use App\Models\Attendance;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class StudentsNotAlreadyMarked implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $records = $this->data['records'] ?? [];

        if (! is_array($records)) {
            return;
        }

        $ids = collect($records)->pluck('student_profile_id')->filter()->unique();

        if ($ids->isEmpty()) {
            return;
        }

        if (Attendance::where('date', $value)->whereIn('student_profile_id', $ids)->exists()) {
            $fail('Attendance was already recorded for one or more of these students on this date.');
        }
    }
}
