<?php

namespace App\Rules;

use App\Models\StudentProfile;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class StudentsBelongToClassroom implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $classroomId = $this->data['classroom_id'] ?? null;
        $ids = collect($value)->pluck('student_profile_id')->filter()->unique();

        if (! $classroomId || $ids->isEmpty()) {
            return;
        }

        $count = StudentProfile::where('classroom_id', $classroomId)
            ->whereIn('id', $ids)
            ->count();

        if ($count !== $ids->count()) {
            $fail('One or more students do not belong to this classroom.');
        }
    }
}
