<?php

namespace App\Http\Requests\User;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = RoleEnum::tryFrom((string) $this->input('role'));

        // admin و manager ميتعملوش إلا بواسطة admin
        if (in_array($role, [RoleEnum::Admin, RoleEnum::Manager], true)) {
            return $this->user()->hasRole(RoleEnum::Admin->value);
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'role'       => ['required', Rule::enum(RoleEnum::class)],
        ];
    }
}
