<?php

namespace App\Rules;

use App\Models\Question;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QuestionsBelongToExam implements ValidationRule
{
    public function __construct(private int $examId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            return;
        }

        $ids = collect($value)->pluck('question_id')->filter()->unique();

        if ($ids->isEmpty()) {
            return;
        }

        $count = Question::where('exam_id', $this->examId)->whereIn('id', $ids)->count();

        if ($count !== $ids->count()) {
            $fail('One or more questions do not belong to this exam.');
        }
    }
}
