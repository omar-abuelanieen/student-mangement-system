<?php

namespace App\Rules;

use App\Enums\QuestionTypeEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidExamQuestions implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            return;
        }

        foreach (array_values($value) as $index => $question) {
            $number = $index + 1;

            if (! is_array($question)) {
                continue;
            }

            $type   = QuestionTypeEnum::tryFrom((string) ($question['type'] ?? ''));
            $answer = $question['correct_answer'] ?? null;

            if (! $type || ! is_string($answer)) {
                continue;
            }

            if ($type === QuestionTypeEnum::MultipleChoice) {
                $options = $question['options'] ?? null;

                if (! is_array($options) || count($options) < 2) {
                    $fail("Question {$number}: a multiple choice question needs at least 2 options.");
                    continue;
                }

                if (collect($options)->contains(fn ($option) => ! is_string($option))) {
                    continue;
                }

                if (count(array_unique($options)) !== count($options)) {
                    $fail("Question {$number}: options must be unique.");
                    continue;
                }

                if (! in_array($answer, $options, true)) {
                    $fail("Question {$number}: the correct answer must be one of the options.");
                }
            }

            if ($type === QuestionTypeEnum::TrueFalse && ! in_array(strtolower($answer), ['true', 'false'], true)) {
                $fail("Question {$number}: the correct answer must be true or false.");
            }
        }
    }
}
