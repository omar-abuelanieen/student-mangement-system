<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public bool $reveal = false;

    public function revealAnswers(bool $value = true): static
    {
        $this->reveal = $value;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'subject'         => new SubjectResource($this->whenLoaded('subject')),
            'classroom'       => new ClassroomResource($this->whenLoaded('classroom')),
            'teacher'         => new TeacherResource($this->whenLoaded('teacher')),
            'questions_count' => $this->whenCounted('questions'),
            'questions'       => $this->whenLoaded('questions', fn () => $this->questions
                ->map(fn ($question) => (new QuestionResource($question))->revealAnswers($this->reveal))
                ->all()),
        ];
    }
}
