<?php

namespace App\Http\Requests\Exam;

use App\Enums\QuestionTypeEnum;
use App\Rules\TeacherTeachesSubjectInClassroom;
use App\Rules\ValidExamQuestions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classroom_id' => ['bail', 'required', 'integer', Rule::exists('classrooms', 'id')->whereNull('deleted_at')],
            'subject_id'   => [
                'bail', 'required', 'integer',
                Rule::exists('subjects', 'id')->whereNull('deleted_at'),
                new TeacherTeachesSubjectInClassroom(),
            ],
            'title'        => ['required', 'string', 'max:255'],

            'questions'                  => ['bail', 'required', 'array', 'min:1', 'max:100', new ValidExamQuestions()],
            'questions.*.body'           => ['required', 'string'],
            'questions.*.type'           => ['required', Rule::enum(QuestionTypeEnum::class)],
            'questions.*.options'        => ['nullable', 'array'],
            'questions.*.options.*'      => ['string', 'max:255'],
            'questions.*.correct_answer' => ['required', 'string'],
            'questions.*.points'         => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
