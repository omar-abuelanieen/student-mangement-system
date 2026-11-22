<?php

namespace App\Rules;

use App\Models\ScheduleEntry;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TeacherTeachesClassroom implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $userId = request()->user()->id;

        $teaches = ScheduleEntry::where('classroom_id', $value)
            ->whereHas('teacher', fn ($query) => $query->where('user_id', $userId))
            ->exists();

        if (! $teaches) {
            $fail('You are not assigned to this classroom.');
        }
    }
}
