<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            [
                'first_name' => 'Admin',
                'last_name'  => 'User',
                'password'   => 'password123',
            ]
        );

        $admin->assignRole(RoleEnum::Admin->value);
    }
}
