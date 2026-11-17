<?php

namespace App\Rules;

use App\Models\TeacherProfile;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class TeacherTeachesSubject implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $teacherId = $this->data['teacher_profile_id'] ?? null;

        if (! $teacherId) {
            return;
        }

        $teachesSubject = TeacherProfile::whereKey($teacherId)
            ->whereHas('subjects', fn ($query) => $query->whereKey($value))
            ->exists();

        if (! $teachesSubject) {
            $fail('The selected teacher does not teach this subject.');
        }
    }
}
