<?php

namespace App\Rules;

use App\Enums\SubmissionStatusEnum;
use App\Models\ExamSubmission;
use App\Models\SubmissionAnswer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GradesCoverAllQuestions implements ValidationRule
{
    public function __construct(private ExamSubmission $submission) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            return;
        }

        $expected = SubmissionAnswer::where('exam_submission_id', $this->submission->id)
            ->pluck('question_id')->map(fn ($id) => (int) $id)->sort()->values()->all();

        $given = collect($value)->pluck('question_id')
            ->map(fn ($id) => (int) $id)->sort()->values()->all();

        // أول تصحيح: لازم كل الأسئلة
        if ($this->submission->status === SubmissionStatusEnum::Submitted) {
            if ($expected !== $given) {
                $fail('You must grade every question in this submission, once each.');
            }

            return;
        }

        // إعادة تصحيح: أي أسئلة، بشرط تكون تابعة للتسليم ده
        if (array_diff($given, $expected) !== []) {
            $fail('One or more questions do not belong to this submission.');
        }
    }
}
