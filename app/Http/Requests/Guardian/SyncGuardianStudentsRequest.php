<?php

namespace App\Http\Requests\Guardian;

use App\Enums\RelationshipEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncGuardianStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'students'                       => ['present', 'array'],
            'students.*.student_profile_id'  => [
                'required', 'integer', 'distinct',
                Rule::exists('student_profiles', 'id')->whereNull('deleted_at'),
            ],
            'students.*.relationship'        => ['nullable', Rule::enum(RelationshipEnum::class)],
        ];
    }
}
