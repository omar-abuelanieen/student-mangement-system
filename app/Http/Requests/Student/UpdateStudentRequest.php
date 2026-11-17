<?php

namespace App\Http\Requests\Student;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
        'email'=> [
            'sometimes', 'email',
            Rule::unique('users', 'email')->Ignore($this->route('student')->user_id),
        ],     'password' => ['sometimes', 'required', 'string', 'min:8'],
            'classroom_id' => ['nullable', 'integer', 'exists:classrooms,id'],
        ];
    }
}
