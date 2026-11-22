<?php

namespace App\Http\Resources;

use App\Enums\SubmissionStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public bool $reveal = false;

    public function revealAnswers(bool $value = true): static
    {
        $this->reveal = $value;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $graded = $this->status === SubmissionStatusEnum::Graded;

        return [
            'id'           => $this->id,
            'status'       => $this->status->value,
            'score'        => $this->score,
            'submitted_at' => $this->submitted_at?->toDateTimeString(),
            'graded_at'    => $this->graded_at?->toDateTimeString(),
            'exam'         => new ExamResource($this->whenLoaded('exam')),
            'student'      => new StudentResource($this->whenLoaded('student')),
            'total_points' => $this->whenLoaded('answers', fn () => $this->answers->sum(fn ($answer) => $answer->question->points)),
            'answers'      => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($answer) => [
                'question_id'    => $answer->question_id,
                'question'       => $answer->question->body,
                'points'         => $answer->question->points,
                'answer'         => $answer->answer,
                'is_correct'     => $answer->is_correct,
                'explanation'    => $answer->explanation,
                // الإجابة الصح: بعد التصحيح للكل، أو دايمًا للمدرس والإدارة
                'correct_answer' => ($graded || $this->reveal) ? $answer->question->correct_answer : null,
            ])->all()),
        ];
    }
}
