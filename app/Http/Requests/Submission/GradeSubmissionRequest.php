<?php

namespace App\Http\Requests\Submission;

use App\Rules\GradesCoverAllQuestions;
use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('grade', $this->route('submission'));
    }

    public function rules(): array
    {
        return [
            'answers'               => ['bail', 'required', 'array', 'min:1', new GradesCoverAllQuestions($this->route('submission'))],
            'answers.*.question_id' => ['required', 'integer', 'distinct'],
            'answers.*.is_correct'  => ['required', 'boolean'],
            'answers.*.explanation' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
