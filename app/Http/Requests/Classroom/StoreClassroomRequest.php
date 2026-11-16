<?php

namespace App\Http\Requests\Classroom;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassroomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'level_id'    => ['required', 'integer', 'exists:levels,id'],
        'section'     => [
            'required', 'integer', 'min:1',
            Rule::unique('classrooms', 'section')->where('level_id', $this->input('level_id')),
        ],
        'capacity'    => ['sometimes', 'integer', 'min:1'],
        'custom_name' => ['nullable', 'string', 'max:255'],
    ];
}
}
