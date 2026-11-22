<?php

namespace App\Http\Requests\Submission;

use App\Rules\QuestionsBelongToExam;
use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
    // الطالب لازم يكون من فصل الاختبار (ExamPolicy::submit)
    public function authorize(): bool
    {
        return $this->user()->can('submit', $this->route('exam'));
    }

    public function rules(): array
    {
        return [
            'answers'               => ['bail', 'required', 'array', 'min:1', new QuestionsBelongToExam($this->route('exam')->id)],
            'answers.*.question_id' => ['required', 'integer', 'distinct'],
            'answers.*.answer'      => ['nullable', 'string', 'max:5000'],
        ];
    }
}
