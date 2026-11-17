<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function create(array $data, RoleEnum $role): User
    {
        return DB::transaction(function () use ($data, $role) {
            $user = User::create(Arr::except($data, ['role']));
            $user->assignRole($role->value);

            match ($role) {
                RoleEnum::Student  => $user->studentProfile()->create(),
                RoleEnum::Teacher  => $user->teacherProfile()->create(),
                RoleEnum::Guardian => $user->guardianProfile()->create(),
                default            => null,
            };

            return $user;
        });
    }
}
