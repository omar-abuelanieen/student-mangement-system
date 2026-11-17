<?php

namespace App\Rules;

use App\Models\ScheduleEntry;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class NoScheduleOverlap implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    // في التعديل بنستثني الحصة نفسها علشان متتعارضش مع نفسها
    public function __construct(private ?int $ignoreId = null) {}

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    // الـ Rule دي بتتطبق على end_time
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $classroomId = $this->data['classroom_id'] ?? null;
        $teacherId   = $this->data['teacher_profile_id'] ?? null;
        $day         = $this->data['day'] ?? null;
        $start       = $this->data['start_time'] ?? null;
        $end         = $value;

        // لو حاجة ناقصة، القواعد التانية هي اللي بترفض
        if (! $classroomId || ! $teacherId || ! $day || ! $start) {
            return;
        }

        $overlapping = fn () => ScheduleEntry::query()
            ->where('day', $day)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->when($this->ignoreId, fn ($query) => $query->where('id', '!=', $this->ignoreId));

        if ($overlapping()->where('classroom_id', $classroomId)->exists()) {
            $fail('This classroom already has a class during this time.');

            return;
        }

        if ($overlapping()->where('teacher_profile_id', $teacherId)->exists()) {
            $fail('This teacher already has a class during this time.');
        }
    }
}
