<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'id'             => $this->id,
            'body'           => $this->body,
            'type'           => $this->type->value,
            'options'        => $this->options,
            'points'         => $this->points,
            'correct_answer' => $this->when($this->reveal, $this->correct_answer),
        ];
    }
}
