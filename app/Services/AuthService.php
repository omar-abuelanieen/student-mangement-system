<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\GuardianProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $role = RoleEnum::from($data['role']);

        $user = DB::transaction(function () use ($data, $role) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'],
                'password'   => Hash::make($data['password']),
            ]);

            $user->assignRole($role->value);

            if ($role->value === 'guardian') {
                GuardianProfile::create([
                    'user_id' => $user->id,
                    // باقي حقول الـ profile من $data
                ]);
            }

            return $user;
        });

        return [$user, $user->createToken('api')->plainTextToken];
    }
    public function login(array $data): array
{
    $user = User::where('email', $data['email'])->first();

    if (! $user || ! Hash::check($data['password'], $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return [$user, $user->createToken('api')->plainTextToken];
}
}
