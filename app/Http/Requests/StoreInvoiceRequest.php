<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحية على مستوى الـ route (role:admin)
    }

    public function rules(): array
    {
        return [
            'student_profile_id'  => ['required', 'integer', 'exists:student_profiles,id'],
            'guardian_profile_id' => [
                'required',
                'integer',
                Rule::exists('guardian_student', 'guardian_profile_id')
                    ->where('student_profile_id', $this->input('student_profile_id')),
            ],
            'title'    => ['required', 'string', 'max:255'],
            'amount'   => ['required', 'decimal:0,2', 'min:1', 'max:1000000'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'guardian_profile_id.exists' => 'This guardian is not linked to the selected student.',
        ];
    }
}
