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
        $userData = $data;
        $userData['password'] = Hash::make($data['password']);
        $userData['role'] = RoleEnum::from($data['role'])->value;

        $user = User::create($userData);

        return [$user, $user->createToken('api')->plainTextToken];
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
