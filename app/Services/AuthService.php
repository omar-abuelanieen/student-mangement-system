<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $role = RoleEnum::from($data['role']);

            $user = User::create(Arr::except($data, ['role']));
            $user->assignRole($role->value);

            match ($role) {
                RoleEnum::Student  => $user->studentProfile()->create(),
                RoleEnum::Guardian => $user->guardianProfile()->create(),
                default            => null,
            };

            return [$user, $user->createToken('api')->plainTextToken];
        });
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        return [$user, $user->createToken('api')->plainTextToken];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }
    }
}
