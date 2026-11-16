<?php

namespace App\Http\Requests\Classroom;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateClassroomRequest extends FormRequest
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
    $classroom = $this->route('classroom');
    $levelId = $this->input('level_id', $classroom->level_id);

    return [
        'level_id'    => ['sometimes', 'integer', 'exists:levels,id'],
        'section'     => [
            'sometimes', 'integer', 'min:1',
            Rule::unique('classrooms', 'section')
                ->where('level_id', $levelId)
                ->ignore($classroom->id),
        ],
        'capacity'    => ['sometimes', 'integer', 'min:1'],
        'custom_name' => ['nullable', 'string', 'max:255'],
    ];
}
}
