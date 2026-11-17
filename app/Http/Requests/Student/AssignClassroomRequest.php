<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classroom_id' => [
                'required', 'integer',
                Rule::exists('classrooms', 'id')->whereNull('deleted_at'),
            ],
        ];
    }
}
