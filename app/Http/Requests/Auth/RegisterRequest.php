<?php

namespace App\Http\Requests\Auth;

use App\Enums\RoleEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
  public function authorize(): bool { return true; }

public function rules(): array
{
    return [
        'first_name' => ['required', 'string', 'max:255'],
        'last_name'  => ['required', 'string', 'max:255'],
        'email'      => ['required', 'email', 'unique:users,email'],
        'password'   => ['required', 'string', 'min:8', 'confirmed'],
        'role'       => ['required', Rule::enum(RoleEnum::class)
                            ->only([RoleEnum::Student, RoleEnum::Guardian])],
    ];
}
}
